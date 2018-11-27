<?php
$capabilities = array(
    'block/staffenroll:studentenroll' => array(
        'riskbitmask'  => RISK_SPAM | RISK_PERSONAL,
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
    ),
    'block/staffenroll:staffenroll' => array(
        'riskbitmask'  => RISK_SPAM | RISK_PERSONAL,
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
    ),
    'block/staffenroll:addinstance' => array(
        'riskbitmask'  => RISK_SPAM | RISK_XSS,
        'captype'      => 'write',
        // was CONTEXT_BLOCK
        'contextlevel' => CONTEXT_COURSE,
        'clonepermissionsfrom' => 'moodle/site:manageblocks',
    ),
    'block/staffenroll:myaddinstance' => array(
        'captype'              => 'write',
        'contextlevel'         => CONTEXT_SYSTEM,
        'clonepermissionsfrom' => 'moodle/my:manageblocks',
    )
);
