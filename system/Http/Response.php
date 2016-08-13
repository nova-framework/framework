<?php

namespace Http;

use Http\ResponseTrait;
use Support\Contracts\JsonableInterface;
use Support\Contracts\RenderableInterface;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

use ArrayObject;


class Response extends SymfonyResponse
{
    use ResponseTrait;

    /**
     * The original content of the Response.
     *
     * @var mixed
     */
    public $original;


    /**
     * Set the content on the response.
     *
     * @param  mixed  $content
     * @return void
     */
    public function setContent($content)
    {
        $this->original = $content;

        if ($this->shouldBeJson($content)) {
            $this->headers->set('Content-Type', 'application/json');

            $content = $this->morphToJson($content);
        } else if ($content instanceof RenderableInterface) {
            $content = $content->render();
        }

        return parent::setContent($content);
    }

    /**
     * Morph the given content into JSON.
     *
     * @param  mixed   $content
     * @return string
     */
    protected function morphToJson($content)
    {
        if ($content instanceof JsonableInterface) {
            return $content->toJson();
        }

        return json_encode($content);
    }

    /**
     * Determine if the given content should be turned into JSON.
     *
     * @param  mixed  $content
     * @return bool
     */
    protected function shouldBeJson($content)
    {
        return (($content instanceof JsonableInterface) || ($content instanceof ArrayObject) || is_array($content));
    }

    /**
     * Get the original response content.
     *
     * @return mixed
     */
    public function getOriginalContent()
    {
        return $this->original;
    }

}
