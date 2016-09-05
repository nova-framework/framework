<?php

namespace Routing\Compiler;

use Routing\Compiler\CompiledRoute;
use Routing\Compiler\RouteCompilerInterface;
use Routing\Route;

use Closure;


class RouteCompiler implements RouteCompilerInterface
{
    const REGEX_DELIMITER = '#';

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

    public function compile(Route $route, array $optionals = array())
    {
        $hostVariables = array();

        $variables = array();

        $hostRegex = null;

        $hostTokens = array();

        //
        $domain = $route->domain();

        if (! is_null($domain)) {
            $result = $this->compilePattern($route, $domain, $optionals, true);

            $hostVariables = $result['variables'];

            $variables = $hostVariables;

            $hostTokens = $result['tokens'];
            $hostRegex  = $result['regex'];
        }

        $path = preg_replace('/\{(\w+?)\?\}/', '{$1}', $route->getPath());

        $result = $this->compilePattern($route, $path, $optionals, false);

        $staticPrefix  = $result['staticPrefix'];
        $pathVariables = $result['variables'];

        $variables = array_merge($variables, $pathVariables);

        $tokens = $result['tokens'];
        $regex  = $result['regex'];

        return new CompiledRoute(
            $staticPrefix,
            $regex,
            $tokens,
            $pathVariables,
            $hostRegex,
            $hostTokens,
            $hostVariables,
            array_unique($variables)
        );
    }

    protected function compilePattern(Route $route, $pattern, array $optionals = array(), $isHost)
    {
        preg_match_all('#\{[^\}]+\}#', $pattern, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);

        //
        $regexp = '';

        $variables = array();

        $tokens = array();

        $pos = 0;

        $defaultSeparator = $isHost ? '.' : '/';

        foreach ($matches as $match) {
            $varName = substr($match[0][0], 1, -1);

           if (is_numeric($varName)) {
                sprintf('Variable name "%s" cannot be numeric in route pattern "%s". Please use a different name.', $varName, $pattern);

                throw new \DomainException($error);
            }
            if (in_array($varName, $variables)) {
                $error = sprintf('Route pattern "%s" cannot reference variable name "%s" more than once.', $pattern, $varName);

                throw new \LogicException($error);
            }

            array_push($variables, $varName);

            // Get all static text preceding the current variable.
            $precedingText = substr($pattern, $pos, $match[0][1] - $pos);

            $pos = $match[0][1] + strlen($match[0][0]);

            $precedingChar = (strlen($precedingText) > 0) ? substr($precedingText, -1) : '';

            $isSeparator = ('' !== $precedingChar) && (false !== strpos(static::SEPARATORS, $precedingChar));

            //
            if ($isSeparator && (strlen($precedingText) > 1)) {
                $tokens[] = array('text', substr($precedingText, 0, -1));
            } else if (! $isSeparator && (strlen($precedingText) > 0)) {
                $tokens[] = array('text', $precedingText);
            }

            $regexp = $this->getRequirement($varName);

            if (null === $regexp) {
                $followingPattern = (string) substr($pattern, $pos);

                $nextSeparator = static::findNextSeparator($followingPattern);

                $regexp = sprintf(
                    '[^%s%s]+',
                    preg_quote($defaultSeparator, static::REGEX_DELIMITER),
                    ($defaultSeparator !== $nextSeparator) && ('' !== $nextSeparator) ? preg_quote($nextSeparator, static::REGEX_DELIMITER) : ''
                );

                if ((('' !== $nextSeparator) && ! preg_match('#^\{\w+\}#', $followingPattern)) || ('' === $followingPattern)) {
                    $regexp .= '+';
                }
            }

            $tokens[] = array('variable', $isSeparator ? $precedingChar : '', $varName, $regexp);
        }

        if ($pos < strlen($pattern)) {
            $tokens[] = array('text', substr($pattern, $pos));
        }

        // Find the first optional token
        $firstOptional = PHP_INT_MAX;

        if (! $isHost) {
            for ($i = (count($tokens) - 1); $i >= 0; --$i) {
                $token = $tokens[$i];

                if (('variable' === $token[0]) && in_array($token[2], $optionals)) {
                    $firstOptional = $i;
                } else {
                    break;
                }
            }
        }

        // compute the matching regexp
        $regexp = '';

        for ($i = 0, $nbToken = count($tokens); $i < $nbToken; ++$i) {
            $regexp .= static::computeRegexp($tokens, $i, $firstOptional);
        }

        $regexp = ($regexp == '/') ? '/' : '/' .$regexp;

        return array(
            'staticPrefix' => ! empty($tokens) && ('text' === $tokens[0][0]) ? $tokens[0][1] : '',
            'regex' => static::REGEX_DELIMITER .'^'.$regexp .'$' .static::REGEX_DELIMITER .'s'.($isHost ? 'i' : ''),
            'tokens' => array_reverse($tokens),
            'variables' => $variables,
        );
    }

    /**
     * Returns the next static character in the Route pattern that will serve as a separator.
     *
     * @param string $pattern The route pattern
     *
     * @return string The next static character that functions as separator (or empty string when none available)
     */
    private static function findNextSeparator($pattern)
    {
        if ('' == $pattern) {
            // Return empty string if pattern is empty or false (false which can be returned by substr)
            return '';
        }

        // First remove all placeholders from the pattern so we can find the next real static character
        $pattern = preg_replace('#\{\w+\}#', '', $pattern);

        return isset($pattern[0]) && (false !== strpos(static::SEPARATORS, $pattern[0])) ? $pattern[0] : '';
    }

    /**
     * Computes the regexp used to match a specific token. It can be static text or a subpattern.
     *
     * @param array $tokens        The route tokens
     * @param int   $index         The index of the current token
     * @param int   $firstOptional The index of the first optional token
     *
     * @return string The regexp pattern for a single token
     */
    private static function computeRegexp(array $tokens, $index, $firstOptional)
    {
        $token = $tokens[$index];

        if ('text' === $token[0]) {
            // Text tokens
            return preg_quote($token[1], static::REGEX_DELIMITER);
        } else {
            // Variable tokens
            if ((0 === $index) && (0 === $firstOptional)) {
                // When the only token is an optional variable token, the separator is required
                return sprintf('%s(?P<%s>%s)?', preg_quote($token[1], static::REGEX_DELIMITER), $token[2], $token[3]);
            } else {
                $regexp = sprintf('%s(?P<%s>%s)', preg_quote($token[1], static::REGEX_DELIMITER), $token[2], $token[3]);

                if ($index >= $firstOptional) {
                    $regexp = "(?:$regexp";

                    $nbTokens = count($tokens);

                    if (($nbTokens - 1) == $index) {
                        // Close the optional subpatterns
                        $regexp .= str_repeat(')?', $nbTokens - $firstOptional - (0 === $firstOptional ? 1 : 0));
                    }
                }

                return $regexp;
            }
        }
    }

    protected function getRequirement($name)
    {
        return isset($this->patterns[$name]) ? $this->patterns[$name] : null;
    }

    public function parseLegacyRoute($route)
    {
        preg_match_all('#\(:\w+\)#', $route, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);

        //
        $patterns = array_merge($this->patterns, array(
            ':any' => '[^/]+',
            ':num' => '[0-9]+',
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

        // Create the Route translated pattern.
        $path = static::createPath($tokens, $optionals);

        // Create the Route wheres.
        $wheres = array();

        foreach ($tokens as $token) {
            if (($token[0] == 'variable') && ($token[3] != '[^/]+')) {
                $key = $token[2];

                $wheres[$key] = $token[3];
            }
        }

        return array($path, $optionals, $wheres);
    }

    private static function createPath(array $tokens, array $optionals)
    {
        $pattern = '';

        foreach ($tokens as $token) {
            if ($token[0] == 'text') {
                $pattern .= $token[1];

                continue;
            }

            // A token of type 'variable'; extract its information.
            list($type, $separator, $varName, $regexp) = $token;

            $pattern .= $separator .'{' .$varName;

            if (in_array($varName, $optionals)) {
                $pattern .= '?';
            }

            $pattern .= '}';
        }

        return $pattern;
    }

    public function setRequirements(array $patterns = array())
    {
        $this->patterns = $patterns;
    }
}
