<?php

declare(strict_types=1);

namespace NITSAN\NsT3afExtended\Mcp;

use NITSAN\NsT3AF\Contract\McpToolsExtensionCardDescriptor;
use NITSAN\NsT3AF\Contract\McpToolsExtensionCardProviderInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * MCP Tools backend card for EXT:ns_t3af_extended.
 */
final class T3afExtendedMcpToolsExtensionCardProvider implements McpToolsExtensionCardProviderInterface
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

    public function getCardDescriptor(): McpToolsExtensionCardDescriptor
    {
        return new McpToolsExtensionCardDescriptor(
            label: 'T3AF Extended',
            icon: '🧪',
            iconIdentifier: 'actions-code',
            tagline: 'Reference MCP tools for AI Foundation integrators — echo (non-AI) and summarize (AI).',
            skillName: 'T3AF Extended Assistant',
            skillTrigger: '/t3af-extended',
            skillFile: 't3af-extended-skill.md',
            skillDesc: 'Sample MCP tools shipped with EXT:ns_t3af_extended.',
            toolPrefix: 't3af_extended_',
            sortPriority: 5,
        );
    }
}
