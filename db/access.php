<?php
$capabilities = array(
    'block/staffenroll:studentenableenrol' => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
    ),
    'block/staffenroll:staffenableenroll' => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
    ),
    'block/staffenroll:studentenroll' => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
    ),
    'block/staffenroll:staffenroll' => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
    ),
    'block/staffenroll:addinstance' => array(
        'riskbitmask'  => RISK_SPAM | RISK_XSS,
        'captype'      => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'clonepermissionsfrom' => 'moodle/site:manageblocks',
    ),
    'block/staffenroll:myaddinstance' => array(
        'captype'              => 'write',
        'contextlevel'         => CONTEXT_SYSTEM,
        'clonepermissionsfrom' => 'moodle/my:manageblocks',
    )
);
