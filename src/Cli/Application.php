<?php
namespace PhpPreprocessor\Cli;

use Cilex\Application as CoreApplication;
use DirectoryIterator;

class Application extends CoreApplication {

    public function __construct()
    {
        parent::__construct("preprocessor", "1.0");

        $this->loadAllCommands();
    }

    private function loadAllCommands()
    {
        $it = new DirectoryIterator(__DIR__ . '/Command');
        foreach ($it as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }
            $class = __NAMESPACE__ . '\\Command\\' . $file->getBasename('.php');
            $this->command(new $class);
        }
    }
}