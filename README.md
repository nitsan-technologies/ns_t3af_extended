# AI Foundation T3AF Extended

Reference TYPO3 extension demonstrating every integration hook documented in
`EXT:ns_t3af/Documentation/Developer/`.

Requires **EXT:ns_t3af** (AI Foundation) to be installed and activated for all
demo cards and tools to appear. Full documentation: `Documentation/Index.rst`.

## Installation

```bash
composer require nitsan/ns-t3af nitsan/ns-t3af-extended
vendor/bin/typo3 extension:activate ns_t3af ns_t3af_extended
vendor/bin/typo3 cache:flush
```

`composer.json` lists ns_t3af under `suggest` (optional at install time) but it is
**required in practice** for every integration hook. Child extensions copying these
patterns should use `"require": { "nitsan/ns-t3af": "^1.0" }`.

---



## Dependency injection

Third-party extensions register ns_t3af hooks in their own DI configuration.
This extension uses `Services.yaml` for adapters and access, and `Services.php`
for conditional tags when ns_t3af interfaces are available.

```yaml
# packages/ns_t3af_extended/Configuration/Services.yaml
services:
  _defaults:
    autowire: true
    autoconfigure: true
    public: false

  _instanceof:
    NITSAN\NsT3AF\Provider\Contract\AdapterInterface:
      tags: ['nst3af.adapter']
    NITSAN\NsT3AF\Contract\AiAccessCatalogProviderInterface:
      tags: ['t3af.ai_access_catalog_provider']

  NITSAN\NsT3afExtended\Provider\:
    resource: '../Classes/Provider/*'

  NITSAN\NsT3afExtended\Access\:
    resource: '../Classes/Access/*'
```

```php
// packages/ns_t3af_extended/Configuration/Services.php
if (interface_exists(\NITSAN\NsT3AF\Contract\PromptCatalogProviderInterface::class)) {
    $services->load('NITSAN\\NsT3afExtended\\Prompt\\', __DIR__ . '/../Classes/Prompt/')
        ->tag('t3af.prompt_catalog_provider');
}
if (interface_exists(\NITSAN\NsT3AF\Contract\AiFeatureCardProviderInterface::class)) {
    $services->set(T3afExtendedAiFeatureCardProvider::class)->tag('t3af.ai_feature_card_provider');
}
if (interface_exists(\NITSAN\NsT3AF\Contract\ExtensionSettingsScopeProviderInterface::class)) {
    $services->set(T3afExtendedExtensionSettingsScopeProvider::class)->tag('t3af.extension_settings_scope');
}
if (interface_exists(\NITSAN\NsT3AF\Contract\FeatureProviderFormOptionsInterface::class)) {
    $services->set(T3afExtendedFeatureProviderFormOptions::class)->tag('t3af.feature_provider_form_options');
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
```

---



## Custom AI provider

Implement `AdapterInterface` and tag the class with `nst3af.adapter` so it appears
under **AI Foundation → Providers**.

```php
// packages/ns_t3af_extended/Classes/Provider/T3afExtendedAdapter.php
final class T3afExtendedAdapter implements AdapterInterface
{
    public function getType(): string
    {
        return 'custom.t3af_extended';
    }

    public function getDisplayName(): string
    {
        return 'T3AF Extended (stub)';
    }

    public function testConnection(Provider $provider): VerifyResult
    {
        return VerifyResult::ok('Demo adapter registered.', ['demo-model'], 1);
    }

    public function platform(Provider $provider): object
    {
        return new T3afExtendedPlatform();
    }
}
```

---



## AI Prompts

Register built-in prompt contracts in PHP and expose them via
`PromptCatalogProviderInterface` (tag: `t3af.prompt_catalog_provider`).

```php
// packages/ns_t3af_extended/Classes/Service/Ai/PromptContractRegistry.php
private const CONTRACTS = [
    'extended_summary' => [
        'scope' => 'demo',
        'label' => 'Extended summary',
        'defaultText' => 'Summarize the following text in [tone] style for [language]: [input]',
        'requiredVariables' => ['tone', 'language', 'input'],
    ],
];
```

```php
// packages/ns_t3af_extended/Classes/Service/Ai/PromptResolver.php
public function resolveText(string $promptType, ?string $explicitPromptText = null): string
{
    if ($explicitPromptText !== null && trim($explicitPromptText) !== '') {
        return trim($explicitPromptText);
    }
    return $this->promptContractRegistry->getDefaultText($promptType);
}
```

---



## AI Features

Per-site settings use a TypoScript field schema, a card provider, scope provider,
provider-override dropdown, and optional dynamic defaults.

```typoscript
# packages/ns_t3af_extended/Configuration/ExtensionSettings/fields.typoscript
# cat=t3af extended//01; type=boolean; label=Enable demo AI features
enableExtendedAi = 0

# cat=t3af extended//02; type=options[concise, detailed]; label=Summary tone
summaryTone = concise
```

```php
// packages/ns_t3af_extended/Classes/Feature/T3afExtendedAiFeatureCardProvider.php
return [
    new AiFeatureCardDescriptor(
        id: 't3af-extended-ai',
        name: 'T3AF Extended',
        extKey: 'ns_t3af_extended',
        settingsScope: 't3af extended',
        wizardToggleField: 'enableExtendedAi',
    ),
];
```

---



## AI Access

Register module cards, feature bits, and record ACL via
`AiAccessCatalogProviderInterface` (tag: `t3af.ai_access_catalog_provider`).

```php
// packages/ns_t3af_extended/Classes/Access/T3afExtendedAccessCatalogProvider.php
return [
    new FeaturePermissionDescriptor(
        id: 'extendedSummarize',
        label: 'Demo summarize',
        permBase: 'T3afExtended.Summarize',
        relevantModules: ['t3af_extended'],
        extension: 'ns_t3af_extended',
    ),
];
```

---



## MCP tools

Annotate handler methods with `#[McpTool]` and `#[McpToolOwner]`, then tag services
with `mcp.tool` (`public: true`).

```php
// packages/ns_t3af_extended/Classes/Mcp/Tool/EchoTool.php
#[McpTool(name: 't3af_extended_echo', description: 'Greeting echo for integrator testing.')]
#[McpToolOwner(extensionKey: 'ns_t3af_extended')]
public function execute(string $name = 'integrator'): string
{
    return json_encode(['message' => 'Hello from T3AF Extended, ' . trim($name) . '!']);
}
```

---



## Runtime AI calls

Inject `AiServiceInterface` and pass `extensionKey` / `featureKey` in `AiOptions`.

```php
// packages/ns_t3af_extended/Classes/Service/T3afExtendedAiService.php
$options = new AiOptions(
    extensionKey: 'ns_t3af_extended',
    featureKey: 'extended.summarize',
    featureLabel: 'T3AF Extended Summarize',
    requestSource: 'mcp_tool',
);

return $this->aiService->complete($prompt, $options)->content;
```

---

