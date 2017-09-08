<?php
declare(strict_types=1);
namespace PhpPreprocessor\Transform;

use PhpPreprocessor\Transform;

class Json implements Transform
{
    protected $assoc;
    protected $depth;
    protected $options;
    protected $process;

    public function __construct(int $process = Transform::POSTPROCESS, bool $assoc = false, int $depth = 512, int $options = 0)
    {
        $this->assoc = $assoc;
        $this->depth = $depth;
        $this->options = $options;
        $this->process = $process;
    }

    public function transform(string $data): string
    {
        if ($this->process & Transform::PREPROCESS) {
            $parsed = json_decode($data, $this->assoc, $this->depth, $this->options);
            return "<?php return unserialize(" . var_export(serialize($parsed), true) . ");";
        }
        $code = "<?php return json_decode(";
        $code .= var_export($data, true);
        $code .= ", " . var_export($this->assoc, true);
        $code .= ", " . var_export($this->depth, true);
        $code .= ", " . var_export($this->options, true);
        $code .= ");";
        return $code;
    }
}
