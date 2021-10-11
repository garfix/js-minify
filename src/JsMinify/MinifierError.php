<?php

namespace Garfix\JsMinify;

use RuntimeException;

/**
 * @author Patrick van Bergen
 */
class MinifierError
{
    /**
     * @throws RuntimeException
     */
    public static function checkRegexpError()
    {
        $error = preg_last_error();
        if ($error === 0) { return; }

        $msg = "";
        switch ($error) {
            case PREG_INTERNAL_ERROR: $msg = "Internal error (no specified)"; break;
            case PREG_BACKTRACK_LIMIT_ERROR: $msg = "Backtrace limit error"; break;
            case PREG_RECURSION_LIMIT_ERROR: $msg = "Recursion limit error"; break;
            case PREG_BAD_UTF8_ERROR: $msg = "Bad utf-8 error"; break;
            case PREG_BAD_UTF8_OFFSET_ERROR: $msg = "Bad utf-8 offset error"; break;
            case 6 /* PREG_JIT_STACKLIMIT_ERROR */: $msg = "JIT stack limit error"; break;
        }
        throw new RuntimeException("A regular expression error occurred: " . $msg);
    }

}