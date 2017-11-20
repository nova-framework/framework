<?php

namespace App\Modules\Contacts\Controllers;

use Nova\Http\Request;
use Nova\Routing\Controller as BaseController;
use Nova\Support\Facades\App;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Validator;

use App\Modules\Contacts\Models\Contact;
use App\Modules\Contacts\Models\Message;
use App\Modules\Contacts\Notifications\MessageSubmitted as MessageSubmittedNotification;
use App\Modules\Users\Models\User;


class Contacts extends BaseController
{

    protected function validator(array $data)
    {
        $rules = array(
            'author'        => 'required',
            'author_email'  => 'required|email',
            'content'       => 'required'
        );

        $attributes = array(
            'author'       => __d('content', 'Name'),
            'author_email' => __d('content', 'Email Address'),
            'content'      => __d('content', 'Message'),
        );

        return Validator::make($data, $rules, array(), $attributes);
    }

    public function store(Request $request)
    {
        $input = $request->all();

        // Verify the submitted reCAPTCHA
        if (! Auth::check() && ! ReCaptcha::check($request->input('g-recaptcha-response'), $request->ip())) {
            return Redirect::back()->withInput($input)->withStatus(__d('content', 'The reCaptcha verification failed.'), 'danger');
        }

        $path = $request->input('path');

        if (is_null($contact = Contact::findByPath($path))) {
            App::abort(500);
        }

        $validator = $this->validator($input);

        if ($validator->fails()) {
            return Redirect::back()->withInput($input)->withErrors($validator);
        }

        $message = Message::create(array(
            'author_id'      => 1,
            'content'        => $input['content'],
            'title'          => null,
            'parent_id'      => $contact->id,
            'excerpt'        => null,
            'status'         => 'publish',
            'menu_order'     => 0,
            'type'           => 'contact_message',
            'comment_status' => 'closed',
        ));

        $message->name = $id = $message->id;

        $message->guid = site_url('content/' .$id);

        // Handle the Metadata.
        $message->meta->contact_author       = $input['author'];
        $message->meta->contact_author_email = $input['author_email'];
        $message->meta->contact_author_ip    = $request->ip();
        $message->meta->contact_path         = $path;

        $message->save();

        // Notify the associated User, if exists.
        $user = User::where('email', $contact->email)->first();

        if (! is_null($user)) {
            $user->notify(new MessageSubmittedNotification($message, $contact));
        }

        return Redirect::back()
            ->withStatus(__d('content', 'Your message was successfully sent.'), 'success');
    }
}
