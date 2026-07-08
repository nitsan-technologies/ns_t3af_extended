.. include:: /Includes.rst.txt

.. _developer-index:

==============
T3AF Extended
==============

|extension_name| registers every integration hook documented in
``EXT:ns_t3af/Documentation/Developer/``.

Use it as a copy-paste starting point for:

- Custom AI providers
- AI Prompts
- AI Features (settings schema, provider overrides, dynamic defaults)
- MCP tools
- AI Access / permissions
- :php:`AiServiceInterface` runtime calls

Integration map
===============

.. list-table::
   :header-rows: 1
   :widths: 28 36 36

   * - Guide (EXT:ns_t3af)
     - Sample class
     - DI tag
   * - CustomProviders.rst
     - ``Classes/Provider/T3afExtendedAdapter.php``
     - ``nst3af.adapter``
   * - CustomAiPrompts.rst
     - ``Classes/Prompt/T3afExtendedPromptCatalogProvider.php``
     - ``t3af.prompt_catalog_provider``
   * - CustomAiFeatures.rst
     - ``Classes/Feature/T3afExtendedAiFeatureCardProvider.php``
     - ``t3af.ai_feature_card_provider``
   * - CustomAiFeatures.rst (scope)
     - ``Classes/Feature/T3afExtendedExtensionSettingsScopeProvider.php``
     - ``t3af.extension_settings_scope``
   * - FeatureProviderOverrides.rst
     - ``Classes/Feature/T3afExtendedFeatureProviderFormOptions.php``
     - ``t3af.feature_provider_form_options``
   * - CustomAiFeatures.rst (dynamic defaults)
     - ``Classes/Feature/T3afExtendedExtensionSettingsDynamicDefaultsProvider.php``
     - ``t3af.extension_settings_dynamic_defaults``
   * - CustomAiAccess.rst
     - ``Classes/Access/T3afExtendedAccessCatalogProvider.php``
     - ``t3af.ai_access_catalog_provider``
   * - CustomMcpTools.rst
     - ``Classes/Mcp/Tool/EchoTool.php``, ``SummarizeTool.php``
     - ``mcp.tool`` (``public: true``)
   * - CustomMcpTools.rst (card)
     - ``Classes/Mcp/T3afExtendedMcpToolsExtensionCardProvider.php``
     - ``t3af.mcp_tools_extension_card_provider``
   * - ExtensionIntegration.rst
     - ``Classes/Service/T3afExtendedAiService.php``
     - inject ``AiServiceInterface``

Verify in the backend
=====================

#. Activate **ns_t3af_extended** and **ns_t3af**, then flush caches.
#. **AI Foundation → Providers** — adapter chip **T3AF Extended (stub)**; test connection succeeds.
#. **AI Prompts** — filter **ns_t3af_extended**; card **T3AF Extended Prompts** with built-in rows.
#. **AI Features** — card **T3AF Extended**; enable **Enable demo AI features**; save per site.
#. **MCP Tools** — card **T3AF Extended**; tools ``t3af_extended_echo`` and ``t3af_extended_summarize``.
#. **AI Access / Roles** — wizard shows **T3AF Extended** module with **Demo summarize** / **Demo echo** bits.
