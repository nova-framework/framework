<?php

namespace Modules\Contacts\Controllers\Admin;

use Nova\Database\ORM\ModelNotFoundException;
use Nova\Http\Request;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Validator;

use Modules\Contacts\Models\Contact;
use Modules\Contacts\Models\Message;
use Modules\Platform\Controllers\Admin\BaseController;

use ErrorException;


class Messages extends BaseController
{

    public function index($id)
    {
        try {
            $contact = Contact::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->with('danger', __d('contacts', 'Contact not found: #{0}', $id));
        }

        $messages = $contact->messages()->orderBy('created_at', 'DESC')->paginate(15);

        return $this->createView()
            ->shares('title', __d('contacts', 'Messages received by : {0}', $contact->name))
            ->with(compact('contact', 'messages'))
            ->with('search', '')
            ->with('searching', false);
    }

    public function show($contactId, $id)
    {
        try {
            $contact = Contact::findOrFail($contactId);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->with('danger', __d('contacts', 'Contact not found: #{0}', $contactId));
        }

        try {
            $message = Message::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::to('admin/contacts/' .$contact->id .'/messages')
                ->with('danger', __d('contacts', 'Message not found: #{0}', $id));
        }

        return $this->createView()
            ->shares('title', __d('contacts', 'Show Message'))
            ->with(compact('contact', 'message'));
    }

    public function destroy($contactId, $id)
    {
        try {
            $contact = Contact::findOrFail($contactId);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->with('danger', __d('contacts', 'Contact not found: #{0}', $contactId));
        }

        try {
            $message = Message::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::to('admin/contacts/' .$contact->id .'/messages')
                ->with('danger', __d('contacts', 'Message not found: #{0}', $id));
        }

        // Delete the Message (and its attachments)
        $message->delete();

        // Update the Contact's messages count.
        $contact->updateCount();

        return Redirect::to('admin/contacts/' .$contact->id .'/messages')
            ->with('success', __d('contacts', 'The Message was successfully deleted.'));
    }

    public function search(Request $request, $id)
    {
        $rules = array(
            'query' => 'required|valid_query'
        );

        $attributes = array(
            'query' => __d('courses', 'Search Query'),
        );

        // Validate the Input data.
        $input = $request->only('query');

        $validator = Validator::make($input, $rules, array(), $attributes);

        // Add the custom Validation Rule commands.
        $validator->addExtension('valid_query', function($attribute, $value, $parameters)
        {
            $pattern = '~^(?:[\p{L}\p{Mn}\p{Pd}\'\x{2019}]+(?:$|\s+)){1,}$~u';

            return (preg_match($pattern, $value) === 1);
        });

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator);
        }

        try {
            $contact = Contact::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->with('danger', __d('contacts', 'Contact not found: #{0}', $id));
        }

        // Search the Messages on Database.
        $search = $input['query'];

        $messages = $contact->messages()->where(function ($query) use ($search)
            {
                $query->where('author', 'LIKE', '%' .$search .'%')
                    ->orWhere('author_email', 'LIKE', '%' .$search .'%')
                    ->orWhere('subject', 'LIKE', '%' .$search .'%')
                    ->orWhere('content', 'LIKE', '%' .$search .'%');
            })
            ->orderBy('created_at', 'DESC')
            ->paginate(15);

        return $this->createView(compact('contact', 'messages', 'search'), 'Index')
            ->shares('title', __d('contacts', 'Messages received by : {0}', $contact->name))
            ->with('searching', true);
    }
}
