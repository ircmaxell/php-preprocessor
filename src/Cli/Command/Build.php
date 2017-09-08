<?php
namespace PhpPreprocessor\Cli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use PhpPreprocessor\Builder;

class Build extends Command
{

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('build')
            ->setDescription('PreProcess all files in a directory ahead of time')
            ->addArgument('src', InputArgument::REQUIRED, 'The source directory')
            ->addArgument('dest', InputArgument::REQUIRED, 'The destination directory')
            ->addArgument('boot', InputArgument::OPTIONAL, 'The boot file to run prior to executing')
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $errOutput = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;
        $src = $input->getArgument('src');
        $dest = $input->getArgument('dest');
        $boot = $input->getArgument('boot');
        if ($boot) {
            require $boot;
        }
        if (substr($dest, 0, 1) !== DIRECTORY_SEPARATOR) {
            $dest = realpath(getcwd()) . '/' . $dest;
        }
        if (!is_dir($src)) {
            $errOutput->writeln("Source directory src must exist");
            return 2;
        }
        $builder = new Builder;
        $builder->build($src, $dest);
        return 0;
    }
}
