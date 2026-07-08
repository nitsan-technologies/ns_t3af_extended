<?php

declare(strict_types=1);

namespace NITSAN\NsT3afExtended\Service\Ai;

/**
 * Built-in AI prompt contracts for the t3af extended extension.
 *
 * @phpstan-type PromptContract array{
 *     scope: string,
 *     label: string,
 *     defaultText: string,
 *     requiredVariables: list<string>
 * }
 */
final class PromptContractRegistry
{
    /** @var array<string, PromptContract> */
    private const CONTRACTS = [
        'extended_summary' => [
            'scope' => 'demo',
            'label' => 'Extended summary',
            'defaultText' => 'Summarize the following text in [tone] style for [language]: [input]',
            'requiredVariables' => ['tone', 'language', 'input'],
        ],
        'extended_greeting' => [
            'scope' => 'demo',
            'label' => 'Extended greeting',
            'defaultText' => 'Write a friendly greeting for [audience] mentioning [topic].',
            'requiredVariables' => ['audience', 'topic'],
        ],
    ];

    /**
     * @return list<string>
     */
    public function getPromptTypes(): array
    {
        return array_keys(self::CONTRACTS);
    }

    public function has(string $promptType): bool
    {
        return isset(self::CONTRACTS[$promptType]);
    }

    public function getScope(string $promptType): string
    {
        return self::CONTRACTS[$promptType]['scope'] ?? '';
    }

    public function getDefaultText(string $promptType): string
    {
        return self::CONTRACTS[$promptType]['defaultText'] ?? '';
    }

    /**
     * @return list<string>
     */
    public function getRequiredVariables(string $promptType): array
    {
        return self::CONTRACTS[$promptType]['requiredVariables'] ?? [];
    }

    public function textContainsRequiredVariables(string $promptType, string $text): bool
    {
        foreach ($this->getRequiredVariables($promptType) as $variable) {
            if (!str_contains($text, '[' . $variable . ']')) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return list<string>
     */
    public function getPromptTypesForScope(string $scope): array
    {
        $types = [];
        foreach (self::CONTRACTS as $promptType => $contract) {
            if ($contract['scope'] === $scope) {
                $types[] = $promptType;
            }
        }

        return $types;
    }
}
