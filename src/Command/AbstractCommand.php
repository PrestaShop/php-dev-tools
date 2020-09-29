<?php

namespace PrestaShop\CodingStandards\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Filesystem\Filesystem;

abstract class AbstractCommand extends Command
{
    /**
     * Copy file, check if file exists.
     * If yes, ask for overwrite
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param string $source
     * @param string $destination
     */
    protected function copyFile(InputInterface $input, OutputInterface $output, $source, $destination)
    {
        $fs = new Filesystem();
        if ($fs->exists($destination) && !$this->askForOverwrite($input, $output, $source, $destination)) {
            return;
        }

        $fs->copy(
            $source,
            $destination
        );

        $output->writeln(
            sprintf(
                'File "%s" copied to "%s"',
                basename($source),
                $destination
            )
        );
    }

    /**
     * Ask for overwrite
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param string $source
     * @param string $destination
     * @param string $message
     * @param bool $default
     *
     * @return bool
     */
    protected function askForOverwrite(
        InputInterface $input,
        OutputInterface $output,
        $source,
        $destination,
        $message = null,
        $default = false
    ) {
        if (null === $message) {
            $availableOptionsText = $default ? '[Y/n]' : '[y/N]';
            $message = sprintf(
                '%s already exists in destination folder %s. Overwrite? %s ',
                pathinfo($source, PATHINFO_BASENAME),
                pathinfo(realpath($destination), PATHINFO_DIRNAME),
                $availableOptionsText
            );
        }
        $helper = $this->getHelper('question');
        $overwriteQuestion = new ConfirmationQuestion($message, $default);

        return $helper->ask($input, $output, $overwriteQuestion);
    }
}
