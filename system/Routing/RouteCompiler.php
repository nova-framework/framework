<?php

namespace Routing;

use Closure;


class RouteCompiler
{
    /**
     * This string defines the characters that are automatically considered separators in front of
     * optional placeholders (with default and no static text following). Such a single separator
     * can be left out together with the optional placeholder from matching and generating URLs.
     */
    const SEPARATORS = '/,;.:-_~+*=@|';

    /**
     * The regular expression requirements.
     *
     * @var array
     */
    protected $patterns = array();


    /**
     * Create a new controller dispatcher instance.
     *
     * @param  string $route
     * @param  array  $patterns
     * @return void
     */
    public function __construct(array $patterns = array())
    {
        $this->patterns = $patterns;
    }

    protected function createPattern(array $tokens, array $optionals)
    {
        $pattern = '';

        foreach ($tokens as $token) {
            if ($token[0] == 'text') {
                $pattern .= $token[1];

                continue;
            }

            list($type, $separator, $varName, $regexp) = $token;

            //
            if (in_array($varName, $optionals)) $pattern .= '(?:';

            $pattern .= $separator .'(?P<' .$varName .'>' .$regexp .')';
        }

        // Pad the pattern with ')?' if it is need.
        if (! empty($optionals)) {
            $pattern .= str_repeat (')?', count($optionals));
        }

        return $pattern;
    }

    public function compileRoute($route, array $optionals = array())
    {
        preg_match_all('#\{[^\}]+\}#', $route, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);

        //
        $variables = array();

        $tokens = array();

        $pos = 0;

        foreach ($matches as $match) {
            $varName = substr($match[0][0], 1, -1);

            if (in_array($varName, $variables)) {
                $error = sprintf('Route pattern "%s" cannot reference variable name "%s" more than once.', $route, $varName);

                throw new \LogicException($error);
            }

            array_push($variables, $varName);

            // Get all static text preceding the current variable.
            $precedingText = substr($route, $pos, $match[0][1] - $pos);

            $pos = $match[0][1] + strlen($match[0][0]);

            $precedingChar = (strlen($precedingText) > 0) ? substr($precedingText, -1) : '';

            $isSeparator = ('' !== $precedingChar) && (false !== strpos(static::SEPARATORS, $precedingChar));

            //
            if ($isSeparator && (strlen($precedingText) > 1)) {
                $tokens[] = array('text', substr($precedingText, 0, -1));
            } elseif (! $isSeparator && (strlen($precedingText) > 0)) {
                $tokens[] = array('text', $precedingText);
            }

            if (isset($this->patterns[$varName])) {
                $regexp = $this->patterns[$varName];
            } else {
                $regexp = '[^/]+';
            }

            $tokens[] = array('variable', $isSeparator ? $precedingChar : '', $varName, $regexp);
        }

        if ($pos < strlen($route)) {
            $tokens[] = array('text', substr($route, $pos));
        }

        return $this->createPattern($tokens, $optionals);
    }

    public function compileLegacyRoute($route)
    {
        preg_match_all('#\(:\w+\)#', $route, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);

        //
        $patterns = array_merge($this->patterns, array(
            ':any' => '[^/]+',
            ':num' => '[0-9]+',
            ':all' => '.*'
        ));

        //
        $optionals = array();

        $tokens = array();

        $pos = 0;
        $cnt = 1;

        foreach ($matches as $match) {
            $varName = substr($match[0][0], 1, -1);

            // Get all static text preceding the current variable
            $precedingText = substr($route, $pos, $match[0][1] - $pos);

            $pos = $match[0][1] + strlen($match[0][0]);

            $precedingChar = (strlen($precedingText) > 0) ? substr($precedingText, -1) : '';

            $isSeparator = ('' !== $precedingChar) && (false !== strpos(static::SEPARATORS, $precedingChar));

            //
            $isOptional = false;

            if ($isSeparator && (strlen($precedingText) > 1)) {
                if (substr($precedingText, -2) == '(/') {
                    $isOptional = true;

                    if ((strlen($precedingText) > 2)) {
                        $tokens[] = array('text', substr($precedingText, 0, -2));
                    }
                } else {
                    $tokens[] = array('text', substr($precedingText, 0, -1));
                }
            } else if (! $isSeparator && (strlen($precedingText) > 0)) {
                $tokens[] = array('text', $precedingText);
            }

            if (isset($patterns[$varName])) {
                $regexp = $patterns[$varName];
            } else {
                $regexp = '[^/]+';
            }

            //
            $varName = 'param' .$cnt++;

            if ($isOptional) {
                array_push($optionals, $varName);
            }

            $tokens[] = array('variable', $isSeparator ? $precedingChar : '', $varName, $regexp);
        }

        if (empty($optionals) && ($pos < strlen($route))) {
            $tokens[] = array('text', substr($route, $pos));
        }

        return $this->createPattern($tokens, $optionals);
    }

}
