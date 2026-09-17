<?php

declare(strict_types=1);

namespace NITSAN\NsT3afExtended\Mcp\Tool;

use const JSON_THROW_ON_ERROR;

use Mcp\Capability\Attribute\McpTool;
use NITSAN\NsT3afExtended\Service\T3afExtendedSummarizeAndBindService;
use NITSAN\NsT3AF\Mcp\Attribute\McpToolOwner;
use NITSAN\NsT3AF\Mcp\Contract\McpToolHandlerInterface;

/**
 * Working AI Label demo: summarize into an existing tt_content record and bind.
 */
final readonly class SummarizeContentTool implements McpToolHandlerInterface
{
    public function __construct(
        private T3afExtendedSummarizeAndBindService $summarizeAndBindService,
    ) {}

    /**
     * Summarize text into an existing content element and bind AI Label.
     *
     * @param int $contentUid Existing tt_content uid.
     * @param string $text Source text. Empty uses the record's current bodytext.
     * @param string $tone Summary tone (concise or detailed).
     */
    #[McpTool(
        name: 't3af_extended_summarize_content',
        description: 'Summarize into an existing tt_content element and bind AI Label (demo for EXT:ns_t3af_extended).',
    )]
    #[McpToolOwner(extensionKey: 'ns_t3af_extended')]
    public function execute(int $contentUid, string $text = '', string $tone = 'concise'): string
    {
        try {
            $result = $this->summarizeAndBindService->summarizeAndBind($contentUid, $text, $tone);
        } catch (\Throwable $e) {
            return json_encode([
                'error' => $e->getMessage(),
                'hint' => 'Enable demo AI features, configure a provider, and pass a valid tt_content uid.',
            ], JSON_THROW_ON_ERROR);
        }

        return json_encode([
            'uid' => $result['uid'],
            'summary' => $result['summary'],
            'bound' => $result['bound'],
            'tone' => $tone,
            'extension' => 'ns_t3af_extended',
            'tool' => 't3af_extended_summarize_content',
        ], JSON_THROW_ON_ERROR);
    }
}
