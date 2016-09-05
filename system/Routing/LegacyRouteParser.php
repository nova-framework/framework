<?php

namespace Routing;

use Routing\Route;


class LegacyRouteParser
{
    const REGEX_DELIMITER = '#';

    /**
     * This string defines the characters that are automatically considered separators in front of
     * optional placeholders (with default and no static text following). Such a single separator
     * can be left out together with the optional placeholder from matching and generating URLs.
     */
    const SEPARATORS = '/,;.:-_~+*=@|';


    public static function parse($route, array $patterns = array())
    {
        preg_match_all('#\(:\w+\)#', $route, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);

        //
        $patterns = array_merge($patterns, array(
            ':any' => '[^/]++',
            ':num' => '([0-9]+)',
            ':all' => '(.*)'
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
                $regexp = '[^/]++';
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

        // Create the Route wheres and translated pattern.
        list($pattern, $wheres) = static::process($tokens, $optionals);

        return array($pattern, $optionals, $wheres);
    }

    private static function process(array $tokens, array $optionals)
    {
        $pattern = '';

        $wheres = array();

        foreach ($tokens as $token) {
            if ($token[0] == 'text') {
                $pattern .= $token[1];

                continue;
            }

            // A token of type 'variable'; extract its information.
            list($type, $separator, $varName, $regexp) = $token;

            $pattern .= $separator .'{' .$varName .'}';

            if ($regexp != '[^/]++') {
                $wheres[$varName] = $regexp;
            }
        }

        return array($pattern, $wheres);
    }

}
