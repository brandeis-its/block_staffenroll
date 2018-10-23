<?php

$capabilities = array(

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
    ),
);
