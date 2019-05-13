<?php

namespace PrestaShop\CodingStandards\Command;

use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
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
        if ($fs->exists($destination) && !$this->askForOverwrite($input, $output)) {
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
     * @param string $message
     * @param mixed $default
     *
     * @return bool
     */
    protected function askForOverwrite(
        InputInterface $input,
        OutputInterface $output,
        $message = 'Overwrite?',
        $default = false
    ) {
        $helper = $this->getHelper('question');
        $overwriteQuestion = new ConfirmationQuestion('Overwrite?', $default);
        if (!$helper->ask($input, $output, $overwriteQuestion)) {
            return false;
        }

        return true;
    }
}
