<?php

namespace Modules\Users\Listeners;

use Nova\Http\UploadedFile;
use Nova\Support\Facades\File;
use Nova\Support\Str;

use Modules\Platform\Listeners\MetaFields\BaseListener;

use Modules\Users\Events\MetaFields\UpdateUserValidation;
use Modules\Users\Events\MetaFields\UserEditing;
use Modules\Users\Events\MetaFields\UserSaved;
use Modules\Users\Events\MetaFields\UserShowing;
use Modules\Users\Models\User;

use BadMethodCallException;
use InvalidArgumentException;


class MetaFields extends BaseListener
{

    /**
     * Handle the event.
     *
     * @param  Modules\Users\Models\User  $user
     * @return void
     */
    public function deleting(User $user)
    {
        $user->load('meta');

        if (! empty($filePath = $user->meta->picture) && File::exists($filePath)) {
            $this->deleteFile($filePath);
        }
    }

    /**
     * Handle the event.
     *
     * @param  Modules\Users\Events\UpdateValidation  $event
     * @return void
     */
    public function updateValidator(UpdateUserValidation $event)
    {
        $rules = array(
            'first_name' => 'required|min:3|max:100|valid_name',
            'last_name'  => 'required|min:3|max:100|valid_name',
            'location'   => 'min:3|max:100',
            'picture'    => 'max:1024|mimes:png,jpg,jpeg,gif',
        );

        $messages = array(
            //
        );

        $attributes = array(
            'first_name' => __d('users', 'First Name'),
            'last_name'  => __d('users', 'Last Name'),
            'location'   => __d('users', 'Location'),
            'picture'    => __d('users', 'Picture'),
        );

        // Update the Validator instance passed via the given Event.
        $validator = $event->validator;

        $validator->setRules(
            array_merge($validator->getRules(), $rules)
        );

        $validator->setCustomMessages($messages);
        $validator->addCustomAttributes($attributes);
    }

    /**
     * Handle the event.
     *
     * @param  Modules\Users\Events\UserEditing  $event
     * @return void
     */
    public function edit(UserEditing $event)
    {
        if (! is_null($user = $event->user)) {
            $fields = $user->meta;
        } else {
            $fields = null;
        }

        return $this->createView()
            ->with('request', $this->getRequest())
            ->with('fields', $fields)
            ->render();
    }

    /**
     * Handle the event.
     *
     * @param  Modules\Users\Events\UserSaved  $event
     * @return void
     */
    public function save(UserSaved $event)
    {
        $user = $event->user;

        //
        $request = $this->getRequest();

        $user->saveMeta(array(
            'first_name' => $request->input('first_name'),
            'last_name'  => $request->input('last_name'),
            'location'   => $request->input('location'),
        ));

        // Handle the User Picture, which is an uploaded file.
        $picture = $request->file('picture');

        if ($picture instanceof UploadedFile) {
            $fileName = pathinfo($picture->getClientOriginalName(), PATHINFO_FILENAME);

            $extension = $picture->getClientOriginalExtension();

            $fileName = sprintf('%s-%s.%s', uniqid(), Str::slug($fileName), $extension);
        } else if (empty($picture)) {
            return;
        }

        // An invalid picture was given.
        else {
            throw new InvalidArgumentException("No uploaded file was given. [$picture].");
        }

        $path = $this->getFilesPath('pictures');

        if (! File::exists($path)) {
            File::makeDirectory($path, 0755, true, true);
        }

        $picture->move($path, $fileName);

        // We will delete first the previous uploaded file.
        if (! empty($filePath = $user->meta->picture) && File::exists($filePath)) {
            $this->deleteFile($filePath);
        }

        // Finally we save the associated meta field.
        $filePath = $path .DS .$fileName;

        $user->saveMeta('picture', $filePath);
    }

    /**
     * Handle the event.
     *
     * @param  Modules\Users\Events\UserShowing  $event
     * @return void
     */
    public function show(UserShowing $event)
    {
        $fields = $event->user->meta;

        $result = array(
            'first_name' => array(
                'name' => __d('users', 'First Name'),
                'text' => $fields->first_name,
            ),
            'last_name'  => array(
                'name' => __d('users', 'Last Name'),
                'text' => $fields->last_name,
            ),
            'location'   => array(
                'name'  => __d('users', 'Location'),
                'text' => $fields->location,
            ),
            'picture'    => array(
                'name' => __d('users', 'Picture'),
                'text' => '-',
            ),
        );

        if (! empty($picture = $fields->picture)) {
            $path = str_replace(BASEPATH, '', $picture);

            $name = preg_replace('#^([^\-]+)\-(.*)$#s', '$2', basename($path));

            $html = $this->createView(compact('path', 'name'), 'Picture')->render();

            //
            $result['picture']['text'] = $html;
        }

        return $result;
    }
}
