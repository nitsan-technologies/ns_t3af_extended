<?php

declare(strict_types=1);

namespace NITSAN\NsT3afExtended\Feature;

use NITSAN\NsT3AF\Contract\FeatureProviderFormOptionsInterface;
use NITSAN\NsT3AF\Domain\Model\Provider;
use NITSAN\NsT3AF\Domain\Repository\ProviderRepositoryInterface;
use NITSAN\NsT3AF\Provider\AdapterRegistry;

/**
 * Minimal {@see FeatureProviderFormOptionsInterface} for the demo provider override field.
 */
final class T3afExtendedFeatureProviderFormOptions implements FeatureProviderFormOptionsInterface
{
    private const EXTENSION_KEY = 'ns_t3af_extended';

    private const SETTINGS_SCOPE = 't3af extended';

    private const FIELD_DEFAULT_PROVIDER = 'defaultProviderForExtended';

    public function __construct(
        private readonly ProviderRepositoryInterface $providerRepository,
        private readonly AdapterRegistry $adapterRegistry,
    ) {}

    public function getExtensionKey(): string
    {
        return self::EXTENSION_KEY;
    }

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

        $options = [
            [
                'label' => 'Default (inherit global provider)',
                'value' => 'default',
                'selected' => $currentValue === '' || $currentValue === 'default',
                'adapterAvailable' => true,
            ],
        ];

        foreach ($this->providerRepository->findAll() as $provider) {
            if (!$provider->isEnabled) {
                continue;
            }
            $identifier = trim($provider->identifier);
            if ($identifier === '') {
                continue;
            }
            $adapterAvailable = $this->adapterRegistry->has($provider->adapterType);
            $options[] = [
                'label' => $this->providerLabel($provider),
                'value' => $identifier,
                'selected' => $identifier === $currentValue,
                'adapterAvailable' => $adapterAvailable,
                'unavailableMessage' => $adapterAvailable
                    ? ''
                    : 'Adapter "' . $provider->adapterType . '" is not registered.',
            ];
        }

        return $options;
    }

    public function providerOverrideHint(string $scope, string $fieldName): string
    {
        if ($scope !== self::SETTINGS_SCOPE || $fieldName !== self::FIELD_DEFAULT_PROVIDER) {
            return '';
        }

        return 'Leave as Default to use the global provider from AI Foundation → Providers.';
    }

    public function allowedValuesForField(string $fieldName): array
    {
        if ($fieldName !== self::FIELD_DEFAULT_PROVIDER) {
            return [];
        }

        $values = ['default'];
        foreach ($this->providerRepository->findAll() as $provider) {
            if ($provider->isEnabled && trim($provider->identifier) !== '') {
                $values[] = $provider->identifier;
            }
        }

        return array_values(array_unique($values));
    }

    public function validateOverrideValue(string $fieldName, string $value): ?string
    {
        if ($fieldName !== self::FIELD_DEFAULT_PROVIDER) {
            return null;
        }

        if ($value === '' || $value === 'default') {
            return null;
        }

        if (!in_array($value, $this->allowedValuesForField($fieldName), true)) {
            return 'Unknown provider identifier.';
        }

        return null;
    }

    public function validateSubmittedSettings(array $submitted): array
    {
        return [];
    }

    private function providerLabel(Provider $provider): string
    {
        $label = trim($provider->title);
        if ($label === '') {
            $label = $provider->identifier;
        }

        return $label . ' (' . $provider->identifier . ')';
    }
}
