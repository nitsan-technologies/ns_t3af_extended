<?php

declare(strict_types=1);

namespace NITSAN\NsT3afExtended\Mcp\Tool;

use const JSON_THROW_ON_ERROR;

use Mcp\Capability\Attribute\McpTool;
use NITSAN\NsT3AF\Mcp\Attribute\McpToolOwner;
use NITSAN\NsT3AF\Mcp\Contract\McpNonAiToolInterface;
use NITSAN\NsT3AF\Settings\ExtensionSettingsService;

/**
 * Sample non-AI MCP tool for EXT:ns_t3af_extended.
 */
final readonly class EchoTool implements McpNonAiToolInterface
{
    public function __construct(
        private ExtensionSettingsService $extensionSettingsService,
    ) {}

    /**
     * Return a greeting echo (reads greeting prefix from AI Features settings when available).
     *
     * @param string $name Name to greet.
     */
    #[McpTool(
        name: 't3af_extended_echo',
        description: 'Returns a greeting echo for integrator testing (non-AI demo tool).',
    )]
    #[McpToolOwner(extensionKey: 'ns_t3af_extended')]
    public function execute(string $name = 'integrator'): string
    {
        $prefix = 'Hello from T3AF Extended';
        try {
            $settings = $this->extensionSettingsService->getAll('ns_t3af_extended', 0);
            if (isset($settings['greetingPrefix']) && trim((string) $settings['greetingPrefix']) !== '') {
                $prefix = trim((string) $settings['greetingPrefix']);
            }
        } catch (\Throwable) {
            // Settings service unavailable — use static prefix.
        }

        return json_encode([
            'message' => $prefix . ', ' . trim($name) . '!',
            'extension' => 'ns_t3af_extended',
            'tool' => 't3af_extended_echo',
        ], JSON_THROW_ON_ERROR);
    }
}
