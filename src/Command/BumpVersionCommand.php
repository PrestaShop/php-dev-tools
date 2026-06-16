<?php

declare(strict_types=1);

namespace PrestaShop\CodingStandards\Command;

use Composer\Semver\VersionParser;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class BumpVersionCommand extends AbstractCommand
{

    const SEMVER_TYPES = ['patch', 'minor', 'major'];

    protected function configure(): void
    {
        $this->setName('bump-version')
            ->setDescription('Bump the version number')
            ->addArgument(
                'type',
                InputArgument::REQUIRED,
                'Version bump type: ' . implode(', ', self::SEMVER_TYPES)
            )
            ->addOption(
                'path',
                'p',
                InputOption::VALUE_REQUIRED,
                'Path to the module directory',
                '.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $type = $input->getArgument('type');
        $modulePath = $input->getOption('path');

        if (!in_array($type, self::SEMVER_TYPES)) {
            $output->writeln('<error>Invalid version type. Valid values are: ' . implode(', ', self::SEMVER_TYPES) . '</error>');
            return self::INVALID;
        }

        // Check that we can find the main module file
        $mainModuleFile = $this->findMainModuleFile($modulePath);
        if (!$mainModuleFile) {
            $output->writeln('<error>Could not find main module file</error>');
            return self::FAILURE;
        }

        // Get current version from the module file
        $currentVersion = $this->getCurrentVersion($mainModuleFile);
        if (!$currentVersion) {
            $output->writeln('<error>Could not find current version in module file</error>');
            return self::FAILURE;
        }

        // Strip to core version (major.minor.patch) if needed
        $originalVersion = $currentVersion;
        if (preg_match('/^(\d+\.\d+\.\d+)/', $currentVersion, $matches)) {
            $currentVersion = $matches[1];
            if ($currentVersion !== $originalVersion) {
                $output->writeln(sprintf('<comment>Stripped version from %s to %s</comment>', $originalVersion, $currentVersion));
            }
        }

        // Validate using composer/semver
        $parser = new VersionParser();
        try {
            $parser->normalize($currentVersion);
        } catch (\UnexpectedValueException $e) {
            $output->writeln(sprintf('<error>Invalid semantic version in module: %s</error>', $currentVersion));
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            return self::FAILURE;
        }

        $output->writeln(sprintf('<info>Current version: %s</info>', $currentVersion));

        // Bump the version
        $newVersion = $this->bumpVersion($currentVersion, $type);
        $output->writeln(sprintf('<info>New version: %s</info>', $newVersion));

        // Update module PHP file
        $this->updateModuleFile($mainModuleFile, $currentVersion, $newVersion);
        $output->writeln(sprintf('<info>Updated %s</info>', basename($mainModuleFile)));

        // Update config.xml if it exists
        $configFile = dirname($mainModuleFile) . '/config.xml';
        if (file_exists($configFile)) {
            $configVersion = $this->getConfigVersion($configFile);
            if ($configVersion && $configVersion !== $currentVersion) {
                $output->writeln(sprintf('<comment>Warning: config.xml version (%s) differs from module version (%s)</comment>', $configVersion, $currentVersion));
                // Force update by using the old version
                $this->updateConfigFile($configFile, $configVersion, $newVersion);
            } else {
                // Versions match, update normally
                $this->updateConfigFile($configFile, $currentVersion, $newVersion);
            }
            $output->writeln('<info>Updated config.xml</info>');
        } else {
            $output->writeln('<comment>config.xml not found, skipping</comment>');
        }

        $output->writeln(sprintf('<info>Successfully bumped version from %s to %s</info>', $currentVersion, $newVersion));

        return self::SUCCESS;
    }

    private function findMainModuleFile(string $path): ?string
    {
        $fs = new Filesystem();

        // Look for PHP files in the directory that match the directory name
        $dirName = basename(realpath($path));
        $possibleFile = $path . '/' . $dirName . '.php';

        if ($fs->exists($possibleFile)) {
            return $possibleFile;
        }

        // If not found, look for any PHP file that extends Module class
        $files = glob($path . '/*.php');
        foreach ($files as $file) {
            try {
                $content = file_get_contents($file);
                if (preg_match('/class\s+\w+\s+extends\s+Module/i', $content)) {
                    return $file;
                }
            } catch (\Exception $e) {
                // Skip files that can't be read
                continue;
            }
        }

        return null;
    }

    private function getCurrentVersion(string $filePath): ?string
    {
        try {
            $content = file_get_contents($filePath);
        } catch (\Exception $e) {
            return null;
        }

        // Extract whatever is in $this->version = '...'
        if (preg_match('/\$this->version\s*=\s*[\'"]([^\'"]+)[\'"]/', $content, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    private function getConfigVersion(string $filePath): ?string
    {
        try {
            $content = file_get_contents($filePath);
        } catch (\Exception $e) {
            return null;
        }

        // Extract version from <version>...</version> or <version><![CDATA[...]]></version>
        if (preg_match('/<version>(?:<!\[CDATA\[)?([^\]<]+)(?:\]\]>)?<\/version>/', $content, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    private function bumpVersion(string $version, string $type): string
    {
        // Parse version components (already validated by normalize())
        list($major, $minor, $patch) = explode('.', $version);
        $major = (int)$major;
        $minor = (int)$minor;
        $patch = (int)$patch;

        // Apply semver bump
        switch ($type) {
            case 'major':
                $major++;
                $minor = 0;
                $patch = 0;
                break;
            case 'minor':
                $minor++;
                $patch = 0;
                break;
            case 'patch':
                $patch++;
                break;
        }

        return sprintf('%d.%d.%d', $major, $minor, $patch);
    }

    private function updateModuleFile(string $filePath, string $oldVersion, string $newVersion): void
    {
        try {
            $content = file_get_contents($filePath);

            // Update $this->version
            $content = preg_replace(
                '/(\$this->version\s*=\s*[\'"])' . preg_quote($oldVersion, '/') . '([\'"])/i',
                '${1}' . $newVersion . '${2}',
                $content
            );

            file_put_contents($filePath, $content);
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf('Failed to update module file: %s', $e->getMessage()), 0, $e);
        }
    }

    private function updateConfigFile(string $filePath, string $oldVersion, string $newVersion): void
    {
        try {
            $content = file_get_contents($filePath);

            // Update <version>x.x.x</version> or <version><![CDATA[x.x.x]]></version>
            $content = preg_replace(
                '/<version>(<!\[CDATA\[)?' . preg_quote($oldVersion, '/') . '(\]\]>)?<\/version>/i',
                '<version>${1}' . $newVersion . '${2}</version>',
                $content
            );

            file_put_contents($filePath, $content);
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf('Failed to update config file: %s', $e->getMessage()), 0, $e);
        }
    }
}
