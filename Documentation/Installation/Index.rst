.. include:: /Includes.rst.txt

============
Installation
============

Install |extension_name| alongside **EXT:ns_t3af** (AI Foundation). This package
``require``\ s ``nitsan/ns-t3af`` — activate both extensions before verifying backend cards.

Example: Install via Composer
=============================

From the project root, require both packages and activate the extension in the
Extension Manager (or via CLI).

.. code-block:: bash
   :caption: Project root

   composer require nitsan/ns-t3af nitsan/ns-t3af-extended
   vendor/bin/typo3 extension:activate ns_t3af ns_t3af_extended
   vendor/bin/typo3 cache:flush

For a path repository monorepo (this workspace), the root :file:`composer.json`
already maps ``packages/ns_t3af_extended`` — run ``composer update nitsan/ns-t3af-extended``
after pulling changes.

Example: Composer dependency declaration
======================================

.. code-block:: json
   :caption: packages/ns_t3af_extended/composer.json

   {
       "require": {
           "typo3/cms-core": "^13.4 || ^14.3",
           "php": ">=8.2",
           "nitsan/ns-t3af": "^1.0"
       }
   }

When copying patterns into your own extension, keep the same hard ``require``.

System requirements
===================

- TYPO3 **13.4** or **14.3+**
- PHP **>= 8.2**
- **EXT:ns_t3af** activated before verifying backend cards

After installation, follow the checklist in :ref:`developer-index`.
