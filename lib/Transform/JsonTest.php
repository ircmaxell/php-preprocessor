<?php
declare(strict_types=1);
namespace PhpPreprocessor\Transform;

use PHPUnit\Framework\TestCase;
use PhpPreprocessor\Transform;

class JsonTest extends Testcase {

    public static function provideTransformSuccess(): array
    {
        $tests = [
            ['[1, 2, "abc"]', []],
            ['{"foo": 123, "bar": 12.2}', []],
            ['"something"', []],
            ['{"foo": 123, "bar": 12.2}', [true]],
            ['{"foo": 123, "bar": 12.2, "baz": {"a": {"b"}}}', [true, 1]],
            ['{"foo": 123, "bar": 12.2, "baz": {"a": {"b"}}}', [false, 1, JSON_OBJECT_AS_ARRAY]],
        ];
        $result = [];
        foreach ($tests as $test) {
            list($json, $args) = $test;
            $result[] = [$json, json_decode($json, ...$args), new Json(Transform::POSTPROCESS, ...$args)];
            $result[] = [$json, json_decode($json, ...$args), new Json(Transform::PREPROCESS, ...$args)];
        }
        return $result;
    }

    public static function provideTransformFailures(): array
    {
        return [
            ["["],
            ["'a"],
        ];
    }

    /**
     * @dataProvider provideTransformSuccess
     */
    public function testTransformSuccess(string $json, $expected, Json $transform)
    {
        $code = $transform->transform($json);
        $this->assertEquals($expected, eval('?>' . $code));
    }

    /**
     * @dataProvider provideTransformFailures
     */
    public function testTransformPreProcessFailure(string $json)
    {
        $transform = new Json(Transform::PREPROCESS);
        $code = $transform->transform($json);
        $this->assertNull(eval('?>' . $code));
    }

    /**
     * @dataProvider provideTransformFailures
     */
    public function testTransformPostProcessFailure(string $json)
    {
        $transform = new Json(Transform::POSTPROCESS);
        $code = $transform->transform($json);
        $this->assertNull(eval('?>' . $code));
    }

}