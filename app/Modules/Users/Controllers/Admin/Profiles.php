<?php

namespace App\Modules\Users\Controllers\Admin;

use Nova\Auth\Access\AuthorizationException;
use Nova\Database\ORM\ModelNotFoundException;
use Nova\Http\Request;
use Nova\Support\Facades\App;
use Nova\Support\Facades\DB;
use Nova\Support\Facades\Gate;
use Nova\Support\Facades\Input;
use Nova\Support\Facades\File;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Validator;
use Nova\Support\Arr;

use App\Modules\Fields\Models\Field;
use App\Modules\Fields\Types\Registry as TypeRegistry;
use App\Modules\Platform\Controllers\Admin\BaseController;
use App\Modules\Users\Models\Profile;


class Profiles extends BaseController
{

    protected function validator(array $data, Profile $profile, $id = null)
    {
        // Validation rules
        $rules = array(
            'name'    => 'required|min:3|valid_name',
            'key'     => 'required|min:3|alpha_dash|unique_key',
            'type'    => 'required|valid_type',
            'order'   => 'required|numeric|min:0|max:1000',
            'columns' => 'required|numeric|min:1|max:8',
        );

        $messages = array(
            'valid_name' => __d('users', 'The :attribute field is not a valid Name.'),
            'valid_type' => __d('users', 'The :attribute field is not a valid Field type.'),
            'unique_key' => __d('users', 'The :attribute field is not unique in the Fields list.'),
        );

        $attributes = array(
            'name'    => __d('users', 'Name'),
            'key'     => __d('users', 'Key'),
            'type'    => __d('users', 'Type'),
            'order'   => __d('users', 'Order'),
            'columns' => __d('users', 'Columns'),
        );

        // Add the custom Validation Rule commands.
        Validator::extend('valid_name', function($attribute, $value, $parameters)
        {
            $pattern = '~^(?:[\p{L}\p{Mn}\p{Pd}\'\x{2019}]+(?:$|\s+)){1,}$~u';

            return (preg_match($pattern, $value) === 1);
        });

        Validator::extend('unique_key', function($attribute, $value, $parameters) use ($profile, $id)
        {
            $query = $profile->fields()->where('key', $value);

            if (! is_null($id)) {
                $query->where('id', '!=', $id);
            }

            return ! $query->exists();
        });

        Validator::extend('valid_type', function($attribute, $value, $parameters)
        {
            $registry = App::make(TypeRegistry::class);

            return $registry->has($value);
        });

        return Validator::make($data, $rules, $messages, $attributes);
    }

    public function index()
    {
        // Authorize the current User.
        if (Gate::denies('manage', Profile::class)) {
            throw new AuthorizationException();
        }

        $profile = Profile::findOrFail(1);

        $registry = App::make(TypeRegistry::class);

        $types = $registry->registered();

        return $this->createView()
            ->shares('title', __d('users', 'Users Profile'))
            ->with('profile', $profile)
            ->with('types', $types);
    }

    public function store(Request $request)
    {
        // Authorize the current User.
        if (Gate::denies('manage', Profile::class)) {
            throw new AuthorizationException();
        }

        $profile = Profile::findOrFail(1);

        //
        $input = $request->all();

        // Validate the Input data.
        $validator = $this->validator($input, $profile);

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withStatus($validator->errors(), 'danger');
        }

        $fields = $profile->fields();

        //
        $hidden = $request->has('hidden');

        $order = $hidden ? 0 : $input['order'];

        $fields->create(array(
            'name'     => $input['name'],
            'key'      => $input['key'],
            'type'     => $input['type'],
            'validate' => $input['validate'],
            'order'    => $order,
            'columns'  => $input['columns'],
            'hidden'   => (int) $hidden,
        ));

        return Redirect::to('admin/profile')
            ->withStatus(__d('users', 'The Field <b>{0}</b> was successfully created.', $input['name']), 'success');
    }

    public function update(Request $request, $id)
    {
        // Authorize the current User.
        if (Gate::denies('manage', Profile::class)) {
            throw new AuthorizationException();
        }

        $profile = Profile::findOrFail(1);

        try {
            $field = Field::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::to('admin/profile')->withStatus(__d('users', 'Field not found: #{0}', $id), 'danger');
        }

        $input = $request->all();

        // Validate the Input data.
        $validator = $this->validator($input, $profile, $id);

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withStatus($validator->errors(), 'danger');
        }

        $name = $field->name;

        //
        $hidden = $request->has('hidden');

        $order = $hidden ? 0 : $input['order'];

        // Fill the data.
        $field->name     = $input['name'];
        $field->key      = $input['key'];
        $field->type     = $input['type'];
        $field->validate = $input['validate'];
        $field->order    = $order;
        $field->columns  = $input['columns'];
        $field->hidden   = (int) $hidden;

        // Save the Field instance.
        $field->save();

        return Redirect::to('admin/profile')
            ->withStatus(__d('users', 'The Field <b>{0}</b> was successfully updated.', $name), 'success');
    }

    public function destroy($id)
    {
        // Authorize the current User.
        if (Gate::denies('manage', Profile::class)) {
            throw new AuthorizationException();
        }

        try {
            $field = Field::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::to('admin/profile')->withStatus(__d('users', 'Field not found: #{0}', $id), 'danger');
        }

        // We should delete first the associated meta-data.
        DB::table('users_meta')->where('key', $field->key)->delete();

        // Destroy the requested Field record.
        $field->delete();

        return Redirect::to('admin/profile')
            ->withStatus(__d('users', 'The Field <b>{0}</b> was successfully deleted.', $field->name), 'success');
    }
}
