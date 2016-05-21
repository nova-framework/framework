<?php

namespace Http;

use ArrayObject;
use Symfony\Component\HttpFoundation\Cookie;

use Core\Base\View;
use Support\Contracts\JsonableInterface;
use Support\Contracts\RenderableInterface;


class Response extends \Symfony\Component\HttpFoundation\Response
{
    /**
     * The original content of the Response.
     *
     * @var mixed
     */
    public $original;


    /**
     * Set a header on the Response.
     *
     * @param  string  $key
     * @param  string  $value
     * @param  bool    $replace
     * @return \Http\Response
     */
    public function header($key, $value, $replace = true)
    {
        $this->headers->set($key, $value, $replace);

        return $this;
    }

    /**
     * Add a cookie to the response.
     *
     * @param  \Symfony\Component\HttpFoundation\Cookie  $cookie
     * @return \Http\Response
     */
    public function withCookie(Cookie $cookie)
    {
        $this->headers->setCookie($cookie);

        return $this;
    }

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
        } else if (($content instanceof RenderableInterface) || ($content instanceof View)) {
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
