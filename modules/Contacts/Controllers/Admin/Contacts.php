<?php

namespace Modules\Contacts\Controllers\Admin;

use Nova\Database\ORM\ModelNotFoundException;
use Nova\Http\Request;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Config;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Response;
use Nova\Support\Facades\View;
use Nova\Support\Str;

use Modules\Contacts\Models\Contact;
use Modules\Platform\Controllers\Admin\BaseController;


class Contacts extends BaseController
{

    public function index()
    {
        $contacts = Contact::paginate(15);

        return $this->createView()
            ->shares('title', __d('contacts', 'Contacts'))
            ->with(compact('contacts'));
    }

    public function create()
    {
        return $this->createView()
            ->shares('title', __d('contacts', 'Create a new Contact'));
    }

    public function store(Request $request)
    {
        $name = $request->input('name');

        $contact = Contact::create(array(
            'name'        => $name,
            'email'       => $request->input('email', Config::get('app.email')),
            'description' => $request->input('description'),
            'path'        => $request->input('path'),
            'message'     => $request->input('message'),
        ));

        return Redirect::to('admin/contacts')
            ->withStatus(__d('content', 'The Contact <b>{0}</b> was successfully created.', $name), 'success');
    }

    public function show($id)
    {
        try {
            $contact = Contact::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->withStatus(__d('content', 'Contact not found: #{0}', $id), 'danger');
        }

        return $this->createView()
            ->shares('title', __d('contacts', 'Show Contact'))
            ->with(compact('contact'));
    }

    public function edit($id)
    {
        try {
            $contact = Contact::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->withStatus(__d('content', 'Contact not found: #{0}', $id), 'danger');
        }

        return $this->createView()
            ->shares('title', __d('contacts', 'Edit a Contact'))
            ->with(compact('contact'));
    }

    public function update(Request $request, $id)
    {
        try {
            $contact = Contact::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->withStatus(__d('content', 'Contact not found: #{0}', $id), 'danger');
        }

        $name = $contact->name;

        // Update the Contact.
        $contact->name        = $request->input('name');
        $contact->email       = $request->input('email', Config::get('app.email'));
        $contact->description = $request->input('description');
        $contact->path        = $request->input('path');
        $contact->message     = $request->input('message');

        $contact->save();

        return Redirect::to('admin/contacts')
            ->withStatus(__d('content', 'The Contact <b>{0}</b> was successfully updated.', $name), 'success');
    }

    public function destroy($id)
    {
        try {
            $contact = Contact::with('messages')->findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->withStatus(__d('content', 'Contact not found: #{0}', $id), 'danger');
        }

        $contact->messages()->delete();

        $contact->delete();

        return Redirect::back()
            ->withStatus(__d('content', 'The Contact was successfully deleted.'), 'success');
    }
}
