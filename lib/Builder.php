<?php

namespace PhpPreprocessor;

use DirectoryIterator;

class Builder {

    protected $extensions = [];

    public function build(string $sourceDir, string $destinationDir)
    {
        $sourceDir = rtrim($sourceDir, '/' . DIRECTORY_SEPARATOR);
        $destinationDir = rtrim($destinationDir, '/' . DIRECTORY_SEPARATOR);

        $this->preprocessor = PreProcessor::instance();
        $this->extensions = $this->preprocessor->getRegisteredExtensions();

        $this->buildFolder($sourceDir, $destinationDir);
    }

    protected function buildFolder(string $sourceDir, string $destinationDir)
    {
        if (!is_dir($destinationDir)) {
            mkdir($destinationDir, 0777, true);
        }

        $it = new DirectoryIterator($sourceDir);
        foreach ($it as $file) {
            $destinationFile = $destinationDir . "/" . $file->getFilename();
            if ($file->isDot()) {
                continue;
            } elseif ($file->isDir()) {
                if (realpath($destinationDir) === realpath($file->getPathname())) {
                    // incase your build folder is inside of your source folder
                    continue;
                }
                $this->buildFolder($file->getPathname(), $destinationFile);
                continue;
            } elseif (!in_array($file->getExtension(), $this->extensions)) {
                copy($file->getPathname(), $destinationFile);
                continue;
            }
            $data = file_get_contents($file->getPathname());
            $data = $this->preprocessor->send($data, $file->getPathname(), $file->getExtension());
            file_put_contents($destinationFile, $data);
        }
    }

}