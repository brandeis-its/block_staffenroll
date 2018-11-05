<?php
defined('MOODLE_INTERNAL') || die();

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

    'block/staffenroll:viewpages' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'guest' => CAP_PREVENT,
            'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'coursecreator' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        )
    ),

    'block/staffenroll:managepages' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'guest' => CAP_PREVENT,
            'student' => CAP_PREVENT,
            'teacher' => CAP_PREVENT,
            'editingteacher' => CAP_ALLOW,
            'coursecreator' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        )
    )
);
