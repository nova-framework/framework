<?php

namespace Modules\Contacts\Controllers\Admin;

use Nova\Auth\Access\AuthorizationException;
use Nova\Database\ORM\ModelNotFoundException;
use Nova\Http\Request;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Cache;
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

    protected function validator(array $data, $id = null)
    {
        $ignore = ! is_null($id) ? ',' .intval($id) : '';

        $rules = array(
            'contact_id' => 'required|numeric|exists:contacts,id' .$ignore,
            'title'      => 'required|min:3|max:255|valid_title',
            'order'      => 'required|numeric|min:-100|max:100',
        );

        $messages = array(
            'valid_title' => __d('contacts', 'The :attribute field is not a valid title.'),
        );

        $attributes = array(
            'contact_id' => __d('contacts', 'Contact ID'),
            'title'      => __d('contacts', 'Title'),
            'order'      => __d('contacts', 'Order'),
        );

        $validator = Validator::make($data, $rules, $messages, $attributes);

        // Add the custom Validation Rule commands.
        $validator->addExtension('valid_title', function($attribute, $value, $parameters)
        {
            $pattern = '~^(?:[\p{L}\p{Mn}\p{Pd}\'\x{2019}]+(?:$|\s+)){1,}$~u';

            return (preg_match($pattern, $value) === 1);
        });

        return $validator;
    }

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
        $input = $request->only('contact_id', 'title', 'order');

        // Validate the input data.
        $validator = $this->validator($input);

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

        // Invalidate the cached information.
        Cache::section('contacts')->flush();

        //
        $url = site_url('admin/contacts/{0}/field-groups', $contact->id);

        return Redirect::to($url)
            ->with('success', __d('contacts', 'The Field Group <b>{0}</b> was successfully created.', $title));
    }

    public function update(Request $request, $contactId, $id)
    {
        try {
            $contact = Contact::findOrFail($contactId);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::to('admin/contacts')->with('danger', __d('contacts', 'Contact not found: #{0}', $contactId));
        }

        try {
            $group = FieldGroup::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            $url = site_url('admin/contacts/{0}/field-groups', $contact->id);

            return Redirect::to($url)->with('danger', __d('contacts', 'Field Group not found: #{0}', $id));
        }

        /*
        // Authorize the current User for deleting this Field Group instance.
        if (Gate::denies('update', $group)) {
            throw new AuthorizationException();
        }
        */

        $input = $request->only('contact_id', 'title', 'order');

        // Validate the input data.
        $validator = $this->validator($input);

        if ($validator->fails()) {
            return Redirect::back()->onlyInput('title', 'order')->withErrors($validator);
        }

        $title = $group->title;

        $group->title = $input['title'];
        $group->order = $input['order'];

        $group->updated_by = Auth::id();

        $group->save();

        // Invalidate the cached information.
        Cache::section('contacts')->flush();

        //
        $url = site_url('admin/contacts/{0}/field-groups', $contact->id);

        return Redirect::to($url)
            ->with('success', __d('contacts', 'The Field Group <b>{0}</b> was successfully updated.', $title));
    }

    public function destroy(Request $request, $contactId, $id)
    {
        try {
            $contact = Contact::findOrFail($contactId);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::to('admin/contacts')->with('danger', __d('contacts', 'Contact not found: #{0}', $contactId));
        }

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

        // Invalidate the cached information.
        Cache::section('contacts')->flush();
        s
        //
        $url = site_url('admin/contacts/{0}/field-groups', $contact->id);

        return Redirect::to($url)->with('success', __d('contacts', 'The Field Group was successfully deleted.'));
    }
}
