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
use Modules\Contacts\Models\FieldItem;
use Modules\Platform\Controllers\Admin\BaseController;


class FieldItems extends BaseController
{
    public function index($id)
    {
        try {
            $group = FieldGroup::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::to('admin/contacts')->with('danger', __d('contacts', 'Field Group not found: #{0}', $id));
        }

        /*
        // Authorize the current User.
        if (Gate::denies('lists', FieldItem::class)) {
            throw new AuthorizationException();
        }
        */

        return $this->createView()
            ->shares('title', __d('contacts', 'Manage the Field Items : {0}', $group->name))
            ->with(compact('group'));
    }
}
