<?php
// FIXME: this is for learning only
// perhaps there are other global settings
// that would be applicable
$settings->add(new admin_setting_heading(
    'headerconfig',
    get_string('headerconfig', 'block_staffenroll'),
    get_string('descconfig', 'block_staffenroll')
));

// FIXME: should be global
// this needs to match what's written in block_staffenroll.php
$arbOptions = array(
    0 => get_string('none'),
    1 => get_string('dogs', 'block_staffenroll'),
    2 => get_string('tumbleweeds', 'block_staffenroll'),
    3 => get_string('satellites', 'block_staffenroll')
);

$settings->add(new admin_setting_configselect(
    'block_staffenroll/arbitrary',
    get_string('staffenrollarbitrarydefault', 'block_staffenroll'),
    get_string('configstaffenrollarbitrarydefault', 'block_staffenroll'),
    0,
    $arbOptions)
);
