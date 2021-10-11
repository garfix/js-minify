<?php

namespace Garfix\JsMinify\Test;

use Garfix\JsMinify\Minifier;
use PHPUnit\Framework\TestCase;

class MinifierTest extends TestCase
{
    public function testJsMinify()
    {
        $baseDir = __DIR__ . '/../../../resources/';

        $testFiles = glob($baseDir . '*.input.js');
        foreach ($testFiles as $inputFile) {
            $outputFile = str_replace('input.js', 'output.js', $inputFile);
            $testInput = file_get_contents($inputFile);
            $testOutput = file_get_contents($outputFile);

            $output = Minifier::minify($testInput);

            $this->assertEquals($testOutput, $output, $outputFile);
        }
    }
}
