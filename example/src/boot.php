<?php
use PhpPreprocessor\PreProcessor;
use PhpPreprocessor\Transform\Json;
use PhpPreprocessor\Transform\Yaml;

PreProcessor::instance()
    ->addTransform('json', new Json)
    ->addTransform('yaml', new Yaml);