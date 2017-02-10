<?php

namespace Plugins\Presenter;

use Plugins\Presenter\Exceptions\PresenterException;


trait PresentableTrait
{
    /**
     * View Presenter instance
     *
     * @var mixed
     */
    protected $presenterInstance;

    /**
     * Prepare a new or cached Presenter instance.
     *
     * @return mixed
     * @throws \Plugins\Presenter\View\Exceptions\PresenterException
     */
    public function present()
    {
        if (is_null($this->presenter) || ! class_exists($this->presenter)) {
            throw new PresenterException('Please set the $presenter property to your presenter path.');
        }

        if ( ! $this->presenterInstance) {
            $className = $this->presenter;

            $this->presenterInstance = new $className($this);
        }

        return $this->presenterInstance;
    }

}
