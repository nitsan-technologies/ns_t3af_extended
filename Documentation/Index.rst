.. include:: /Includes.rst.txt

.. _start:

====================================
AI Foundation T3AF Extended
====================================

:Extension key:
   |extension_key|

:Package name:
   nitsan/ns-t3af-extended

:Language:
   en

:Author:
   T3Planet // NITSAN

:License:
   GPL-2.0-or-later

----

|extension_name| is the **complete reference implementation** for third-party
integrators extending **EXT:ns_t3af** (AI Foundation).

Copy the patterns below into your own extension. Each page follows the same layout:
a short explanation and a working code sample from this repository.

Prerequisites
=============

- **EXT:ns_t3af** installed and activated
- PHP **>= 8.2**
- Flush caches after every DI change

.. toctree::
   :maxdepth: 2
   :caption: Getting started

   Installation/Index
   Developer/Index

.. toctree::
   :maxdepth: 2
   :caption: Developer guide

   Developer/DependencyInjection
   Developer/CustomProvider
   Developer/AiPrompts
   Developer/AiFeatures
   Developer/AiAccess
   Developer/McpTools
   Developer/RuntimeAiCalls

.. toctree::
   :maxdepth: 1
   :caption: Help

   Troubleshooting/Index
