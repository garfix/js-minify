<?php

namespace Garfix\JsMinify\Test;

use Garfix\JsMinify\Minifier;
use PHPUnit\Framework\TestCase;

class MinifierTest extends TestCase
{
    /**
     * @group unit
     */
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

    /**
     * @group libraries
     */
    public function testRealLibraries()
    {
        $libraries = [
            ['https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.js', 28, 0.025],
            ['https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.js', 51, 0.021],
            ['https://cdnjs.cloudflare.com/ajax/libs/babel-standalone/6.26.0/babel.js', 66.5, 0.16]
        ];

        echo "\n";

        foreach ($libraries as $library) {
            list($url, $maxPercentage, $maxDuration) = $library;
            $js = file_get_contents($url);

            $minifiedCode = Minifier::minify($js);
            $percentage = (strlen($minifiedCode) / strlen($js)) * 100;

            $duration = 1000;
            // execution time: best of 5
            for ($i = 0; $i < 5; $i++) {
                $start = microtime(true);
                Minifier::minify($js);
                $end = microtime(true);
                $duration = min($duration, $end - $start);
            }

            echo $url . ": percentage = " . sprintf("%0.2f", $percentage) . "; duration = " . sprintf("%0.3f", $duration) . ")\n";

            $this->assertNotEquals(0, $percentage);
            $this->assertNotEquals(100, $percentage);
            $this->assertLessThanOrEqual($maxDuration, $duration);
            $this->assertLessThanOrEqual($maxPercentage, $percentage);
        }
    }
}
