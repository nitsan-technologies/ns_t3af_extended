<?php

declare(strict_types=1);

namespace NITSAN\NsT3afExtended\Feature;

use NITSAN\NsT3AF\Contract\AiFeatureCardDescriptor;
use NITSAN\NsT3AF\Contract\AiFeatureCardProviderInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Sample {@see AiFeatureCardProviderInterface} for EXT:ns_t3af_extended.
 */
final class T3afExtendedAiFeatureCardProvider implements AiFeatureCardProviderInterface
{
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

    public function getFeatureCards(): array
    {
        return [
            new AiFeatureCardDescriptor(
                id: 't3af-extended-ai',
                name: 'T3AF Extended',
                subtitle: 'Reference AI Features integration',
                extKey: self::EXTENSION_KEY,
                settingsScope: self::SETTINGS_SCOPE,
                icon: 'actions-code',
                iconBg: 'aiu-feature-card__icon--emerald',
                iconColor: 'aiu-feature-card__glyph--emerald',
                tags: ['ns_t3af_extended', 'developer', 'demo', 'reference', 'ai'],
                description: 'Per-site toggles, summary tone, greeting prefix, and optional per-feature AI provider override.',
                sortPriority: 10,
                wizardEligible: true,
                wizardToggleField: 'enableExtendedAi',
            ),
        ];
    }
}
