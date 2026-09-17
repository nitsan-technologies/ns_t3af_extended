<?php

declare(strict_types=1);

namespace NITSAN\NsT3afExtended\Service;

use NITSAN\NsT3afExtended\AiLabel\T3afExtendedAiLabelBinder;
use NITSAN\NsT3afExtended\Service\Ai\PromptResolver;
use NITSAN\NsT3AF\AiLabel\Service\AiLabelBindHelper;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Demo persist path: summarize via {@see T3afExtendedAiService}, write
 * tt_content.bodytext with DataHandler, then bind AI Label in the same request.
 */
final class T3afExtendedSummarizeAndBindService
{
    public function __construct(
        private readonly T3afExtendedAiService $t3afExtendedAiService,
        private readonly T3afExtendedAiLabelBinder $aiLabelBinder,
        private readonly PromptResolver $promptResolver,
    ) {}

    /**
     * @return array{uid: int, summary: string, bound: bool}
     */
    public function summarizeAndBind(int $contentUid, string $text = '', string $tone = 'concise'): array
    {
        if ($contentUid <= 0) {
            throw new \InvalidArgumentException('contentUid must be a positive tt_content uid.', 1758102000);
        }

        $record = BackendUtility::getRecord('tt_content', $contentUid);
        if (!is_array($record)) {
            throw new \RuntimeException(sprintf('Content element uid %d was not found.', $contentUid), 1758102001);
        }

        $source = trim($text);
        if ($source === '') {
            $source = trim(html_entity_decode(
                strip_tags((string) ($record['bodytext'] ?? '')),
                ENT_QUOTES | ENT_HTML5,
            ));
        }
        if ($source === '') {
            throw new \RuntimeException(
                'No text to summarize. Pass text or use a content element that already has bodytext.',
                1758102002,
            );
        }

        $tone = trim($tone) !== '' ? trim($tone) : 'concise';
        $template = $this->promptResolver->resolveText('extended_summary');
        $prompt = str_replace(
            ['[tone]', '[language]', '[input]'],
            [$tone, 'English', $source],
            $template,
        );

        $summary = $this->t3afExtendedAiService->summarize($prompt);
        $this->writeBodytext($contentUid, $summary);
        $this->aiLabelBinder->bindContentRecord($contentUid);

        return [
            'uid' => $contentUid,
            'summary' => $summary,
            'bound' => class_exists(AiLabelBindHelper::class),
        ];
    }

    private function writeBodytext(int $uid, string $bodytext): void
    {
        if (!isset($GLOBALS['BE_USER']) || !is_object($GLOBALS['BE_USER'])) {
            throw new \RuntimeException('No backend user available for DataHandler.', 1758102003);
        }

        $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $dataHandler->start(
            ['tt_content' => [$uid => ['bodytext' => $bodytext]]],
            [],
            $GLOBALS['BE_USER'],
        );
        $dataHandler->process_datamap();

        if ($dataHandler->errorLog !== []) {
            throw new \RuntimeException(implode('; ', $dataHandler->errorLog), 1758102004);
        }
    }
}
