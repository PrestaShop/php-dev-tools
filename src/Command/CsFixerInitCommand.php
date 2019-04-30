<?php

namespace PrestaShop\CodingStandards\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class CsFixerInitCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('cs-fixer:init')
            ->setDescription('Initialize Cs Fixer environement')
            ->addOption(
                'dest',
                null,
                InputOption::VALUE_REQUIRED,
                'Where the configuration will be stored',
                '.' // Current directory
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fs = new Filesystem();
        $directory = __DIR__ . '/../../templates/cs-fixer/';
        $destination = $input->getOption('dest');

        $phpcsDistFile = 'php_cs.dist';
        $phpcsDistDestination = $destination . '/.' . $phpcsDistFile;

        $this->copyFile(
            $input,
            $output,
            $directory . $phpcsDistFile,
            $phpcsDistDestination
        );
    }
}
