<?php

declare(strict_types=1);

namespace PrestaShop\CodingStandards\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CsFixerInitCommand extends AbstractCommand
{
    protected function configure(): void
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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $directory = __DIR__ . '/../../templates/cs-fixer/';
        $destination = $input->getOption('dest');

        // Try to delete the old dist file
        $this->deleteFile($destination . '/.php_cs.dist');

        // Create config file
        foreach (['.php-cs-fixer.dist.php'] as $template) {
            $this->copyFile(
                $input,
                $output,
                $directory . $template,
                $destination . '/' . $template
            );
        }

        return 0;
    }
}
