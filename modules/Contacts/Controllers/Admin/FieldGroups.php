<?php

namespace Modules\Contacts\Controllers\Admin;

use Nova\Auth\Access\AuthorizationException;
use Nova\Database\ORM\ModelNotFoundException;
use Nova\Http\Request;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Gate;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Response;
use Nova\Support\Facades\Validator;
use Nova\Support\Facades\View;

use Modules\Contacts\Models\Contact;
use Modules\Contacts\Models\FieldGroup;
use Modules\Platform\Controllers\Admin\BaseController;


class FieldGroups extends BaseController
{

    public function index($id)
    {
        try {
            $contact = Contact::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::to('admin/contacts')->with('danger', __d('contacts', 'Contact not found: #{0}', $id));
        }

        /*
        // Authorize the current User.
        if (Gate::denies('lists', FieldGroup::class)) {
            throw new AuthorizationException();
        }
        */

        $contacts = Contact::paginate(15);

        return $this->createView()
            ->shares('title', __d('contacts', 'Manage the Field Groups : {0}', $contact->name))
            ->with(compact('contact'));
    }

    public function store(Request $request, $id)
    {
        $rules = array(
            'contact_id' => 'required|numeric|exists:contacts,id',
            'title'      => 'required|min:3|max:255|valid_title',
            'order'      => 'required|numeric|min:-100|max:100',
        );

        $messages = array(
            'valid_title' => __d('contacts', 'The :attribute field is not a valid title.'),
        );

        $attributes = array(
            'title' => __d('contacts', 'Title'),
            'order' => __d('contacts', 'Order'),
        );

        // Validate the Input data.
        $input = $request->only('contact_id', 'title', 'order');

        $validator = Validator::make($input, $rules, array(), $attributes);

        // Add the custom Validation Rule commands.
        $validator->addExtension('valid_title', function($attribute, $value, $parameters)
        {
            $pattern = '~^(?:[\p{L}\p{Mn}\p{Pd}\'\x{2019}]+(?:$|\s+)){1,}$~u';

            return (preg_match($pattern, $value) === 1);
        });

        if ($validator->fails()) {
            return Redirect::back()->onlyInput('title', 'order')->withErrors($validator);
        }

        try {
            $contact = Contact::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::to('admin/contacts')->with('danger', __d('contacts', 'Contact not found: #{0}', $id));
        }

        /*
        // Authorize the current User.
        if (Gate::denies('create', FieldGroup::class)) {
            throw new AuthorizationException();
        }
        */

        $title = $input['title'];

        $group = FieldGroup::create(array(
            'title'      => $title,
            'order'      => $input['order'],
            'contact_id' => $contact->id,

            //
            'created_by' => Auth::id(),
        ));

        $url = site_url('admin/contacts/{0}/field-groups', $contact->id);

        return Redirect::to($url)
            ->with('success', __d('contacts', 'The Field Group <b>{0}</b> was successfully created.', $title));
    }

    public function destroy(Request $request, $contactId, $id)
    {
        try {
            $contact = Contact::findOrFail($contactId);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::to('admin/contacts')->with('danger', __d('contacts', 'Contact not found: #{0}', $contactId));
        }

        /*
        // Authorize the current User for updating this Contact instance.
        if (Gate::denies('update', $contact)) {
            throw new AuthorizationException();
        }
        */

        try {
            $group = FieldGroup::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::to('admin/contacts')->with('danger', __d('contacts', 'Field Group not found: #{0}', $id));
        }

        /*
        // Authorize the current User for deleting this Field Group instance.
        if (Gate::denies('delete', $group)) {
            throw new AuthorizationException();
        }
        */

        // Destroy the requested Field Group record.
        $group->delete();

        //
        $url = site_url('admin/contacts/{0}/field-groups', $contact->id);

        return Redirect::to($url)->with('success', __d('contacts', 'The Field Group was successfully deleted.'));
    }
}
