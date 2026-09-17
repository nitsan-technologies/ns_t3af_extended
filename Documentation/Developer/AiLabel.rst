.. include:: /Includes.rst.txt

========
AI Label
========

AI Label records AI-generated content so editors can review it in
**AI Foundation → AI Label**. Capture is automatic on every
:php:`AiServiceInterface` call. Bind after you persist a record.

Official guide: ``EXT:ns_t3af/Documentation/DeveloperGuide/AiLabelIntegration/Index.rst``.

Working demo: summarize into ``tt_content``
===========================================

``t3af_extended_summarize`` returns JSON only. ``t3af_extended_summarize_content``
is the runnable path: capture via :php:`AiServiceInterface`, DataHandler write
to ``tt_content.bodytext``, then bind in the **same request**.

.. code-block:: php
   :caption: packages/ns_t3af_extended/Classes/Mcp/Tool/SummarizeContentTool.php

   public function execute(int $contentUid, string $text = '', string $tone = 'concise'): string
   {
       $result = $this->summarizeAndBindService->summarizeAndBind($contentUid, $text, $tone);

       return json_encode([
           'uid' => $result['uid'],
           'summary' => $result['summary'],
           'bound' => $result['bound'],
           'tool' => 't3af_extended_summarize_content',
       ], JSON_THROW_ON_ERROR);
   }

Pass an existing ``tt_content`` uid. If ``text`` is empty, the current bodytext
is summarized in place.

Bind after save
===============

:php:`T3afExtendedAiLabelBinder` wraps :php:`AiLabelBindHelper` with source
``ns_t3af_extended``. Child binds store involvement ``ai_generated``.

.. code-block:: php
   :caption: packages/ns_t3af_extended/Classes/AiLabel/T3afExtendedAiLabelBinder.php

   public function bindContentRecord(int $uid): void
   {
       if ($uid <= 0 || !class_exists(AiLabelBindHelper::class)) {
           return;
       }

       AiLabelBindHelper::bindContentRecord($uid, self::SOURCE);
   }

Verify
------

#. Activate **ns_t3af** and **ns_t3af_extended**, then flush caches.
#. Enable **AI Features → T3AF Extended** and configure a real provider.
#. Note a ``tt_content`` uid from the Page module.
#. Call ``t3af_extended_summarize_content`` with that uid (MCP client or
   **AI Foundation → MCP Tools**).
#. Open **AI Foundation → AI Label** (Texts) — the content element is listed
   with recording source ``ns_t3af_extended``.
