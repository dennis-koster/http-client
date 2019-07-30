<?php

namespace Eekhoorn\PhpSdk\Tests\Unit;

use Eekhoorn\PhpSdk\JsonApiParser;
use PHPUnit\Framework\TestCase;

class JsonApiParserTest extends TestCase
{
    /** 
     * @test 
     */
    public function it_throws_an_exception_when_parsing_invalid_json()
    {
        $string = 'foo-bar-non-json';
        $jsonParser = new JsonApiParser([]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Could not parse given json data');
        $jsonParser->parse($string);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_parsing_incomplete_json()
    {
        $string = '{"foo": "bar"}';
        $jsonParser = new JsonApiParser([]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Could not parse given json data');
        $jsonParser->parse($string);
    }

}
