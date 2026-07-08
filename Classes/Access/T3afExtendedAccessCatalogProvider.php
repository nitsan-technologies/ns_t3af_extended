<?php

declare(strict_types=1);

namespace NITSAN\NsT3afExtended\Access;

use NITSAN\NsT3AF\Access\Dto\FeatureAccessBindingsDescriptor;
use NITSAN\NsT3AF\Access\Dto\FeaturePermissionDescriptor;
use NITSAN\NsT3AF\Access\Dto\ModuleAccessDescriptor;
use NITSAN\NsT3AF\Access\Dto\RecordPermissionDescriptor;
use NITSAN\NsT3AF\Contract\AiAccessCatalogProviderInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Sample {@see AiAccessCatalogProviderInterface} for EXT:ns_t3af_extended.
 */
final class T3afExtendedAccessCatalogProvider implements AiAccessCatalogProviderInterface
{
    private const EXTENSION_KEY = 'ns_t3af_extended';

    public function isAvailable(): bool
    {
        return ExtensionManagementUtility::isLoaded(self::EXTENSION_KEY)
            && ExtensionManagementUtility::isLoaded('ns_t3af');
    }

    public function getExtensionKey(): string
    {
        return self::EXTENSION_KEY;
    }

    public function getCatalogModuleKey(): string
    {
        return 't3af_extended';
    }

    public function getModuleAccess(): ?ModuleAccessDescriptor
    {
        return new ModuleAccessDescriptor(
            label: 'T3AF Extended',
            sublabel: 'Reference integration',
            description: 'Sample module card for AI Access / Roles wizard and be_groups custom options.',
            color: '#10b981',
            groupMod: 'nitsan_nst3afextended',
            extension: self::EXTENSION_KEY,
        );
    }

    public function getFeaturePermissions(): array
    {
        return [
            new FeaturePermissionDescriptor(
                id: 'extendedSummarize',
                label: 'Demo summarize',
                description: 'Allow AI summarization via demo MCP tool and services.',
                permBase: 'T3afExtended.Summarize',
                relevantModules: ['t3af_extended'],
                group: 't3af_extended',
                extension: self::EXTENSION_KEY,
            ),
            new FeaturePermissionDescriptor(
                id: 'extendedEcho',
                label: 'Demo echo',
                description: 'Allow non-AI echo MCP tool for integrator testing.',
                permBase: 'T3afExtended.Echo',
                relevantModules: ['t3af_extended'],
                group: 't3af_extended',
                extension: self::EXTENSION_KEY,
            ),
        ];
    }

    public function getRecordPermissions(): array
    {
        return [
            new RecordPermissionDescriptor(
                id: 'extendedContent',
                label: 'Content elements (demo ACL)',
                tables: ['tt_content'],
                relevantModules: ['t3af_extended'],
                relevantFeatures: ['extendedSummarize'],
                readHelp: 'View content elements when demo summarize is granted',
                writeHelp: 'Modify content elements when demo summarize is granted',
                extension: self::EXTENSION_KEY,
            ),
        ];
    }

    public function getFeatureAccessBindings(): FeatureAccessBindingsDescriptor
    {
        return new FeatureAccessBindingsDescriptor(
            moduleKey: 't3af_extended',
            legacyCardPermPrefix: 'tx_t3af_extended_',
            moduleGroupMod: 'nitsan_nst3afextended',
            defaultTabFeature: 'T3afExtended.Summarize',
            tabFeatureMap: [
                'summarize' => 'T3afExtended.Summarize',
                'echo' => 'T3afExtended.Echo',
            ],
        );
    }
}
