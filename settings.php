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
$roles = array();
$contextcourseroles = get_roles_for_contextlevels(CONTEXT_COURSE);
foreach ($contextcourseroles as $roleid) {
    $dbrole = $DB->get_record('role', array('id' => $roleid));
    $roles[$roleid] = $dbrole->name;
}

$settings->add(
    new admin_setting_configmulticheckbox(
        'block_staffenroll/instructorroles',
        get_string('insructorroleslabel', 'block_staffenroll'),
        get_string('instructorrolesdesc', 'block_staffenroll'),
        NULL,
        $roles
    )
);



// role that is used for student support staff
$settings->add(
    new admin_setting_configselect(
        'block_staffenroll/studentrole',
        get_string('studentroleslabel', 'block_staffenroll'),
        get_string('studentrolesdesc', 'block_staffenroll'),
        NULL,
        $roles
    )
);

// role that is used for full-time support staff
$settings->add(
    new admin_setting_configselect(
        'block_staffenroll/staffrole',
        get_string('staffroleslabel', 'block_staffenroll'),
        get_string('staffrolesdesc', 'block_staffenroll'),
        NULL,
        $roles
    )
);
