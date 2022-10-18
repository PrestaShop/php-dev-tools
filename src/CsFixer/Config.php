<?php

declare(strict_types=1);

namespace PrestaShop\CodingStandards\CsFixer;

use PhpCsFixer\Config as BaseConfig;

class Config extends BaseConfig
{
    public function __construct($name = 'default')
    {
        parent::__construct('PrestaShop coding standard');

        $this->setRiskyAllowed(true);
    }

    public function getRules(): array
    {
        return [
            '@Symfony' => true,
            'concat_space' => [
                'spacing' => 'one',
            ],
            'cast_spaces' => [
                'space' => 'single',
            ],
            'error_suppression' => [
                'mute_deprecation_error' => false,
                'noise_remaining_usages' => false,
                'noise_remaining_usages_exclude' => [],
            ],
            'function_to_constant' => false,
            'visibility_required' => [
                'elements' => ['property', 'method'],
            ],
            'no_alias_functions' => false,
            'phpdoc_summary' => false,
            'phpdoc_align' => [
                'align' => 'left',
            ],
            'protected_to_private' => false,
            'psr_autoloading' => false,
            'self_accessor' => false,
            'yoda_style' => false,
            'non_printable_character' => true,
            'no_superfluous_phpdoc_tags' => false,
        ];
    }
}
