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
use Nova\Support\Arr;

use Modules\Contacts\Models\Contact;
use Modules\Contacts\Models\FieldGroup;
use Modules\Contacts\Models\FieldItem;
use Modules\Platform\Controllers\Admin\BaseController;


class FieldItems extends BaseController
{

    protected function validator(array $data, Contact $contact, $id = null)
    {
        $types = array('text', 'password', 'textarea', 'select', 'checkbox', 'radio', 'file');

        $ignore = ! is_null($id) ? ':' .intval($id) : '';

        // The Validation rules.
        $rules = array(
            'field_title' => 'required|min:4|max:40|valid_title',
            'field_name'  => 'required|min:4|max:40|alpha_dash|valid_name' .$ignore,
            'field_type'  => 'required|valid_type',
            'field_order' => 'required|numeric|min:-100|max:100',

            //
            'group_id'    => 'required|numeric|exists:contact_field_groups,id',
        );

        $messages = array(
            'valid_title' => __d('contacts', 'The :attribute field is not a valid title.'),
            'valid_name'  => __d('contacts', 'The :attribute field is not a valid name.'),
            'valid_type'  => __d('contacts', 'The :attribute field is not a valid type.'),
        );

        $attributes = array(
            'field_title' => __d('contacts', 'Label'),
            'field_name'  => __d('contacts', 'Name'),
            'field_type'  => __d('contacts', 'Type'),
            'field_order' => __d('contacts', 'Order'),

            //
            'group_id'    => __d('contacts', 'Field Group ID'),
        );

        $validator = Validator::make($data, $rules, $messages, $attributes);

        // Add the custom Validation Rule commands.
        $validator->addExtension('valid_title', function($attribute, $value, $parameters)
        {
            $pattern = '~^(?:[\p{L}\p{Mn}\p{Pd}\'\x{2019}]+(?:$|\s+)){1,}$~u';

            return (preg_match($pattern, $value) === 1);
        });

        $validator->addExtension('valid_type', function($attribute, $value, $parameters) use ($types)
        {
            return in_array($value, $types);
        });

        $validator->addExtension('valid_name', function($attribute, $value, $parameters) use ($contact)
        {
            $table = with(new FieldItem)->getTable();

            $query = $contact->fieldItems()->where($table .'.name', $value);

            if (! empty($parameters) && is_numeric($id = head($parameters))) {
                $query->where($table .'.id', '!=', (int) $id);
            }

            return ! $query->exists();
        });

        return $validator;
    }

    public function create($id)
    {
        /*
        // Authorize the current User.
        if (Gate::denies('create', FieldItem::class)) {
            throw new AuthorizationException();
        }
        */

        try {
            $group = FieldGroup::with('contact')->findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::to('admin/contacts')->with('danger', __d('contacts', 'Field Group not found: #{0}', $id));
        }

        return $this->createView()
            ->shares('title', __d('contacts', 'Create Field Item'))
            ->with('contact', $group->contact)
            ->with('group', $group);
    }

    public function store(Request $request, $id)
    {
        /*
        // Authorize the current User.
        if (Gate::denies('create', FieldItem::class)) {
            throw new AuthorizationException();
        }
        */

        try {
            $group = FieldGroup::with('contact')->findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::to('admin/contacts')->with('danger', __d('contacts', 'Field Group not found: #{0}', $id));
        }

        $input = $request->only(
            'field_title', 'field_name', 'field_type', 'field_order', 'field_rules',
            'field_placeholder', 'field_default', 'field_choices', 'field_rows',
            'group_id'
        );

        // Validate the Input data.
        $validator = $this->validator($input, $group->contact);

        if ($validator->fails()) {
            return Redirect::back()->withInput($input)->withErrors($validator->errors());
        }

        $type = $input['field_type'];

        // Handle the Text inputs.
        if ($type == 'text') {
            $options = array(
                'placeholder' => $input['field_placeholder'],
                'default'     => $input['field_default'],
            );
        }

        // Handle the Password inputs.
        else if ($type == 'password') {
            $options = array(
                'placeholder' => $input['field_placeholder'],
            );
        }

        // Handle the Textarea fields.
        else if ($type == 'textarea') {
            $options = array(
                'placeholder' => $input['field_placeholder'],
                'rows'        => $input['field_rows'],
            );
        }

        // Handle the Select fields.
        else if ($type == 'select') {
            $options = array(
                'placeholder' => $input['field_placeholder'],
                'default'     => $input['field_default'],
                'choices'     => $input['field_choices']
            );
        }

        // Handle the Checkbox and Radio buttons.
        else if (($type == 'checkbox') || ($type == 'radio')) {
            $options = array(
                'choices' => $input['field_choices']
            );
        }

        // Handle the File uploads.
        else if ($type == 'file') {
            $options = array();
        }

        // Create a Field Item Model instance.
        $item = FieldItem::create(array(
            'title'   => $input['field_title'],
            'name'    => $input['field_name'],
            'type'    => $input['field_type'],
            'order'   => $input['field_order'],
            'rules'   => $input['field_rules'],
            'options' => $options,

            // Resolve the fieldGroup relationship.
            'field_group_id' => $group->id,
        ));

        // Invalidate the cached information.
        Cache::section('contacts')->flush();

        //
        $url = site_url('admin/contacts/{0}/field-groups', $group->contact->id);

        return Redirect::to($url)
            ->with('success', __d('contacts', 'The Field Item <b>{0}</b> was successfully created.', $item->title));
    }

    public function show($groupId, $id)
    {
        try {
            $group = FieldGroup::with('contact')->findOrFail($groupId);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::to('admin/contacts')->with('danger', __d('contacts', 'Field Group not found: #{0}', $groupId));
        }

        try {
            $item = FieldItem::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            $url = site_url('admin/contacts/{0}/field-groups', $contact->id);

            return Redirect::to($url)->with('danger', __d('contacts', 'Field Item not found: #{0}', $id));
        }

        /*
        // Authorize the current User.
        if (Gate::denies('view', $item)) {
            throw new AuthorizationException();
        }
        */

        $default = null;
        $choices = null;
        $rows    = 5;

        //
        $options = $item->options ?: array();

        return $this->createView()
            ->shares('title', __d('contacts', 'Show Field Item'))
            ->with('contact', $group->contact)
            ->with(compact('group', 'item', 'options'));
    }

    public function edit($groupId, $id)
    {
        try {
            $group = FieldGroup::with('contact')->findOrFail($groupId);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::to('admin/contacts')->with('danger', __d('contacts', 'Field Group not found: #{0}', $groupId));
        }

        try {
            $item = FieldItem::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            $url = site_url('admin/contacts/{0}/field-groups', $contact->id);

            return Redirect::to($url)->with('danger', __d('contacts', 'Field Item not found: #{0}', $id));
        }

        /*
        // Authorize the current User.
        if (Gate::denies('update', $item)) {
            throw new AuthorizationException();
        }
        */

        $placeholder = null;
        $default     = null;
        $choices     = null;
        $rows        = 5;

        //
        $type = $item->type;

        $options = is_array($options = $item->options) ? $options : array();

        // Handle for the Text inputs.
        if ($type == 'text') {
            $placeholder = Arr::get($options, 'placeholder');
            $default     = Arr::get($options, 'default');
        }

        // Handle for the Password inputs.
        else if ($type == 'password') {
            $placeholder = Arr::get($options, 'placeholder');
        }

        // Handle for the Textarea fields.
        else if ($type == 'textarea') {
            $placeholder = Arr::get($options, 'placeholder');
            $rows        = Arr::get($options, 'rows', 5);
        }

        // Handle for the Select fields.
        else if ($type == 'select') {
            $placeholder = Arr::get($options, 'placeholder');
            $default     = Arr::get($options, 'default');
            $choices     = Arr::get($options, 'choices');
        }

        // Handle for the Checkbox and Radio buttons.
        else if (($type == 'checkbox') || ($type == 'radio')) {
            $choices = Arr::get($options, 'choices');
        }

        // Handle for the File uploads.
        else if ($type == 'file') {
            //
        }

        return $this->createView()
            ->shares('title', __d('contacts', 'Edit Field Item'))
            ->with('contact', $group->contact)
            ->with(compact('group', 'item', 'placeholder', 'default', 'rows', 'choices'));
    }

    public function update(Request $request, $groupId, $id)
    {
        try {
            $group = FieldGroup::with('contact')->findOrFail($groupId);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::to('admin/contacts')->with('danger', __d('contacts', 'Field Group not found: #{0}', $groupId));
        }

        try {
            $item = FieldItem::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            $url = site_url('admin/contacts/{0}/field-groups', $contact->id);

            return Redirect::to($url)->with('danger', __d('contacts', 'Field Item not found: #{0}', $id));
        }

        /*
        // Authorize the current User.
        if (Gate::denies('update', $item)) {
            throw new AuthorizationException();
        }
        */

        $input = $request->only(
            'field_title', 'field_name', 'field_type', 'field_order', 'field_rules',
            'field_placeholder', 'field_default', 'field_choices', 'field_rows',
            'group_id'
        );

        // Validate the Input data.
        $validator = $this->validator($input, $group->contact, $id);

        if ($validator->fails()) {
            return Redirect::back()->withInput($input)->withErrors($validator->errors());
        }

        $type = $input['field_type'];

        // Handle the Text  inputs.
        if ($type == 'text') {
            $options = array(
                'placeholder' => $input['field_placeholder'],
                'default'     => $input['field_default']
            );
        }

        // Handle the Password inputs.
        else if ($type == 'password') {
            $options = array(
                'placeholder' => $input['field_placeholder'],
            );
        }

        // Handle the Textarea fields.
        else if ($type == 'textarea') {
            $options = array(
                'placeholder' => $input['field_placeholder'],
                'rows'        => $input['field_rows']
            );
        }

        // Handle the Select fields.
        else if ($type == 'select') {
            $options = array(
                'placeholder' => $input['field_placeholder'],
                'default'     => $input['field_default'],
                'choices'     => $input['field_choices']
            );
        }

        // Handle the Checkbox and Radio buttons.
        else if (($type == 'checkbox') || ($type == 'radio')) {
            $options = array(
                'choices' => $input['field_choices']
            );
        }

        // Handle the File uploads.
        else if ($type == 'file') {
            $options = array();
        }

        $title = $item->title;

        // Update a Field Item Model instance.
        $item->title = $input['field_title'];
        $item->name  = $input['field_name'];
        $item->type  = $input['field_type'];
        $item->order = $input['field_order'];
        $item->rules = $input['field_rules'];

        $item->options = $options;

        $item->save();

        // Invalidate the cached information.
        Cache::section('contacts')->flush();

        //
        $url = site_url('admin/contacts/{0}/field-groups', $group->contact->id);

        return Redirect::to($url)
            ->with('success', __d('contacts', 'The Field Item <b>{0}</b> was successfully updated.', $title));
    }

    public function destroy(Request $request, $groupId, $id)
    {
        try {
            $group = FieldGroup::with('contact')->findOrFail($groupId);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::to('admin/contacts')->with('danger', __d('contacts', 'Field Group not found: #{0}', $groupId));
        }

        try {
            $item = FieldItem::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::to('admin/contacts')->with('danger', __d('contacts', 'Field Item not found: #{0}', $id));
        }

        /*
        // Authorize the current User for deleting this Field Item instance.
        if (Gate::denies('delete', $item)) {
            throw new AuthorizationException();
        }
        */

        // Destroy the requested Field Item record.
        $item->delete();

        // Invalidate the cached information.
        Cache::section('contacts')->flush();

        //
        $url = site_url('admin/contacts/{0}/field-groups', $group->contact->id);

        return Redirect::to($url)->with('success', __d('contacts', 'The Field Item was successfully deleted.'));
    }
}
