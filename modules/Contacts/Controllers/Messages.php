<?php

namespace Modules\Contacts\Controllers;

use Nova\Auth\Access\AuthorizationException;
use Nova\Http\Request;
use Nova\Routing\Controller as BaseController;
use Nova\Support\Facades\App;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Gate;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Validator;
use Nova\Support\Arr;
use Nova\Support\Str;

use Shared\Support\Facades\PDF;
use Shared\Support\ReCaptcha;

use Modules\Contacts\Models\Attachment;
use Modules\Contacts\Models\Contact;
use Modules\Contacts\Models\CustomField;
use Modules\Contacts\Models\Message;
use Modules\Contacts\Notifications\MessageSubmitted as MessageSubmittedNotification;
use Modules\Users\Models\User;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use LogicException;


class Messages extends BaseController
{

    protected function validator(Contact $contact, Request $request)
    {
        $rules = array(
            'g-recaptcha-response'  => 'required|recaptcha'
        );

        $messages = array(
            'recaptcha'  => __d('contacts', 'The reCaptcha verification failed.'),
            'valid_name' => __d('contacts', 'The :attribute field is not a valid name.'),
            'valid_text' => __d('contacts', 'The :attribute field cannot contain HTML tags.'),
        );

        $attributes = array(
            'g-recaptcha-response' => __d('contacts', 'ReCaptcha'),
        );

        $data = $request->all();

        // Prepare the dynamic rules and attributes for Field Items.
        foreach ($contact->fieldItems as $item) {
            if (empty($rule = $item->rule)) {
                continue;
            }

            $key = 'contact_' .str_replace('-', '_', $item->name);

            if (isset($input[$key]) && empty($input[$key]) && Str::contains($rule, 'sometimes')) {
                unset($data[$key]);
            }

            if ($item->type == 'checkbox') {
                $options = $item->options ?: array();

                $choices = explode("\n", trim(array_get($item->options, 'choices')));

                if (count ($choices) > 1) {
                    $max = count($choices) - 1;

                    foreach (range(0, $max) as $index) {
                        $name = $key .'.' .$index;

                        //
                        $rules[$name] = $rule;

                        $attributes[$name] = $item->title;
                    }

                    $rule = Str::contains($rule, 'required') ? 'required|array' : 'array';
                }
            }

            $rules[$key] = $rule;

            $attributes[$key] = $item->title;
        }

        $validator = Validator::make($data, $rules, $messages, $attributes);

        // Add the custom Validation Rule commands.
        $validator->addExtension('recaptcha', function($attribute, $value, $parameters) use ($request)
        {
            return ReCaptcha::check($value, $request->ip());
        });

        $validator->addExtension('valid_name', function($attribute, $value, $parameters)
        {
            $pattern = '~^(?:[\p{L}\p{Mn}\p{Pd}\'\x{2019}]+(?:$|\s+)){1,}$~u';

            return (preg_match($pattern, $value) === 1);
        });

        $validator->addExtension('valid_text', function($attribute, $value, $parameters)
        {
            return strip_tags($value) == $value;
        });

        return $validator;
    }

    public function store(Request $request)
    {
        // Authorize the current User, if it is authenticated.
        if (Auth::check() && Gate::denies('create', Message::class)) {
            throw new AuthorizationException();
        }

        $input = $request->all();

        $path = $request->input('path');

        if (is_null($contact = Contact::findByPath($path))) {
            throw new LogicException('Contact not found.');
        }

        $validator = $this->validator($contact, $request);

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }

        $userId = Auth::id() ?: 0;

        $message = Message::create(array(
            'path'       => $path,
            'remote_ip'  => $request->ip(),
            'user_id'    => $userId,

            // Resolve the relationships.
            'contact_id' => $contact->id,
        ));

        foreach ($contact->fieldItems as $item) {
            $name = 'contact_' .str_replace('-', '_', $item->name);

            if ($item->type == 'file') {
                if (! $request->hasFile($name)) {
                    continue;
                }

                $file = $request->file($name);

                $attachment = Attachment::uploadFileAndCreate($file, $message);

                $message->attachments()->save($attachment);

                // We will store in the custom field the attachment's ID.
                $value = $attachment->id;
            } else {
                $value = $request->input($name);
            }

            $field = CustomField::create(array(
                'name'  => $item->name,
                'type'  => $item->type,
                'value' => $value,

                // Resolve the relationships.
                'field_item_id' => $item->id,
                'message_id'    => $message->id,
            ));
        }

        // Update the Contact's messages count.
        $contact->updateCount();

        // Notify the associated User, if exists.
        $user = User::where('email', $contact->email)->first();

        if (! is_null($user)) {
            $user->notify(new MessageSubmittedNotification($message, $contact, $this->createPdf($message, $contact)));
        }

        return Redirect::back()->with('success', __d('contacts', 'Your message was successfully sent.'));
    }

    protected function createPdf(Message $message, Contact $contact)
    {
        return PDF::loadView('Modules/Contacts::PDF/Message', compact('contact', 'message'))->output();
    }
}
