<?php

declare(strict_types=1);

namespace NITSAN\NsT3afExtended\Mcp\Tool;

use const JSON_THROW_ON_ERROR;

use Mcp\Capability\Attribute\McpTool;
use NITSAN\NsT3afExtended\Service\Ai\PromptResolver;
use NITSAN\NsT3afExtended\Service\T3afExtendedAiService;
use NITSAN\NsT3AF\Mcp\Attribute\McpToolOwner;
use NITSAN\NsT3AF\Mcp\Contract\McpToolHandlerInterface;

/**
 * Sample AI MCP tool calling {@see T3afExtendedAiService} / {@see AiServiceInterface}.
 */
final readonly class SummarizeTool implements McpToolHandlerInterface
{
    public function __construct(
        private T3afExtendedAiService $t3afExtendedAiService,
        private PromptResolver $promptResolver,
    ) {}

    /**
     * Summarize input text using the configured demo prompt and AI provider.
     *
     * @param string $text Text to summarize.
     * @param string $tone Summary tone (concise or detailed).
     */
    #[McpTool(
        name: 't3af_extended_summarize',
        description: 'Summarize text using AI Foundation (demo tool for EXT:ns_t3af_extended).',
    )]
    #[McpToolOwner(extensionKey: 'ns_t3af_extended')]
    public function execute(string $text, string $tone = 'concise'): string
    {
        $text = trim($text);
        if ($text === '') {
            return json_encode(['error' => 'Text must not be empty.'], JSON_THROW_ON_ERROR);
        }

        $template = $this->promptResolver->resolveText('extended_summary');
        $prompt = str_replace(
            ['[tone]', '[language]', '[input]'],
            [trim($tone) !== '' ? trim($tone) : 'concise', 'English', $text],
            $template,
        );

        try {
            $summary = $this->t3afExtendedAiService->summarize($prompt);
        } catch (\Throwable $e) {
            return json_encode([
                'error' => $e->getMessage(),
                'hint' => 'Configure a provider under AI Foundation → Providers and flush caches.',
            ], JSON_THROW_ON_ERROR);
        }

        return json_encode([
            'summary' => $summary,
            'tone' => $tone,
            'extension' => 'ns_t3af_extended',
            'tool' => 't3af_extended_summarize',
        ], JSON_THROW_ON_ERROR);
    }
}
