<?php

declare(strict_types=1);

namespace NITSAN\NsT3afExtended\Provider;

use NITSAN\NsT3AF\Domain\Model\Provider;
use NITSAN\NsT3AF\Provider\Capability;
use NITSAN\NsT3AF\Provider\Contract\AdapterInterface;
use NITSAN\NsT3AF\Provider\Contract\VerifyResult;

/**
 * Sample {@see AdapterInterface} for EXT:ns_t3af_extended (see EXT:ns_t3af CustomProviders.rst).
 */
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

    public function getDefaultEndpoint(): string
    {
        return '';
    }

    public function getDefaultCapabilities(): array
    {
        return [Capability::CHAT, Capability::COMPLETION];
    }

    public function testConnection(Provider $provider): VerifyResult
    {
        return VerifyResult::ok(
            'T3AF Extended adapter registered. Responses are simulated — use a real provider in production.',
            ['extended-model'],
            1,
        );
    }

    public function platform(Provider $provider): object
    {
        return new T3afExtendedPlatform();
    }
}
