<?php

namespace Modules\Contacts\Controllers;

use Nova\Http\Request;
use Nova\Routing\Controller as BaseController;
use Nova\Support\Facades\App;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Validator;
use Nova\Support\Arr;
use Nova\Support\Str;

use Shared\Support\Facades\PDF;
use Shared\Support\ReCaptcha;

use Modules\Contacts\Models\Attachment;
use Modules\Contacts\Models\Contact;
use Modules\Contacts\Models\Message;
use Modules\Contacts\Notifications\MessageSubmitted as MessageSubmittedNotification;
use Modules\Users\Models\User;

use LogicException;


class Messages extends BaseController
{

    protected function validator(array $data, $remoteIp)
    {
        $rules = array(
            'contact_author'        => 'required|min:3|max:100',
            'contact_author_email'  => 'required|min:3|max:100|email',
            'contact_subject'       => 'required|min:3|max:100|valid_text',
            'contact_content'       => 'required|min:3|max:1000|valid_text',
            'contact_attachment'    => 'array|max:5',
            'g-recaptcha-response'  => 'required|recaptcha'
        );

        $messages = array(
            'recaptcha'  => __d('contacts', 'The reCaptcha verification failed.'),
            'valid_text' => __d('contacts', 'The :attribute field cannot contain HTML tags.'),
        );

        $attributes = array(
            'contact_author'       => __d('contacts', 'Name'),
            'contact_author_email' => __d('contacts', 'Email Address'),
            'contact_subject'      => __d('contacts', 'Subject'),
            'contact_content'      => __d('contacts', 'Message'),
            'contact_attachment'   => __d('contacts', 'Attachments'),
            'g-recaptcha-response' => __d('contacts', 'ReCaptcha'),
        );

        // Prepare the dynamic rules and attributes for attachments.
        if (! empty($files = Arr::get($data, 'contact_attachment', array()))) {
            $max = count($files) - 1;

            foreach(range(0, $max) as $index) {
                $key = 'contact_attachment.' .$index;

                //
                $rules[$key] = 'max:10240|mimes:zip,rar,pdf,png,jpg,jpeg,doc,docx';

                $attributes[$key] = __d('contacts', 'Attachment');
            }
        }

        $validator = Validator::make($data, $rules, $messages, $attributes);

        // Add the custom Validation Rule commands.
        $validator->addExtension('recaptcha', function($attribute, $value, $parameters) use ($remoteIp)
        {
            return ReCaptcha::check($value, $remoteIp);
        });

        $validator->addExtension('valid_text', function($attribute, $value, $parameters)
        {
            return strip_tags($value) == $value;
        });

        return $validator;
    }

    public function store(Request $request)
    {
        $input = $request->all();

        if (isset($input['contact_author_url']) && empty($input['contact_author_url'])) {
            unset($input['contact_author_url']);
        }

        $path = $request->input('path');

        if (is_null($contact = Contact::findByPath($path))) {
            throw new LogicException('Contact not found.');
        }

        $validator = $this->validator($input, $request->ip());

        if ($validator->fails()) {
            $errors = array();

            // There we will store the attachment(s) messages.
            $messages = array();

            foreach ($validator->messages()->getMessages() as $key => $value) {
                if (! Str::startsWith($key, 'contact_attachment.')) {
                    $errors[$key] = $value;
                } else {
                    $messages = array_unique(array_merge($value, $messages));
                }
            }

            if (! empty($messages)) {
                $messages = array_merge($messages, Arr::get($errors, 'contact_attachment', array()));

                $errors['contact_attachment'] = $messages;
            }

            return Redirect::back()
                ->onlyInput('contact_author', 'contact_author_email', 'contact_author_url', 'contact_subject', 'contact_content')
                ->withErrors($errors);
        }

        $userId = Auth::id() ?: 0;

        $message = Message::create(array(
            'contact_id'   => $contact->id,
            'author'       => $input['contact_author'],
            'author_email' => $input['contact_author_email'],
            'author_ip'    => $request->ip(),
            'subject'      => $input['contact_subject'],
            'content'      => $input['contact_content'],
            'user_id'      => $userId,
            'path'         => $path,
        ));

        if ($request->hasFile('contact_attachment')) {
            $files = $request->file('contact_attachment');

            foreach ($files as $file) {
                $attachment = Attachment::uploadFileAndCreate($file);

                $message->attachments()->save($attachment);
            }
        }

        // Update the Contact's messages count.
        $contact->updateCount();

        // Notify the associated User, if exists.
        $user = User::where('email', $contact->email)->first();

        if (! is_null($user)) {
            $user->notify(new MessageSubmittedNotification($message, $contact, $this->createPdf($message)));
        }

        return Redirect::back()->with('success', __d('contacts', 'Your message was successfully sent.'));
    }

    protected function createPdf(Message $message)
    {
        return PDF::loadView('Modules/Contacts::PDF/Message', compact('message'))->output();
    }
}
