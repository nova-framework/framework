<?php

namespace App\Modules\Contacts\Controllers\Admin;

use Nova\Support\Facades\Redirect;

use App\Modules\Contacts\Models\Contact;
use App\Modules\Contacts\Models\Message;

use App\Modules\Platform\Controllers\Admin\BaseController;


class Messages extends BaseController
{

    public function index($id)
    {
        try {
            $contact = Contact::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->withStatus(__d('content', 'Contact not found: #{0}', $id), 'danger');
        }

        $messages = $contact->messages()->orderBy('created_at', 'DESC')->paginate(15);

        return $this->createView()
            ->shares('title', __d('contacts', 'Messages received by : {0}', $contact->name))
            ->with(compact('contact', 'messages'));
    }

    public function destroy($contactId, $id)
    {
        try {
            $message = Message::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->withStatus(__d('content', 'Message not found: #{0}', $id), 'danger');
        }

        $contact = $message->contact()->first();

        // Delete the Message.
        $message->delete();

        // Update the Contact's messages count.
        $contact->updateCount();

        return Redirect::back()
            ->withStatus(__d('content', 'The Message was successfully deleted.'), 'success');
    }
}
