.. include:: /Includes.rst.txt

=====================
Dependency injection
=====================

Third-party extensions must register ns_t3af integration services in **their own**
DI configuration. This extension splits registration across
:file:`Configuration/Services.yaml` (always loaded) and
:file:`Configuration/Services.php` (conditional tags when ns_t3af interfaces exist).

Example: Base services and adapter tagging
==========================================

Tag :php:`AdapterInterface` and :php:`AiAccessCatalogProviderInterface` in your
extension — the ``_instanceof`` rules in EXT:ns_t3af apply only to services defined
inside ns_t3af.

.. code-block:: yaml
   :caption: packages/ns_t3af_extended/Configuration/Services.yaml

   services:
     _defaults:
       autowire: true
       autoconfigure: true
       public: false

     NITSAN\NsT3afExtended\:
       resource: '../Classes/*'
       exclude:
         - '../Classes/Prompt/*'
         - '../Classes/Service/Ai/*'
         - '../Classes/Feature/*'
         - '../Classes/Mcp/*'
         - '../Classes/Access/*'
         - '../Classes/Provider/*'

     _instanceof:
       NITSAN\NsT3AF\Provider\Contract\AdapterInterface:
         tags: ['nst3af.adapter']
       NITSAN\NsT3AF\Contract\AiAccessCatalogProviderInterface:
         tags: ['t3af.ai_access_catalog_provider']

     NITSAN\NsT3afExtended\Provider\:
       resource: '../Classes/Provider/*'

     NITSAN\NsT3afExtended\Access\:
       resource: '../Classes/Access/*'

Example: Conditional registration when ns_t3af is loaded
========================================================

Prompt, Feature, and MCP providers are registered only when the matching ns_t3af
interface is available. MCP tool handlers must be ``public: true``.

.. code-block:: php
   :caption: packages/ns_t3af_extended/Configuration/Services.php

   <?php

   declare(strict_types=1);

   use NITSAN\NsT3afExtended\Feature\T3afExtendedAiFeatureCardProvider;
   use NITSAN\NsT3afExtended\Feature\T3afExtendedExtensionSettingsDynamicDefaultsProvider;
   use NITSAN\NsT3afExtended\Feature\T3afExtendedExtensionSettingsScopeProvider;
   use NITSAN\NsT3afExtended\Feature\T3afExtendedFeatureProviderFormOptions;
   use NITSAN\NsT3afExtended\Mcp\T3afExtendedMcpToolsExtensionCardProvider;
   use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

   return static function (ContainerConfigurator $containerConfigurator): void {
       $services = $containerConfigurator->services()
           ->defaults()
           ->autowire()
           ->autoconfigure();

       if (interface_exists(\NITSAN\NsT3AF\Contract\PromptCatalogProviderInterface::class)) {
           $services->load('NITSAN\\NsT3afExtended\\Prompt\\', __DIR__ . '/../Classes/Prompt/')
               ->tag('t3af.prompt_catalog_provider');
           $services->load('NITSAN\\NsT3afExtended\\Service\\Ai\\', __DIR__ . '/../Classes/Service/Ai/');
       }

       if (interface_exists(\NITSAN\NsT3AF\Contract\AiFeatureCardProviderInterface::class)) {
           $services->set(T3afExtendedAiFeatureCardProvider::class)
               ->tag('t3af.ai_feature_card_provider');
       }

       if (interface_exists(\NITSAN\NsT3AF\Contract\ExtensionSettingsScopeProviderInterface::class)) {
           $services->set(T3afExtendedExtensionSettingsScopeProvider::class)
               ->tag('t3af.extension_settings_scope');
       }

       if (interface_exists(\NITSAN\NsT3AF\Contract\FeatureProviderFormOptionsInterface::class)) {
           $services->set(T3afExtendedFeatureProviderFormOptions::class)
               ->tag('t3af.feature_provider_form_options');
       }

       if (interface_exists(\NITSAN\NsT3AF\Contract\ExtensionSettingsDynamicDefaultsProviderInterface::class)) {
           $services->set(T3afExtendedExtensionSettingsDynamicDefaultsProvider::class)
               ->tag('t3af.extension_settings_dynamic_defaults');
       }

       if (interface_exists(\NITSAN\NsT3AF\Mcp\Contract\McpToolHandlerInterface::class)) {
           $services->load('NITSAN\\NsT3afExtended\\Mcp\\Tool\\', __DIR__ . '/../Classes/Mcp/Tool/')
               ->public()
               ->tag('mcp.tool');
       }

       if (interface_exists(\NITSAN\NsT3AF\Contract\McpToolsExtensionCardProviderInterface::class)) {
           $services->set(T3afExtendedMcpToolsExtensionCardProvider::class)
               ->tag('t3af.mcp_tools_extension_card_provider');
       }
   };

Flush caches after changing either file.
