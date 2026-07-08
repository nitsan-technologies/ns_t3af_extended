.. include:: /Includes.rst.txt

=========
MCP tools
=========

MCP tools expose callable handlers to the AI Foundation MCP server. Register handler
classes with the ``mcp.tool`` tag (``public: true``) and a card provider with
``t3af.mcp_tools_extension_card_provider``.

Example: Non-AI echo tool
=========================

Non-AI tools implement :php:`McpNonAiToolInterface`. Annotate ``execute()`` with
``#[McpTool]`` and ``#[McpToolOwner]``.

.. code-block:: php
   :caption: packages/ns_t3af_extended/Classes/Mcp/Tool/EchoTool.php

   <?php

   declare(strict_types=1);

   namespace NITSAN\NsT3afExtended\Mcp\Tool;

   use Mcp\Capability\Attribute\McpTool;
   use NITSAN\NsT3AF\Mcp\Attribute\McpToolOwner;
   use NITSAN\NsT3AF\Mcp\Contract\McpNonAiToolInterface;
   use NITSAN\NsT3AF\Settings\ExtensionSettingsService;

   final readonly class EchoTool implements McpNonAiToolInterface
   {
       public function __construct(
           private ExtensionSettingsService $extensionSettingsService,
       ) {}

       #[McpTool(
           name: 't3af_extended_echo',
           description: 'Returns a greeting echo for integrator testing (non-AI demo tool).',
       )]
       #[McpToolOwner(extensionKey: 'ns_t3af_extended')]
       public function execute(string $name = 'integrator'): string
       {
           $prefix = 'Hello from T3AF Extended';
           $settings = $this->extensionSettingsService->getAll('ns_t3af_extended', 0);
           if (isset($settings['greetingPrefix']) && trim((string) $settings['greetingPrefix']) !== '') {
               $prefix = trim((string) $settings['greetingPrefix']);
           }

           return json_encode([
               'message' => $prefix . ', ' . trim($name) . '!',
               'extension' => 'ns_t3af_extended',
               'tool' => 't3af_extended_echo',
           ], JSON_THROW_ON_ERROR);
       }
   }

Example: AI summarize tool
==========================

AI tools implement :php:`McpToolHandlerInterface` and delegate to
:php:`AiServiceInterface` via a child-extension service.

.. code-block:: php
   :caption: packages/ns_t3af_extended/Classes/Mcp/Tool/SummarizeTool.php

   <?php

   declare(strict_types=1);

   namespace NITSAN\NsT3afExtended\Mcp\Tool;

   use Mcp\Capability\Attribute\McpTool;
   use NITSAN\NsT3afExtended\Service\T3afExtendedAiService;
   use NITSAN\NsT3AF\Mcp\Attribute\McpToolOwner;
   use NITSAN\NsT3AF\Mcp\Contract\McpToolHandlerInterface;

   final readonly class SummarizeTool implements McpToolHandlerInterface
   {
       public function __construct(
           private T3afExtendedAiService $t3afExtendedAiService,
       ) {}

       #[McpTool(
           name: 't3af_extended_summarize',
           description: 'Summarize text using AI Foundation (demo tool for EXT:ns_t3af_extended).',
       )]
       #[McpToolOwner(extensionKey: 'ns_t3af_extended')]
       public function execute(string $text, string $tone = 'concise'): string
       {
           $summary = $this->t3afExtendedAiService->summarize($text);

           return json_encode([
               'summary' => $summary,
               'tone' => $tone,
               'extension' => 'ns_t3af_extended',
               'tool' => 't3af_extended_summarize',
           ], JSON_THROW_ON_ERROR);
       }
   }

Example: MCP Tools backend card
===============================

The card groups tools by ``toolPrefix`` in **AI Foundation → MCP Tools**.

.. code-block:: php
   :caption: packages/ns_t3af_extended/Classes/Mcp/T3afExtendedMcpToolsExtensionCardProvider.php

   <?php

   declare(strict_types=1);

   namespace NITSAN\NsT3afExtended\Mcp;

   use NITSAN\NsT3AF\Contract\McpToolsExtensionCardDescriptor;
   use NITSAN\NsT3AF\Contract\McpToolsExtensionCardProviderInterface;

   final class T3afExtendedMcpToolsExtensionCardProvider implements McpToolsExtensionCardProviderInterface
   {
       public function getCardDescriptor(): McpToolsExtensionCardDescriptor
       {
           return new McpToolsExtensionCardDescriptor(
               label: 'T3AF Extended',
               icon: '🧪',
               iconIdentifier: 'actions-code',
               tagline: 'Reference MCP tools for AI Foundation integrators — echo (non-AI) and summarize (AI).',
               skillName: 'T3AF Extended Assistant',
               skillTrigger: '/t3af-extended',
               toolPrefix: 't3af_extended_',
               sortPriority: 5,
           );
       }

       // ... isAvailable(), getExtensionKey()
   }

Verify under **AI Foundation → MCP Tools** — card **T3AF Extended** lists
``t3af_extended_echo`` and ``t3af_extended_summarize``.
