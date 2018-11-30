<?php
defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/blocks/staffenroll/lib.php');

/*
// FIXME: is this needed?
if (! $hassiteconfig) {
    error_log('!! $hassiteconfig false');
    // FIXME: should do somethingelse on error
    $dashboardurl = new moodle_url('/my');
    redirect($dashboard);
}
 */

$courseRoles = staffenroll_getcourseroles();
$systemRoles = staffenroll_getsystemroles();


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



// FIXME: this is just to see if nested categories work
// in the manner that i expect them to
$currentCategories = staffenroll_getcurrentcategories();

$settings->add(
    new admin_setting_configmultiselect(
        'block_staffenroll/prohibitedcategories',
        get_string('prohibitedcategorieslabel', 'block_staffenroll'),
        get_string('prohibitedcategoriesdesc', 'block_staffenroll'),
        NULL,
        $currentCategories
    )
);
