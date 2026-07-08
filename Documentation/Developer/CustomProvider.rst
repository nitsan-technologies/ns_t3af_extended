.. include:: /Includes.rst.txt

===================
Custom AI provider
===================

A custom adapter lets you connect proprietary or on-prem LLM gateways to
**AI Foundation → Providers**. Implement :php:`AdapterInterface`, return a platform
object from :php:`platform()`, and tag the class with ``nst3af.adapter``.

Example: Register a stub adapter
==================================

The demo adapter simulates chat responses for integrator testing. A real adapter
would wrap your SDK or HTTP client in :php:`testConnection()` and :php:`platform()`.

.. code-block:: php
   :caption: packages/ns_t3af_extended/Classes/Provider/T3afExtendedAdapter.php

   <?php

   declare(strict_types=1);

   namespace NITSAN\NsT3afExtended\Provider;

   use NITSAN\NsT3AF\Domain\Model\Provider;
   use NITSAN\NsT3AF\Provider\Capability;
   use NITSAN\NsT3AF\Provider\Contract\AdapterInterface;
   use NITSAN\NsT3AF\Provider\Contract\VerifyResult;

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
               'Demo adapter registered. Responses are simulated — use a real provider in production.',
               ['demo-model'],
               1,
           );
       }

       public function platform(Provider $provider): object
       {
           return new T3afExtendedPlatform();
       }
   }

Example: Platform handle for runtime calls
==========================================

:php:`AiServiceInterface` duck-types ``invoke()`` on the object returned by
:php:`platform()`.

.. code-block:: php
   :caption: packages/ns_t3af_extended/Classes/Provider/T3afExtendedPlatform.php

   <?php

   declare(strict_types=1);

   namespace NITSAN\NsT3afExtended\Provider;

   final class T3afExtendedPlatform
   {
       public function invoke(mixed ...$args): string
       {
           $prompt = '';
           foreach ($args as $arg) {
               if (is_string($arg) && trim($arg) !== '' && !str_contains($arg, '.')) {
                   $prompt = $arg;
                   break;
               }
           }

           return '[T3AF Extended adapter] ' . ($prompt !== '' ? $prompt : 'No prompt received.');
       }
   }

Verify under **AI Foundation → Providers** — chip **T3AF Extended (stub)** appears
and the connection test succeeds.
