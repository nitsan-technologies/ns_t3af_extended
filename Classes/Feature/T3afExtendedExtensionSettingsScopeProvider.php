<?php

declare(strict_types=1);

namespace NITSAN\NsT3afExtended\Feature;

use NITSAN\NsT3AF\Contract\ExtensionSettingsScopeMessagesTrait;
use NITSAN\NsT3AF\Contract\ExtensionSettingsScopeProviderInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Sample {@see ExtensionSettingsScopeProviderInterface} for EXT:ns_t3af_extended.
 */
final class T3afExtendedExtensionSettingsScopeProvider implements ExtensionSettingsScopeProviderInterface
{
    use ExtensionSettingsScopeMessagesTrait;

    private const EXTENSION_KEY = 'ns_t3af_extended';

    private const SETTINGS_SCOPE = 't3af extended';

    public function isAvailable(): bool
    {
        return ExtensionManagementUtility::isLoaded(self::EXTENSION_KEY)
            && ExtensionManagementUtility::isLoaded('ns_t3af');
    }

    public function getExtensionKey(): string
    {
        return self::EXTENSION_KEY;
    }

    public function getAllowedScopes(): array
    {
        return [self::SETTINGS_SCOPE];
    }

    public function getCompositeScopeCategories(): array
    {
        return [];
    }

    public function getPaletteScopes(): array
    {
        return [];
    }

    public function getFieldFilterScopes(): array
    {
        return [];
    }
}
