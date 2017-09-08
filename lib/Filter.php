<?php
declare(strict_types=1);

namespace PhpPreprocessor;

use php_user_filter;

class Filter extends php_user_filter {
    const NAME = "php-preprocessor";

    protected $buffer = '';

    public static function append($resource, string $path)
    {
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        stream_filter_append(
            $resource,
            self::NAME, 
            STREAM_FILTER_READ, 
            [
                "ext" => $ext,
                "path" => $path,
            ]
        );
    }

    public static function register()
    {
        stream_filter_register(self::NAME, static::class);
    }


    public function filter($in, $out, &$consumed, $closing)
    {
        while ($bucket = stream_bucket_make_writeable($in)) {
            $this->buffer .= $bucket->data;
            $consumed += $bucket->datalen;
        }
        if ($closing) {
            $buffer = PreProcessor::instance()->send($this->buffer, $this->params['path'], $this->params['ext']);
            $bucket = stream_bucket_new($this->stream, $buffer);
            stream_bucket_append($out, $bucket);
        }
        return PSFS_PASS_ON;
    }

}