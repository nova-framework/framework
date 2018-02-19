<?php

namespace Modules\Users\Listeners;

use Nova\Http\UploadedFile;
use Nova\Support\Facades\File;
use Nova\Support\Str;

use Modules\Platform\Listeners\MetaFields\BaseListener;
use Modules\Users\Events\MetaFields\UpdateValidation;
use Modules\Users\Events\MetaFields\UserEditing;
use Modules\Users\Events\MetaFields\UserSaving;

use BadMethodCallException;
use InvalidArgumentException;


class MetaFields extends BaseListener
{

    /**
     * Handle the event.
     *
     * @param  Modules\Users\Events\UserEditing  $event
     * @return void
     */
    public function edit(UserEditing $event)
    {
        if (! is_null($user = $event->user)) {
            $meta = $user->meta;
        } else {
            $meta = null;
        }

        return $this->createView()
            ->with('request', $this->getRequest())
            ->with('meta', $meta)
            ->render();
    }

    /**
     * Handle the event.
     *
     * @param  Modules\Users\Events\UserValidation  $event
     * @return void
     */
    public function validator(UpdateValidation $event)
    {
        $rules = array();

        $attributes = array();

        return array($rules, $attributes);
    }

    /**
     * Handle the event.
     *
     * @param  Modules\Users\Events\UserEditing  $event
     * @return void
     */
    public function save(UserSaving $event)
    {
        $user = $event->user;

        $request = $this->getRequest();

        $user->saveMeta(array(
            'first_name' => $request->input('first-name'),
            'last_name'  => $request->input('last-name'),
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

        // Finally we save the associated meta field.
        $filePath = $path .DS .$fileName;

        // We will delete the previous file, before saving the uploaded one.
        if (! empty($filePath = $user->picture) && File::exists($filePath)) {
            $this->deleteFile($filePath);
        }

        $user->saveMeta('picture', $filePath);
    }
}
