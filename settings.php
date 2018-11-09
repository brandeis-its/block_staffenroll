<?php
defined('MOODLE_INTERNAL') || die;

// FIXME: is this needed?
if (! $hassiteconfig) {
    error_log('!! $hassiteconfig false');
    // FIXME: should do somethingelse on error
    $dashboardurl = new moodle_url('/my');
    redirect($dashboard);
}

// roles
$courseRoles = array();
$roleids = get_roles_for_contextlevels(CONTEXT_COURSE);
foreach ($roleids as $rid) {
    $dbrole = $DB->get_record('role', array('id' => $rid));
    $courseRoles[$rid] = $dbrole->name;
}

$userRoles = array();
$roleids = get_roles_for_contextlevels(CONTEXT_USER);
foreach ($roleids as $rid) {
    $dbrole = $DB->get_record('role', array('id' => $rid));
    $userRoles[$rid] = $dbrole->name;
}

$settings->add(
    new admin_setting_configmulticheckbox(
        'block_staffenroll/instructorroles',
        get_string('instructorroleslabel', 'block_staffenroll'),
        get_string('instructorrolesdesc', 'block_staffenroll'),
        NULL,
        $courseRoles
    )
);


$settings->add(
    new admin_setting_configselect(
        'block_staffenroll/studentenablerole',
        get_string('studentenableroleslabel', 'block_staffenroll'),
        get_string('studentenablerolesdesc', 'block_staffenroll'),
        NULL,
        $userRoles
    )
);

$settings->add(
    new admin_setting_configselect(
        'block_staffenroll/staffenablerole',
        get_string('staffenableroleslabel', 'block_staffenroll'),
        get_string('staffenablerolesdesc', 'block_staffenroll'),
        NULL,
        $userRoles
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
