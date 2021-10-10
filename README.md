# js-minify

A fast Javascript minifier that removes unnecessary whitespace and comments

## Use

The simplest use is simply this:

    $minifiedJs = \Garfix\JsMinify\Minifier::minify($js);

If you want to change the default options, use `minify($js, $options)`, where `$options` is an array of one or more of the following:

* \Garfix\JsMinify\Minifier::FLAGGED_COMMENTS (bool, default: true) When `true`, `/*! ... */` flagged comments are left in.

## Background

I started this library because I believed [JShrink](https://github.com/tedious/JShrink) could be made much faster by the use of dedicated regular expressions. This turned out to be true. It is about 10x faster on PHP 7 and 5x faster on PHP 8.
