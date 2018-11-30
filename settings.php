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
$currentCategories = array(
    'special courses' => array(
        'special 1' => 'sp1',
        'special 2' => 'sp2',
        'special 3' => 'sp3'
    ),
    'fall 2018' => array(
        'college of arts &amp; sciences' => array(
            'african studies' => array(
                'african studies 1' => 'as1',
                'african studies 2' => 'as2',
                'african studies 3' => 'as3'
            ),
            'yiddish' => array(
                'yiddish curses' => 'y1',
                'yiddish blessings' => 'y2'
            )
        ),
        'international business school' => array(
            'management' => array(
                'management 1' => 'mn1',
                'management 2' => 'mn2',
                'white collar crime' => 'mn3'
            ),
            'marketing' => array(
                'bullshitting' => 'mk1',
                'bilking the client' => 'mk2',
                'stalling' => 'mk3'
            )
        )
    )
);

$settings->add(
    new admin_setting_configmultiselect(
        'block_staffenroll/prohibitedcategories',
        get_string('prohibitedcategorieslabel', 'block_staffenroll'),
        get_string('prohibitedcategoriesdesc', 'block_staffenroll'),
        NULL,
        $currentCategories
    )
);
