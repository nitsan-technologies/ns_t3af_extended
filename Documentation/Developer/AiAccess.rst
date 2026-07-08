.. include:: /Includes.rst.txt

=========
AI Access
=========

AI Access registers module cards, feature permission bits, and record ACL rows for
**AI Foundation → AI Access / Roles**. Implement :php:`AiAccessCatalogProviderInterface`
and tag it with ``t3af.ai_access_catalog_provider``.

Example: Module card and feature permissions
============================================

Feature bits map to ``T3Ai:*`` permissions assignable via ``be_groups`` or the
optional access wizard.

.. code-block:: php
   :caption: packages/ns_t3af_extended/Classes/Access/T3afExtendedAccessCatalogProvider.php

   <?php

   declare(strict_types=1);

   namespace NITSAN\NsT3afExtended\Access;

   use NITSAN\NsT3AF\Access\Dto\FeatureAccessBindingsDescriptor;
   use NITSAN\NsT3AF\Access\Dto\FeaturePermissionDescriptor;
   use NITSAN\NsT3AF\Access\Dto\ModuleAccessDescriptor;
   use NITSAN\NsT3AF\Access\Dto\RecordPermissionDescriptor;
   use NITSAN\NsT3AF\Contract\AiAccessCatalogProviderInterface;

   final class T3afExtendedAccessCatalogProvider implements AiAccessCatalogProviderInterface
   {
       private const EXTENSION_KEY = 'ns_t3af_extended';

       public function getCatalogModuleKey(): string
       {
           return 't3af_extended';
       }

       public function getModuleAccess(): ?ModuleAccessDescriptor
       {
           return new ModuleAccessDescriptor(
               label: 'T3AF Extended',
               sublabel: 'Reference integration',
               description: 'Sample module card for AI Access / Roles wizard and be_groups custom options.',
               color: '#10b981',
               groupMod: 'nitsan_nst3afextended',
               extension: self::EXTENSION_KEY,
           );
       }

       public function getFeaturePermissions(): array
       {
           return [
               new FeaturePermissionDescriptor(
                   id: 'extendedSummarize',
                   label: 'Demo summarize',
                   description: 'Allow AI summarization via demo MCP tool and services.',
                   permBase: 'T3afExtended.Summarize',
                   relevantModules: ['t3af_extended'],
                   group: 't3af_extended',
                   extension: self::EXTENSION_KEY,
               ),
               new FeaturePermissionDescriptor(
                   id: 'extendedEcho',
                   label: 'Demo echo',
                   description: 'Allow non-AI echo MCP tool for integrator testing.',
                   permBase: 'T3afExtended.Echo',
                   relevantModules: ['t3af_extended'],
                   group: 't3af_extended',
                   extension: self::EXTENSION_KEY,
               ),
           ];
       }

       public function getRecordPermissions(): array
       {
           return [
               new RecordPermissionDescriptor(
                   id: 'extendedContent',
                   label: 'Content elements (demo ACL)',
                   tables: ['tt_content'],
                   relevantModules: ['t3af_extended'],
                   relevantFeatures: ['extendedSummarize'],
                   readHelp: 'View content elements when demo summarize is granted',
                   writeHelp: 'Modify content elements when demo summarize is granted',
                   extension: self::EXTENSION_KEY,
               ),
           ];
       }

       public function getFeatureAccessBindings(): FeatureAccessBindingsDescriptor
       {
           return new FeatureAccessBindingsDescriptor(
               moduleKey: 't3af_extended',
               legacyCardPermPrefix: 'tx_t3af_extended_',
               moduleGroupMod: 'nitsan_nst3afextended',
               defaultTabFeature: 'T3afExtended.Summarize',
               tabFeatureMap: [
                   'summarize' => 'T3afExtended.Summarize',
                   'echo' => 'T3afExtended.Echo',
               ],
           );
       }

       // ... isAvailable(), getExtensionKey()
   }

Verify under **AI Access / Roles** — wizard shows **T3AF Extended** module with
**Demo summarize** and **Demo echo** feature bits.
