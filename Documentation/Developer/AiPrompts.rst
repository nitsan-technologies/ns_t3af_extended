.. include:: /Includes.rst.txt

==========
AI Prompts
==========

AI Prompts let child extensions expose built-in LLM templates and custom rows in
**AI Foundation → AI Prompts**. Register a :php:`PromptCatalogProviderInterface`
and a PHP contract registry; tag the provider with ``t3af.prompt_catalog_provider``.

Example: Define built-in prompt contracts
=========================================

Built-in prompts live in PHP. Custom overrides are stored in
``tx_nst3af_ai_prompt`` by ns_t3af — you do not add your own table.

.. code-block:: php
   :caption: packages/ns_t3af_extended/Classes/Service/Ai/PromptContractRegistry.php

   <?php

   declare(strict_types=1);

   namespace NITSAN\NsT3afExtended\Service\Ai;

   final class PromptContractRegistry
   {
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

       public function getPromptTypes(): array
       {
           return array_keys(self::CONTRACTS);
       }

       public function getDefaultText(string $promptType): string
       {
           return self::CONTRACTS[$promptType]['defaultText'] ?? '';
       }

       // ... getScope(), getRequiredVariables(), getPromptTypesForScope()
   }

Example: Register a prompt catalog provider
===========================================

The catalog provider declares categories for the backend UI and merges built-in
contracts with custom database rows.

.. code-block:: php
   :caption: packages/ns_t3af_extended/Classes/Prompt/T3afExtendedPromptCatalogProvider.php

   <?php

   declare(strict_types=1);

   namespace NITSAN\NsT3afExtended\Prompt;

   use NITSAN\NsT3afExtended\Service\Ai\PromptContractRegistry;
   use NITSAN\NsT3AF\Contract\PromptCatalogProviderInterface;
   use NITSAN\NsT3AF\Contract\PromptCategoryDescriptor;
   use NITSAN\NsT3AF\Prompt\AiPromptRepository;
   use NITSAN\NsT3AF\Prompt\Support\PromptContractCatalogSupport;
   use TYPO3\CMS\Core\Utility\GeneralUtility;

   final class T3afExtendedPromptCatalogProvider implements PromptCatalogProviderInterface
   {
       private const EXTENSION_KEY = 'ns_t3af_extended';
       private const CATEGORY_ID = 't3af_extended_prompts';

       public function __construct(
           private readonly AiPromptRepository $aiPromptRepository,
       ) {}

       public function isAvailable(): bool
       {
           return ExtensionManagementUtility::isLoaded(self::EXTENSION_KEY)
               && ExtensionManagementUtility::isLoaded('ns_t3af')
               && $this->aiPromptRepository->isTableRegistered();
       }

       public function getCategories(int $storagePid = 0): array
       {
           $descriptor = new PromptCategoryDescriptor(
               id: self::CATEGORY_ID,
               title: 'T3AF Extended Prompts',
               extensionKey: self::EXTENSION_KEY,
               description: 'Built-in and custom prompts for the developer reference extension.',
               manageLabel: 'Manage T3AF Extended Prompts',
               sourceTable: AiPromptRepository::TABLE,
               readOnly: false,
               scope: 'demo',
           );

           return [$descriptor->toArray()];
       }

       public function buildUiCatalog(): array
       {
           return PromptContractCatalogSupport::buildUiCatalogFromRegistry(
               GeneralUtility::makeInstance(PromptContractRegistry::class),
               ['demo' => 'Extended prompts'],
           );
       }

       // ... getPromptRowsForCategory(), validateGlobalPrompt(), supportsCategory()
   }

Example: Resolve prompt text at runtime
=======================================

At call time, resolve explicit text → database override → built-in contract default.

.. code-block:: php
   :caption: packages/ns_t3af_extended/Classes/Service/Ai/PromptResolver.php

   <?php

   declare(strict_types=1);

   namespace NITSAN\NsT3afExtended\Service\Ai;

   use NITSAN\NsT3AF\Prompt\AiPromptRepository;

   final class PromptResolver
   {
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
                   'ns_t3af_extended',
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

Verify under **AI Foundation → AI Prompts** — filter **ns_t3af_extended**; card
**T3AF Extended Prompts** lists built-in rows.
