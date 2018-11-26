<?php
defined('MOODLE_INTERNAL') || die;

/*
// FIXME: is this needed?
if (! $hassiteconfig) {
    error_log('!! $hassiteconfig false');
    // FIXME: should do somethingelse on error
    $dashboardurl = new moodle_url('/my');
    redirect($dashboard);
}
 */

// roles
// FIXME: maybe these should be function calls from lib
// $courseRoles = staffenroll_getcourseroles()
$courseRoles = array();
$roleids = get_roles_for_contextlevels(CONTEXT_COURSE);
foreach ($roleids as $rid) {
    $dbrole = $DB->get_record('role', array('id' => $rid));
    $courseRoles[$rid] = $dbrole->name;
}

// FIXME: lib?
// $systemRoles = staffenroll_getsystemroles()
$systemRoles = array();
$roleids = get_roles_for_contextlevels(CONTEXT_SYSTEM);
foreach ($roleids as $rid) {
    $dbrole = $DB->get_record('role', array('id' => $rid));
    $systemRoles[$rid] = $dbrole->name;
}


// ENABLE

$settings->add(
    new admin_setting_heading(
        'block_staffenroll/enableroles',
        get_string('enableroleslabel', 'block_staffenroll'),
        get_string('enablerolesdesc', 'block_staffenroll')
    )
);

$settings->add(
    new admin_setting_configselect(
        'block_staffenroll/studentenablerole',
        get_string('studentenableroleslabel', 'block_staffenroll'),
        get_string('studentenablerolesdesc', 'block_staffenroll'),
        NULL,
        $systemRoles
    )
);

$settings->add(
    new admin_setting_configselect(
        'block_staffenroll/staffenablerole',
        get_string('staffenableroleslabel', 'block_staffenroll'),
        get_string('staffenablerolesdesc', 'block_staffenroll'),
        NULL,
        $systemRoles
    )
);


// ASSIGN

$settings->add(
    new admin_setting_heading(
        'block_staffenroll/assignroles',
        get_string('assignroleslabel', 'block_staffenroll'),
        get_string('assignrolesdesc', 'block_staffenroll')
    )
);

// role that is used for student support staff
$settings->add(
    new admin_setting_configselect(
        'block_staffenroll/studentrole',
        get_string('studentroleslabel', 'block_staffenroll'),
        get_string('studentrolesdesc', 'block_staffenroll'),
        NULL,
        $courseRoles
    )
);

// role that is used for full-time support staff
$settings->add(
    new admin_setting_configselect(
        'block_staffenroll/staffrole',
        get_string('staffroleslabel', 'block_staffenroll'),
        get_string('staffrolesdesc', 'block_staffenroll'),
        NULL,
        $courseRoles
    )
);


// ADDITIONAL

$settings->add(
    new admin_setting_heading(
        'block_staffenroll/additional',
        get_string('additionallabel', 'block_staffenroll'),
        get_string('additionaldesc', 'block_staffenroll')
    )
);

// FIXME: validate input for this one
$settings->add(
    new admin_setting_configtext(
        'block_staffenroll/allowednetwork',
        get_string('allowednetworklabel', 'block_staffenroll'),
        get_string('allowednetworkdesc', 'block_staffenroll'),
        '0.0.0.0/0'
    )
);
