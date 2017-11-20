<?php

namespace App\Modules\Contacts\Controllers\Admin;

use Nova\Database\ORM\ModelNotFoundException;
use Nova\Http\Request;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Config;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Response;

use App\Modules\Contacts\Models\Contact;

use App\Modules\Platform\Controllers\Admin\BaseController;


class Contacts extends BaseController
{

    public function index()
    {
        $contacts = Contact::paginate(15);

        return $this->createView()
            ->shares('title', __d('contacts', 'Contacts'))
            ->with(compact('contacts'));
    }

    public function store(Request $request)
    {
        $name = $request->input('name');

        $contact = Contact::create(array(
            'name' => $name,
            'email' => $request->input('email', Config::get('app.email')),
            'description' => $request->input('description'),
            'path' => $request->input('path'),
        ));

        return Redirect::back()
            ->withStatus(__d('content', 'The Contact <b>{0}</b> was successfully created.', $name), 'success');
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

        $contact->save();

        return Redirect::back()
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
