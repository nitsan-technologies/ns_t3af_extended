<?php

declare(strict_types=1);

namespace NITSAN\NsT3afExtended\Service\Ai;

use NITSAN\NsT3AF\Prompt\AiPromptRepository;

/**
 * Resolves demo prompt text: explicit → DB by title → built-in contract.
 */
final class PromptResolver
{
    private const EXTENSION_KEY = 'ns_t3af_extended';

    public function __construct(
        private readonly PromptContractRegistry $promptContractRegistry,
        private readonly AiPromptRepository $aiPromptRepository,
    ) {}

    public function resolveText(
        string $promptType,
        ?string $explicitPromptText = null,
        ?string $configuredPromptTitle = null,
    ): string {
        if ($explicitPromptText !== null && trim($explicitPromptText) !== '') {
            return trim($explicitPromptText);
        }

        if ($configuredPromptTitle !== null && trim($configuredPromptTitle) !== '') {
            $fromDatabase = $this->aiPromptRepository->findPromptTextByTypeAndTitle(
                self::EXTENSION_KEY,
                $this->promptContractRegistry->getScope($promptType),
                $promptType,
                trim($configuredPromptTitle),
                0,
            );
            if ($fromDatabase !== null && $fromDatabase !== '') {
                return $fromDatabase;
            }
        }

        return $this->promptContractRegistry->getDefaultText($promptType);
    }
}
