<?php

namespace Garfix\JsMinify;

use RuntimeException;

/**
 * This minifier removes whitespace and comments from a Javascript string.
 *
 * See docs for information.
 *
 * @author Patrick van Bergen
 */
class Minifier
{
    const FLAGGED_COMMENTS = 'flaggedComments';

    protected static $defaultOptions = [
        self::FLAGGED_COMMENTS => true
    ];

    protected $options = [];

    /**
     * Processes a javascript string and returns only the required characters,
     * stripping out all unneeded whitespace and comments.
     *
     * @param string $js      The raw javascript to be minified
     * @param array  $options Various runtime options in an associative array
     * @throws RuntimeException
     */
    public static function minify($js, $options = [])
    {
        $minifier = new Minifier($options);
        return $minifier->minifyUsingCallbacks($js);
    }

    public function __construct($options)
    {
        $this->options = array_merge(static::$defaultOptions, $options);
    }

    /**
     * @param string $js    The raw javascript to be minified
     * @return string
     * @throws RuntimeException
     */
    public function minifyUsingCallbacks($js)
    {
        $e = MinifierExpressions::get();

        // treat all newlines as unix newlines, to keep working with newlines simple
        $shrunkText = str_replace(["\r\n", "\r"], ["\n", "\n"], $js);

        // remove comments
        $exp1 = "~(?:" .  implode("|", $e->tokenExpressions) . ")~su";
        $shrunkText = preg_replace_callback($exp1, [$this, 'removeComments'], $shrunkText);
        MinifierError::checkRegexpError();

        // rewrite blocks by inserting whitespace placeholders
        $exp2 = "~(?:" .  implode("|", $e->allExpressions) . ")~su";
        $shrunkText = preg_replace_callback($exp2, [$this, 'processBlocks'], $shrunkText);
        MinifierError::checkRegexpError();

        // remove all remaining space (without the newlines)
        $shrunkText = preg_replace("~" . $e->someWhitespace . "~", '', $shrunkText);
        MinifierError::checkRegexpError();

        // reduce consecutive newlines to single one
        $shrunkText = preg_replace("~[\\n]+~", "\n", $shrunkText);
        MinifierError::checkRegexpError();

        // remove newlines that may safely be removed
        foreach ($e->safeNewlines as $safeNewline) {
            $shrunkText = preg_replace("~" . $safeNewline . "~", "", $shrunkText);
            MinifierError::checkRegexpError();
        }

        // replace whitespace placeholders by their original whitespace
        $shrunkText = str_replace($e->whitespaceArrayPlaceholders, $e->whitespaceArrayNewline, $shrunkText);
        MinifierError::checkRegexpError();

        // remove leading and trailing whitespace
        return trim($shrunkText);
    }

    /**
     * Removes all comments that need to be removed
     * The newlines that are added here may later be removed again
     *
     * @param $matches
     * @return mixed|string
     */
    protected function removeComments($matches)
    {
        $e = MinifierExpressions::get();

        // the fully matching text
        $match = $matches[0];

        if (!empty($matches['lineComment'])) {
            // not empty because this might glue words together
            return "\n";
        }
        if (!empty($matches['starComment'])) {

            // create a version without leading and trailing whitespace
            $trimmed = trim($match, $e->whitespaceCharsNewline);

            switch ($trimmed[2]) {
                case '@':
                    // IE conditional comment
                    return $match;
                case '!':
                    if ($this->options[self::FLAGGED_COMMENTS]) {
                        // option says: leave flagged comments in
                        return $match;
                    }
            }
            // multi line comment; not empty because this might glue words together
            return "\n";
        }

        // leave other matches unchanged
        return $match;
    }

    /**
     * Updates the code for all blocks (they contain whitespace that should be conserved)
     * No early returns: all code must reach `end` and have the whitespace replaced by placeholders
     *
     * @param $matches
     * @return array|mixed|string|string[]
     */
    protected function processBlocks($matches)
    {
        $e = MinifierExpressions::get();

        // the fully matching text
        $match = $matches[0];

        // create a version without leading and trailing whitespace
        $trimmed = trim($match, $e->whitespaceCharsNewline);

        // Should be handled before optional whitespace
        if (!empty($matches['requiredSpace'])) {
            $match = strpos($matches['requiredSpace'], "\n") === false ? " " : "\n";
            goto end;
        }
        // + followed by +, or - followed by -
        if (!empty($matches['plus']) || !empty($matches['min'])) {
            $match = ' ';
            goto end;
        }
        if (!empty($matches['doubleQuote'])) {
            // remove line continuation
            $match = str_replace("\\\n", "", $match);
            goto end;
        }
        if (!empty($matches['starComment'])) {
            switch ($trimmed[2]) {
                case '@':
                    // IE conditional comment
                    $match = $trimmed;
                    goto end;
                case '!':
                    if ($this->options[self::FLAGGED_COMMENTS]) {
                        // ensure newlines before and after
                        $match = "\n" . $trimmed . "\n";
                        goto end;
                    }
            }
            // simple multi line comment; will have been removed in the first step
            goto end;
        }
        if (!empty($matches['regexp'])) {
            // regular expression
            // only if the space after the regexp contains a newline, keep it
            preg_match("~^" . $e->regexp . "(?P<post>" . $e->optionalWhitespaceNewline . ")" . "$~su",
                $match, $newMatches);
            $postfix = strpos($newMatches['post'], "\n") === false ? "" : "\n";
            $match = $trimmed . $postfix;
            goto end;
        }

        end:

        return str_replace($e->whitespaceArrayNewline, $e->whitespaceArrayPlaceholders, $match);
    }
}
