<?php
defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/blocks/staffenroll/lib.php');

$courseRoles = staffenroll_getroles();
$systemRoles = staffenroll_getroles('system');


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

$settings->add(
    new admin_setting_configiplist(
        'block_staffenroll/allowednetworks',
        get_string('allowednetworkslabel', 'block_staffenroll'),
        get_string('allowednetworksdesc', 'block_staffenroll'),
        '0.0.0.0/0'
    )
);



$prohibitedCategories = staffenroll_getprohibitedcategorylist();

$settings->add(
    new admin_setting_configmultiselect(
        'block_staffenroll/prohibitedcategories',
        get_string('prohibitedcategorieslabel', 'block_staffenroll'),
        get_string('prohibitedcategoriesdesc', 'block_staffenroll'),
        NULL,
        $prohibitedCategories
    )
);


$settings->add(
    new admin_setting_configduration(
        'block_staffenroll/cacheexpiration',
        get_string('cacheexpirationlabel', 'block_staffenroll'),
        get_string('cacheexpirationdesc', 'block_staffenroll'),
        7776000
    )
);
