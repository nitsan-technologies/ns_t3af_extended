<?php

declare(strict_types=1);

namespace NITSAN\NsT3afExtended\Service;

use NITSAN\NsT3AF\Api\AiOptions;
use NITSAN\NsT3AF\Api\AiServiceInterface;
use NITSAN\NsT3AF\Settings\ExtensionSettingsService;
use NITSAN\NsT3AF\Utility\AiUniverseUtilityHelper;

/**
 * Demonstrates {@see AiServiceInterface} usage with analytics attribution from a child extension.
 */
final class T3afExtendedAiService
{
    private const EXTENSION_KEY = 'ns_t3af_extended';

    public function __construct(
        private readonly AiServiceInterface $aiService,
        private readonly ExtensionSettingsService $extensionSettingsService,
    ) {}

    public function summarize(string $prompt): string
    {
        $settings = $this->readSettings();
        if (($settings['enableExtendedAi'] ?? '0') !== '1') {
            throw new \RuntimeException('Demo AI is disabled. Enable it in AI Foundation → AI Features → T3AF Extended.');
        }

        $providerIdentifier = $this->resolveProviderIdentifier($settings);
        $tone = (string) ($settings['summaryTone'] ?? 'concise');

        $options = new AiOptions(
            providerIdentifier: $providerIdentifier,
            temperature: $tone === 'detailed' ? 0.5 : 0.2,
            maxTokens: $tone === 'detailed' ? 600 : 300,
            extensionKey: self::EXTENSION_KEY,
            featureKey: 'extended.summarize',
            featureLabel: 'T3AF Extended Summarize',
            requestSource: 'mcp_tool',
        );

        $response = $this->aiService->complete($prompt, $options);

        return $response->content;
    }

    /**
     * @return array<string, mixed>
     */
    private function readSettings(): array
    {
        try {
            return $this->extensionSettingsService->getAll(self::EXTENSION_KEY, 0);
        } catch (\Throwable) {
            return AiUniverseUtilityHelper::getExtensionConf(self::EXTENSION_KEY);
        }
    }

    /**
     * @param array<string, mixed> $settings
     */
    private function resolveProviderIdentifier(array $settings): ?string
    {
        $value = trim((string) ($settings['defaultProviderForExtended'] ?? 'default'));
        if ($value === '' || $value === 'default') {
            return null;
        }

        return $value;
    }
}
