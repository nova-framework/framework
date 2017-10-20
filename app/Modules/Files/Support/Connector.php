<?php

namespace App\Modules\Files\Support;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

use elFinderConnector;


class Connector extends elFinderConnector
{
    /**
     * @var \Symfony\Component\HttpFoundation\Response
     */
    protected $response;


    /**
     * Originally, output the data in a JSON form.
     *
     * @param  array  $data
     * @return void
     */
    protected function output(array $data)
    {
        $header = isset($data['header']) ? $data['header'] : $this->header;

        unset($data['header']);

        // Prepare the headers.
        $headers = array();

        if (! empty($header)) {
            $headers = $this->parseHeader($header);
        }

        if (isset($data['pointer'])) {
            $this->response = new StreamedResponse($this->getStreamCallback($data), 200, $headers);
        }

        // A non-streamed response is needed.
        else if (isset($data['raw']) && isset($data['error'])) {
            $this->response = new JsonResponse($data['error'], 500);
        } else {
            $this->response = new JsonResponse($data, 200, $headers);
        }
    }

    protected function parseHeader($value)
    {
        $headers = array();

        foreach((array) $value as $header) {
            if (strpos($header, ':') !== false) {
                list($key, $value) = explode(':', $header, 2);

                $headers[$key] = $value;
            }
        }

        return $headers;
    }

    protected function getStreamCallback(array $data)
    {
        return function () use ($data)
        {
            extract($data);

            // Handle the file pointer.
            rewind($pointer);

            fpassthru($pointer);

            if (! empty($volume)) {
                $volume->close($pointer, $info['hash']);
            }
        };
    }

    /**
     * Returns the Response instance.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}
