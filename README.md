# PHP PreProcessor

What is this? It was an attempt at pre-processing of `include/require`'d files in PHP.

TLDR: Juse use [preprocess.io](https://preprocess.io)...

## How does it work

Dark magic and voodoo. Don't worry about it

## Included Transformations

 * `Json` - For including `.json` files

    This transformation will let you load json files by simply calling `$data = require 'path/to/data.json';`. The `$data` that's returned is automatically parsed.

 * `Yaml` - For including `.yaml` files

    This transformation will automatically load yaml files for you.

## How to enable

Create a base PHP file to declare your included transformations.

*boot.php*

    <?php
    use PhpPreprocessor\PreProcessor;

Then, to enable parsing of `.json` files with the `Json` transformation, add it to the preprocessor

    PreProcessor::instance()
        ->addTransform('json', new PhpPreprocessor\Transform\Json);

You can add multiple transforms together:

    PreProcessor::instance()
        ->addTransform('json', new PhpPreprocessor\Transform\Json)
        ->addTransform('yaml', new PhpPreprocessor\Transform\Yaml);

Finally, define an autoload file in your composer.json:

    "autoload": {
        "files": [
            "path/to/boot.php"
        ]
    }

Now, just use it!

## Important Notes

* You can only install one transformation per file extension. Additional or redeclarations will result in an exception.

* You can declare a "global" transformation that will be run on all included files (after a prior transformation is run if matched). Use the `PreProcessor::EXT_ALL` constant in place of an extension.

## To Save Runtime Cost

All of this parsing happens at runtime, which can be expensive. This is useful for development, but what about production? 

You can pre-process your codebase using the cli tool `preprocessor`. This will rewrite every file with a matching processer extension (and `php` files) using the transforms and move them into a new build directory. Non-matching files are copied into the new directory without processing.

A token is included in the processed file `<?php '🧙';` to prevent re-processing of the file in a production environment.

    $ vendor/bin/preprocessor build srcDir destDir

You can also specify a "boot file" which declares the transforms as a third argument:

    $ vendor/bin/preprocessor build srcDir destDir srcDir/path/to/boot.php

## How do I build transformations?

Simply build a class that implements the `PhpPreprocessor\Transform` interface:

    use PhpPreprocessor\Transform;
    class MyTransform implements Transform {
        public function transform(string $data): string
        {

        }
    }

The `transform` method will be called for every matched file, with the `$data` parameter containing the included file contents.

The returned string **MUST** be valid PHP code.

## Pre vs Post Parsing

`Json` is a good example of the tradeoffs of parsing location. Should you parse the JSON in the transformation and export the result, or should you render the parsing code. The `Json` transform does both optionally.

The reason this is important is because of error handling. The `Yaml` parser throws an exception on invalid YAML. Using `Transform::PREPROCESS` means that the exception will happen when building (if you're using the build tool). Using `Transform::POSTPROCESS` will always have the exception occur in the `require` call.

## Credits:

 * Loading mechanism was heavily inspired and partially derived by [Patchwork](https://github.com/antecedent/patchwork/)
 * Also heavily dependent and derived from [Php-Parser](https://github.com/nikic/php-parser)...

## Status of the project

Right now, it's just a proof-of-concept. Because of that, use [preprocess.io](https://preprocess.io) if you want similar functionality...
