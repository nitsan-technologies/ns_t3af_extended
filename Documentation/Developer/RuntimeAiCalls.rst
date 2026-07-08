.. include:: /Includes.rst.txt

================
Runtime AI calls
================

Child extensions should call AI through :php:`AiServiceInterface` — never adapters or
provider repositories directly. Pass ``extensionKey`` and ``featureKey`` in
:php:`AiOptions` so analytics attribution is recorded correctly.

Example: Summarize via AiServiceInterface
=========================================

Inject :php:`AiServiceInterface` and read per-site settings from
:php:`ExtensionSettingsService` before building :php:`AiOptions`.

.. code-block:: php
   :caption: packages/ns_t3af_extended/Classes/Service/T3afExtendedAiService.php

   <?php

   declare(strict_types=1);

   namespace NITSAN\NsT3afExtended\Service;

   use NITSAN\NsT3AF\Api\AiOptions;
   use NITSAN\NsT3AF\Api\AiServiceInterface;
   use NITSAN\NsT3AF\Settings\ExtensionSettingsService;

   final class T3afExtendedAiService
   {
       private const EXTENSION_KEY = 'ns_t3af_extended';

       public function __construct(
           private readonly AiServiceInterface $aiService,
           private readonly ExtensionSettingsService $extensionSettingsService,
       ) {}

       public function summarize(string $prompt): string
       {
           $settings = $this->extensionSettingsService->getAll(self::EXTENSION_KEY, 0);
           if (($settings['enableExtendedAi'] ?? '0') !== '1') {
               throw new \RuntimeException(
                   'Demo AI is disabled. Enable it in AI Foundation → AI Features → T3AF Extended.',
               );
           }

           $options = new AiOptions(
               providerIdentifier: $this->resolveProviderIdentifier($settings),
               temperature: ($settings['summaryTone'] ?? 'concise') === 'detailed' ? 0.5 : 0.2,
               maxTokens: ($settings['summaryTone'] ?? 'concise') === 'detailed' ? 600 : 300,
               extensionKey: self::EXTENSION_KEY,
               featureKey: 'extended.summarize',
               featureLabel: 'T3AF Extended Summarize',
               requestSource: 'mcp_tool',
           );

           return $this->aiService->complete($prompt, $options)->content;
       }

       private function resolveProviderIdentifier(array $settings): ?string
       {
           $value = trim((string) ($settings['defaultProviderForExtended'] ?? 'default'));
           return ($value === '' || $value === 'default') ? null : $value;
       }
   }

The MCP tool ``t3af_extended_summarize`` is the quickest way to exercise this
service end-to-end after enabling demo AI features and configuring a provider.
