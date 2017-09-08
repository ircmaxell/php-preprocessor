<?php

namespace PhpPreprocessor;

interface Transform {
    const POSTPROCESS = 0x01;
    const PREPROCESS  = 0x02;

    public function transform(string $data): string;

}