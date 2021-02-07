<?php

namespace Modules\Users\Controllers\Admin;

use Nova\Auth\Access\AuthorizationException;
use Nova\Database\ORM\ModelNotFoundException;
use Nova\Http\Request;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Gate;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Response;
use Nova\Support\Facades\Validator;
use Nova\Support\Facades\View;
use Nova\Support\Arr;

use Modules\Users\Models\FieldItem;
use Modules\Users\Models\User;
use Modules\Platform\Controllers\Admin\BaseController;


class FieldItems extends BaseController
{

    protected function validator(array $data, $id = null)
    {
        $types = array('text', 'textarea', 'select', 'checkbox', 'radio');

        $ignore = ! is_null($id) ? ',' .intval($id) : '';

        // The Validation rules.
        $rules = array(
            'field_title' => 'required|min:4|max:40|valid_title',
            'field_name'  => 'required|min:4|max:40|alpha_dash|unique:user_field_items,name' .$ignore,
            'field_type'  => 'required|valid_type',
            'field_order' => 'required|numeric|min:-100|max:100',
        );

        $messages = array(
            'valid_title' => __d('users', 'The :attribute field is not a valid title.'),
            'valid_type'  => __d('users', 'The :attribute field is not a valid type.'),
        );

        $attributes = array(
            'field_title' => __d('users', 'Label'),
            'field_name'  => __d('users', 'Name'),
            'field_type'  => __d('users', 'Type'),
            'field_order' => __d('users', 'Order'),
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

        return $validator;
    }

    public function index()
    {
        // Authorize the current User.
        if (Gate::denies('lists', FieldItem::class)) {
            throw new AuthorizationException();
        }

        $items = FieldItem::all();

        return $this->createView()
            ->shares('title', __d('users', 'Custom Fields for Users'))
            ->with('items', $items);
    }

    public function create()
    {
        // Authorize the current User.
        if (Gate::denies('create', FieldItem::class)) {
            throw new AuthorizationException();
        }

        return $this->createView()
            ->shares('title', __d('users', 'Create Field'));
    }

    public function store(Request $request)
    {
        // Authorize the current User.
        if (Gate::denies('create', FieldItem::class)) {
            throw new AuthorizationException();
        }

        $input = $request->only(
            'field_title', 'field_name', 'field_type', 'field_order', 'field_rules',
            'field_placeholder', 'field_default', 'field_choices', 'field_rows'
        );

        // Validate the Input data.
        $validator = $this->validator($input);

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

        // Create a Field Model instance.
        $item = FieldItem::create(array(
            'title'   => $input['field_title'],
            'name'    => $input['field_name'],
            'type'    => $input['field_type'],
            'order'   => $input['field_order'],
            'rules'   => $input['field_rules'],

            'options' => $options,

            //
            'created_by' => Auth::id(),
        ));

        return Redirect::to('admin/users/fields')
            ->with('success', __d('users', 'The Field <b>{0}</b> was successfully created.', $item->title));
    }

    public function show($id)
    {
        try {
            $item = FieldItem::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::to('admin/users/fields')->with('danger', __d('users', 'Field not found: #{0}', $id));
        }

        // Authorize the current User.
        if (Gate::denies('view', $item)) {
            throw new AuthorizationException();
        }

        $default = null;
        $choices = null;
        $rows    = 5;

        //
        $options = $item->options ?: array();

        return $this->createView()
            ->shares('title', __d('users', 'Show Field'))
            ->with(compact('item', 'options'));
    }

    public function edit($id)
    {
        try {
            $item = FieldItem::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            $url = site_url('admin/users/fields');

            return Redirect::to($url)->with('danger', __d('users', 'Field not found: #{0}', $id));
        }

        // Authorize the current User.
        if (Gate::denies('update', $item)) {
            throw new AuthorizationException();
        }

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

        return $this->createView()
            ->shares('title', __d('users', 'Edit Field'))
            ->with(compact('item', 'placeholder', 'default', 'rows', 'choices'));
    }

    public function update(Request $request, $id)
    {
        try {
            $item = FieldItem::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            $url = site_url('admin/users/fields', $contact->id);

            return Redirect::to($url)->with('danger', __d('users', 'Field not found: #{0}', $id));
        }

        // Authorize the current User.
        if (Gate::denies('update', $item)) {
            throw new AuthorizationException();
        }

        $input = $request->only(
            'field_title', 'field_name', 'field_type', 'field_order', 'field_rules',
            'field_placeholder', 'field_default', 'field_choices', 'field_rows'
        );

        // Validate the Input data.
        $validator = $this->validator($input, $id);

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

        $title = $item->title;

        // Update a Field Model instance.
        $item->title = $input['field_title'];
        $item->name  = $input['field_name'];
        $item->type  = $input['field_type'];
        $item->order = $input['field_order'];
        $item->rules = $input['field_rules'];

        $item->options = $options;

        $item->updated_by = Auth::id();

        $item->save();

        return Redirect::to('admin/users/fields')
            ->with('success', __d('users', 'The Field <b>{0}</b> was successfully updated.', $title));
    }

    public function destroy(Request $request, $id)
    {
        try {
            $item = FieldItem::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::to('admin/users/fields')->with('danger', __d('users', 'Field not found: #{0}', $id));
        }

        // Authorize the current User for deleting this Field instance.
        if (Gate::denies('delete', $item)) {
            throw new AuthorizationException();
        }

        // Destroy the requested Field record.
        $item->delete();

        return Redirect::to('admin/users/fields')
            ->with('success', __d('users', 'The Field was successfully deleted.'));
    }
}