<?php

$EM_CONF['ns_t3af_extended'] = [
    'title' => 'AI Foundation T3AF Extended',
    'description' => 'Reference extension demonstrating every EXT:ns_t3af integration hook: custom provider, AI Prompts, AI Features, MCP tools, AI Access, and AiServiceInterface usage.',
    'category' => 'be',
    'author' => 'T3Planet // NITSAN',
    'author_email' => 'info@nitsan.in',
    'author_company' => 'T3Planet // NITSAN',
    'state' => 'beta',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-14.9.99',
        ],
        'conflicts' => [],
        'suggests' => [
            'ns_t3af' => '',
        ],
    ],
];
