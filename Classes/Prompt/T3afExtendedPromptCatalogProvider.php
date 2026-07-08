<?php

declare(strict_types=1);

namespace NITSAN\NsT3afExtended\Prompt;

use NITSAN\NsT3afExtended\Service\Ai\PromptContractRegistry;
use NITSAN\NsT3AF\Contract\PromptCatalogProviderInterface;
use NITSAN\NsT3AF\Contract\PromptCategoryDescriptor;
use NITSAN\NsT3AF\Prompt\AiPromptRepository;
use NITSAN\NsT3AF\Prompt\Support\PromptContractCatalogSupport;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Sample {@see PromptCatalogProviderInterface} for EXT:ns_t3af_extended.
 */
final class T3afExtendedPromptCatalogProvider implements PromptCatalogProviderInterface
{
    private const EXTENSION_KEY = 'ns_t3af_extended';

    private const CATEGORY_ID = 't3af_extended_prompts';

    /** @var array<string, array{title: string, extension: string, description: string, manageLabel: string, scope: string}> */
    private const CATEGORIES = [
        self::CATEGORY_ID => [
            'title' => 'T3AF Extended Prompts',
            'extension' => 'ns_t3af_extended',
            'description' => 'Built-in and custom prompts for the AI Foundation developer reference extension.',
            'manageLabel' => 'Manage T3AF Extended Prompts',
            'scope' => 'demo',
        ],
    ];

    /** @var array<string, string> */
    private const SCOPE_LABELS = [
        'demo' => 'Extended prompts',
    ];

    public function __construct(
        private readonly AiPromptRepository $aiPromptRepository,
    ) {}

    public function isAvailable(): bool
    {
        return ExtensionManagementUtility::isLoaded(self::EXTENSION_KEY)
            && ExtensionManagementUtility::isLoaded('ns_t3af')
            && $this->aiPromptRepository->isTableRegistered();
    }

    public function getExtensionKey(): string
    {
        return self::EXTENSION_KEY;
    }

    public function getCategories(int $storagePid = 0): array
    {
        if (!$this->isAvailable()) {
            return [];
        }

        $categories = [];
        foreach (self::CATEGORIES as $categoryId => $meta) {
            $rows = $this->getPromptRowsForCategory($categoryId, $storagePid);
            $types = array_unique(array_map(static fn(array $row): string => (string) $row['promptType'], $rows));
            $descriptor = new PromptCategoryDescriptor(
                id: $categoryId,
                title: $meta['title'],
                extensionKey: $meta['extension'],
                description: $meta['description'],
                manageLabel: $meta['manageLabel'],
                sourceTable: AiPromptRepository::TABLE,
                readOnly: false,
                scope: $meta['scope'],
            );
            $category = $descriptor->toArray();
            $category['promptCount'] = count($rows);
            $category['customPromptCount'] = count(array_filter(
                $rows,
                static fn(array $row): bool => !($row['isBuiltin'] ?? false),
            ));
            $category['scopeCount'] = max(1, count($types));
            $categories[] = $category;
        }

        return $categories;
    }

    public function supportsCategory(string $categoryId): bool
    {
        return isset(self::CATEGORIES[$categoryId]);
    }

    public function isReadOnlyCategory(string $categoryId): bool
    {
        return false;
    }

    public function resolveCategoryScope(string $categoryId): string
    {
        return self::CATEGORIES[$categoryId]['scope'] ?? $categoryId;
    }

    public function getSourceTable(string $categoryId): string
    {
        return AiPromptRepository::TABLE;
    }

    public function buildUiCatalog(): array
    {
        return PromptContractCatalogSupport::buildUiCatalogFromRegistry(
            GeneralUtility::makeInstance(PromptContractRegistry::class),
            self::SCOPE_LABELS,
        );
    }

    public function getPromptRowsForCategory(string $categoryId, int $storagePid = 0): array
    {
        if (!$this->supportsCategory($categoryId)) {
            return [];
        }

        $scope = $this->resolveCategoryScope($categoryId);
        $registry = GeneralUtility::makeInstance(PromptContractRegistry::class);

        $builtinRows = array_map(static fn(array $row): array => [
            'uid' => (int) $row['uid'],
            'source' => AiPromptRepository::TABLE,
            'promptType' => (string) $row['prompt_type'],
            'scope' => (string) $row['scope'],
            'promptTitle' => (string) $row['prompt_title'],
            'promptText' => (string) $row['prompt_text'],
            'isBuiltin' => true,
        ], PromptContractCatalogSupport::getBuiltinPromptRowsForScope($registry, $scope));

        $customRows = array_map(static fn(array $row): array => [
            'uid' => (int) $row['uid'],
            'source' => AiPromptRepository::TABLE,
            'promptType' => (string) $row['prompt_type'],
            'scope' => (string) $row['scope'],
            'promptTitle' => (string) $row['prompt_title'],
            'promptText' => (string) $row['prompt_text'],
            'isBuiltin' => false,
        ], $this->aiPromptRepository->findGlobalRows(self::EXTENSION_KEY, $categoryId, 0, true));

        return array_merge($builtinRows, $customRows);
    }

    public function validateGlobalPrompt(string $categoryId, string $promptType, string $scope, string $promptText): ?string
    {
        return PromptContractCatalogSupport::validateGlobalPrompt(
            GeneralUtility::makeInstance(PromptContractRegistry::class),
            $promptType,
            $scope,
            $promptText,
        );
    }
}
