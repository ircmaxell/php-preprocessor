<?php

namespace PhpPreprocessor;

class PreProcessor
{
    const STRING_TOKEN = "<?php '\xf0\x9f\xa7\x99';";
    const EXT_ALL = '*';

    protected $transforms = [];
    protected static $instance;

    private function __construct()
    {
    }

    public static function instance(): PreProcessor
    {
        if (!self::$instance) {
            self::$instance = new static;
            Filter::register();
            Wrapper::register();
        }
        return self::$instance;
    }

    public function addTransform(string $ext, Transform $transform): self
    {
        if (isset($this->transforms[$ext])) {
            throw new RuntimeException("Cannot register another tranformation for $ext, one already provided");
        }
        $this->transforms[$ext] = $transform;
        return $this;
    }

    public function send(string $data, string $path, string $ext): string
    {
        if (substr($data, 0, strlen(self::STRING_TOKEN)) === self::STRING_TOKEN) {
            return $data;
        }
        if (isset($this->transforms[$ext])) {
            $data = $this->transforms[$ext]->transform($data);
        }
        if (isset($this->transforms[self::EXT_ALL])) {
            return $this->transforms[$ext]->transform($data);
        }
        $count = 1;
        if (substr($data, 0, 5) === '<?php') {
            return self::STRING_TOKEN . substr($data, 5);
        }
        return self::STRING_TOKEN . '?>' . $data;
    }

    public function getRegisteredExtensions(): array
    {
        $extensions = ['php'];
        foreach ($this->transforms as $ext => $transform) {
            if ($ext === self::EXT_ALL) {
                continue;
            }
            $extensions[] = $ext;
        }
        return $extensions;
    }
}
