.. include:: /Includes.rst.txt

============
AI Features
============

AI Features expose per-site extension settings in **AI Foundation → AI Features**.
Child extensions register a settings schema, a card provider, a scope provider,
optional provider-override dropdowns, and optional dynamic defaults.

Example: Settings field schema
==============================

Fields are defined in TypoScript notation. The ``cat`` segment becomes the settings
scope shown in the backend palette.

.. code-block:: typoscript
   :caption: packages/ns_t3af_extended/Configuration/ExtensionSettings/fields.typoscript

   # cat=t3af extended//01; type=boolean; label=Enable demo AI features
   enableExtendedAi = 0

   # cat=t3af extended//02; type=options[concise, detailed]; label=Summary tone
   summaryTone = concise

   # cat=t3af extended//03; type=string; label=Greeting prefix for echo MCP tool
   greetingPrefix = Hello from T3AF Extended

   # cat=t3af extended//04; type=string; label=Default AI provider for demo features (inherit = default)
   defaultProviderForExtended = default

Point the schema loader at the fields file:

.. code-block:: php
   :caption: packages/ns_t3af_extended/Configuration/ExtensionSettings/schema.php

   <?php

   declare(strict_types=1);

   return [
       'fieldsTemplate' => __DIR__ . '/fields.typoscript',
   ];

Example: Feature card in the backend
======================================

The card provider returns one :php:`AiFeatureCardDescriptor` per integration.
``settingsScope`` must match the ``cat`` prefix in ``fields.typoscript``.

.. code-block:: php
   :caption: packages/ns_t3af_extended/Classes/Feature/T3afExtendedAiFeatureCardProvider.php

   <?php

   declare(strict_types=1);

   namespace NITSAN\NsT3afExtended\Feature;

   use NITSAN\NsT3AF\Contract\AiFeatureCardDescriptor;
   use NITSAN\NsT3AF\Contract\AiFeatureCardProviderInterface;

   final class T3afExtendedAiFeatureCardProvider implements AiFeatureCardProviderInterface
   {
       private const EXTENSION_KEY = 'ns_t3af_extended';
       private const SETTINGS_SCOPE = 't3af extended';

       public function getFeatureCards(): array
       {
           return [
               new AiFeatureCardDescriptor(
                   id: 't3af-extended-ai',
                   name: 'T3AF Extended',
                   subtitle: 'Reference AI Features integration',
                   extKey: self::EXTENSION_KEY,
                   settingsScope: self::SETTINGS_SCOPE,
                   icon: 'actions-code',
                   description: 'Per-site toggles, summary tone, greeting prefix, and optional per-feature AI provider override.',
                   wizardEligible: true,
                   wizardToggleField: 'enableExtendedAi',
               ),
           ];
       }

       // ... isAvailable(), getExtensionKey()
   }

Example: Settings scope provider
================================

Declare which scopes your extension owns so ns_t3af can filter palettes and saves.

.. code-block:: php
   :caption: packages/ns_t3af_extended/Classes/Feature/T3afExtendedExtensionSettingsScopeProvider.php

   <?php

   declare(strict_types=1);

   namespace NITSAN\NsT3afExtended\Feature;

   use NITSAN\NsT3AF\Contract\ExtensionSettingsScopeProviderInterface;

   final class T3afExtendedExtensionSettingsScopeProvider implements ExtensionSettingsScopeProviderInterface
   {
       private const SETTINGS_SCOPE = 't3af extended';

       public function getAllowedScopes(): array
       {
           return [self::SETTINGS_SCOPE];
       }

       // ... getCompositeScopeCategories(), getPaletteScopes(), getFieldFilterScopes()
   }

Example: Provider override dropdown
===================================

Bind a settings field to live provider rows from **AI Foundation → Providers**.

.. code-block:: php
   :caption: packages/ns_t3af_extended/Classes/Feature/T3afExtendedFeatureProviderFormOptions.php

   <?php

   declare(strict_types=1);

   namespace NITSAN\NsT3afExtended\Feature;

   use NITSAN\NsT3AF\Contract\FeatureProviderFormOptionsInterface;

   final class T3afExtendedFeatureProviderFormOptions implements FeatureProviderFormOptionsInterface
   {
       private const SETTINGS_SCOPE = 't3af extended';
       private const FIELD_DEFAULT_PROVIDER = 'defaultProviderForExtended';

       public function getManagedFieldBindings(): array
       {
           return [
               ['scope' => self::SETTINGS_SCOPE, 'field' => self::FIELD_DEFAULT_PROVIDER],
           ];
       }

       public function buildProviderOptions(string $scope, string $fieldName, string $currentValue = 'default'): array
       {
           if ($scope !== self::SETTINGS_SCOPE || $fieldName !== self::FIELD_DEFAULT_PROVIDER) {
               return [];
           }

           return [
               [
                   'label' => 'Default (inherit global provider)',
                   'value' => 'default',
                   'selected' => $currentValue === '' || $currentValue === 'default',
                   'adapterAvailable' => true,
               ],
               // ... append enabled providers from ProviderRepository
           ];
       }

       // ... providerOverrideHint(), allowedValuesForField(), validateOverrideValue()
   }

Example: Dynamic default values
===============================

Supply runtime defaults for fields that need fresh values on each form load.

.. code-block:: php
   :caption: packages/ns_t3af_extended/Classes/Feature/T3afExtendedExtensionSettingsDynamicDefaultsProvider.php

   <?php

   declare(strict_types=1);

   namespace NITSAN\NsT3afExtended\Feature;

   use NITSAN\NsT3AF\Contract\ExtensionSettingsDynamicDefaultsProviderInterface;

   final class T3afExtendedExtensionSettingsDynamicDefaultsProvider implements ExtensionSettingsDynamicDefaultsProviderInterface
   {
       public function getExtensionKey(): string
       {
           return 'ns_t3af_extended';
       }

       public function getDynamicDefaults(int $storagePid = 0): array
       {
           return [
               'greetingPrefix' => 'Hello from T3AF Extended (' . date('Y-m-d') . ')',
           ];
       }
   }

Verify under **AI Foundation → AI Features** — card **T3AF Extended**; enable
**Enable demo AI features** and save per site.
