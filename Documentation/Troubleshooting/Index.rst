.. include:: /Includes.rst.txt

================
Troubleshooting
================

Common issues when integrating with EXT:ns_t3af and verifying
|extension_name| in the backend.

Cards or tools do not appear
============================

Integration services register only when **EXT:ns_t3af** is loaded and the
matching interface exists. Conditional registration lives in
:file:`Configuration/Services.php`.

#. Confirm both extensions are active: **Admin Tools → Extensions** or
   ``vendor/bin/typo3 extension:list | grep ns_``.
#. Flush all caches: ``vendor/bin/typo3 cache:flush``.
#. Check that your extension repeats DI tags in **your** ``Services.yaml`` /
   ``Services.php`` — tags from ns_t3af's ``_instanceof`` block do not apply to
   third-party services. See :doc:`../Developer/DependencyInjection`.

MCP tools missing from tools/list
=================================

MCP handlers must be tagged ``mcp.tool`` **and** declared ``public: true`` in
``Services.php``. Private services are not callable by the MCP server.

.. code-block:: php
   :caption: packages/ns_t3af_extended/Configuration/Services.php

   $services->load('NITSAN\\NsT3afExtended\\Mcp\\Tool\\', __DIR__ . '/../Classes/Mcp/Tool/')
       ->public()
       ->tag('mcp.tool');

AI Prompts card is empty
========================

:php:`T3afExtendedPromptCatalogProvider::isAvailable()` returns ``false`` when the prompt
table is not registered. Ensure ns_t3af database migrations have run and flush
caches after activating both extensions.

Demo summarize returns an error
===============================

The MCP tool ``t3af_extended_summarize`` calls :php:`T3afExtendedAiService`, which
requires:

#. **Enable demo AI features** toggled on in **AI Foundation → AI Features → T3AF Extended** (per site).
#. At least one working provider in **AI Foundation → Providers** (not only the stub adapter).

.. code-block:: php
   :caption: packages/ns_t3af_extended/Classes/Service/T3afExtendedAiService.php

   if (($settings['enableExtendedAi'] ?? '0') !== '1') {
       throw new \RuntimeException(
           'Demo AI is disabled. Enable it in AI Foundation → AI Features → T3AF Extended.',
       );
   }

Custom provider chip missing
============================

:php:`AdapterInterface` implementations must be tagged ``nst3af.adapter`` in
**your** extension's ``Services.yaml`` and loaded from a dedicated
``Provider/`` resource path. See :doc:`../Developer/DependencyInjection`.

Changes to Services.yaml not picked up
======================================

Always flush caches after DI changes. In DDEV: ``ddev typo3 cache:flush``.
