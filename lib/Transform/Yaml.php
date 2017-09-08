<?php
declare(strict_types=1);
namespace PhpPreprocessor\Transform;

use PhpPreprocessor\Transform;
use Symfony\Component\Yaml\Yaml as YamlParser;

class Yaml implements Transform
{
    protected $flags;
    protected $process;

    public function __construct(int $process = Transform::POSTPROCESS, int $flags = 0)
    {
        $this->flags = $flags;
        $this->process = $process;
    }

    public function transform(string $data): string
    {
        if ($this->process & Transform::PREPROCESS) {
            $parsed = YamlParser::parse($data, $this->flags);
            return "<?php return unserialize(" . var_export(serialize($parsed), true) . ");";
        }

        $code = "<?php return Symfony\\Component\\Yaml\\Yaml::parse(";
        $code .= var_export($data, true);
        $code .= ", " . var_export($this->flags, true);
        $code .= ");";
        return $code;
    }
}
