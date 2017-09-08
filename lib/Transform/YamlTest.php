<?php
declare(strict_types=1);
namespace PhpPreprocessor\Transform;

use PHPUnit\Framework\TestCase;
use PhpPreprocessor\Transform;
use Symfony\Component\Yaml\Yaml as YamlParser;

class YamlTest extends Testcase
{
    public static function provideTransformSuccess(): array
    {
        $tests = [
            ['[1, 2, "abc"]', []],
        ];
        $result = [];
        foreach ($tests as $test) {
            list($yaml, $args) = $test;
            $result[] = [$yaml, YamlParser::parse($yaml, ...$args), new Yaml(Transform::POSTPROCESS, ...$args)];
            $result[] = [$yaml, YamlParser::parse($yaml, ...$args), new Yaml(Transform::PREPROCESS, ...$args)];
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
    public function testTransformSuccess(string $yaml, $expected, Yaml $transform)
    {
        $code = $transform->transform($yaml);
        $this->assertEquals($expected, eval('?>' . $code));
    }

    /**
     * @dataProvider provideTransformFailures
     * @expectedException Symfony\Component\Yaml\Exception\ParseException
     */
    public function testTransformPreProcessFailure(string $yaml)
    {
        $transform = new Yaml(Transform::PREPROCESS);
        $code = $transform->transform($yaml);
    }

    /**
     * @dataProvider provideTransformFailures
     * @expectedException Symfony\Component\Yaml\Exception\ParseException
     */
    public function testTransformPostProcessFailure(string $yaml)
    {
        try {
            $transform = new Yaml(Transform::POSTPROCESS);
            $code = $transform->transform($yaml);
        } catch (\Throwable $e) {
            $this->fail("Unexpected exception occurred: " . $e);
        }
        eval('?>' . $code);
    }
}
