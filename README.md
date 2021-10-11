# js-minify

A fast Javascript minifier that removes unnecessary whitespace and comments

## Installation

If you are using Composer, use 

    composer require garfix/js-minify

## Use

The simplest use of the library comes down to this:

    $minifiedJs = \Garfix\JsMinify\Minifier::minify($js);

Where `$js` contains the unprocessed code and `$minifiedJs` holds the minified version. 

If you want to change the default options, use `minify($js, $options)`, where `$options` is an array of one or more of the following:

* `\Garfix\JsMinify\Minifier::FLAGGED_COMMENTS` (bool, default: `true`) When set to `false`, `/*! ... */` flagged comments are removed as well.

## Background

I started this library because I believed [JShrink](https://github.com/tedious/JShrink) could be made much faster by the use of dedicated regular expressions. This turned out to be true. It is about 10x faster on PHP 7 and 5x faster on PHP 8.
