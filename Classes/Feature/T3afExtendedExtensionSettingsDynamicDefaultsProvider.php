<?php

declare(strict_types=1);

namespace NITSAN\NsT3afExtended\Feature;

use NITSAN\NsT3AF\Contract\ExtensionSettingsDynamicDefaultsProviderInterface;

/**
 * Supplies dynamic default values for userFunc-backed settings fields.
 */
final class T3afExtendedExtensionSettingsDynamicDefaultsProvider implements ExtensionSettingsDynamicDefaultsProviderInterface
{
    public function getExtensionKey(): string
    {
        return 'ns_t3af_extended';
    }

    public function getDynamicDefaults(int $storagePid = 0): array
    {
        return [
            'greetingPrefix' => 'Hello from T3AF Extended (' . date('Y-m-d') . ')',
        ];
    }
}
