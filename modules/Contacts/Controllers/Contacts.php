<?php

namespace Modules\Contacts\Controllers;

use Nova\Http\Request;
use Nova\Routing\Controller as BaseController;
use Nova\Support\Facades\App;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Validator;

use Modules\Contacts\Models\Contact;
use Modules\Contacts\Models\Message;
use Modules\Contacts\Notifications\MessageSubmitted as MessageSubmittedNotification;
use Modules\Users\Models\User;

use LogicException;


class Contacts extends BaseController
{

    protected function validator(array $data)
    {
        $rules = array(
            'contact_author'        => 'required',
            'contact_author_email'  => 'required|email',
            'contact_author_url'    => 'sometimes|required|url',
            'contact_content'       => 'required'
        );

        $attributes = array(
            'contact_author'       => __d('content', 'Name'),
            'contact_author_email' => __d('content', 'Email Address'),
            'contact_author_url'   => __d('content', 'Website'),
            'contact_content'      => __d('content', 'Message'),
        );

        return Validator::make($data, $rules, array(), $attributes);
    }

    public function store(Request $request)
    {
        $input = $request->only(
            'contact_author', 'contact_author_email', 'contact_author_url', 'contact_content'
        );

        if (empty($input['contact_author_url'])) {
            unset($input['contact_author_url']);
        }

        // Verify the submitted reCAPTCHA
        if (! Auth::check() && ! ReCaptcha::check($request->input('g-recaptcha-response'), $request->ip())) {
            return Redirect::back()->withInput($input)->with('danger', __d('contacts', 'The reCaptcha verification failed.'));
        }

        $path = $request->input('path');

        if (is_null($contact = Contact::findByPath($path))) {
            throw new LogicException('Contact not found.');
        }

        $validator = $this->validator($input);

        if ($validator->fails()) {
            return Redirect::back()->withInput($input)->withErrors($validator);
        }

        $userId = Auth::id() ?: 0;

        $message = Message::create(array(
            'contact_id'   => $contact->id,
            'author'       => $input['contact_author'],
            'author_email' => $input['contact_author_email'],
            'author_url'   => $input['contact_author_url'],
            'author_ip'    => $request->ip(),
            'content'      => $input['contact_content'],
            'user_id'      => $userId,
            'path'         => $path,
        ));

        // Update the Contact's messages count.
        $contact->updateCount();

        // Notify the associated User, if exists.
        $user = User::where('email', $contact->email)->first();

        if (! is_null($user)) {
            $user->notify(new MessageSubmittedNotification($message, $contact, $elements));
        }

        return Redirect::back()->with('success', __d('contacts', 'Your message was successfully sent.'));
    }
}
