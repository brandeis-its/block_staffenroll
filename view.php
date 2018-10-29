<?php

require_once('../../config.php');
require_once('staffenroll_form.php');

global $DB, $OUTPUT, $PAGE;

// Check for all required variables.
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('blockid', PARAM_INT);

// Next look for optional variables.
$id = optional_param('id', 0, PARAM_INT);


if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourse', 'block_staffenroll', $courseid);
}
require_login($course);

$PAGE->set_url('/blocks/staffenroll/view.php', array('id' => $courseid));
$PAGE->set_pagelayout('standard');
$PAGE->set_heading(get_string('edithtml', 'block_staffenroll'));

$settingsnode = $PAGE->settingsnav->add(
    get_string('staffenrollsettings', 'block_staffenroll')
);
$editurl = new moodle_url('/blocks/staffenroll/view.php',
    array('id' => $id, 'courseid' => $courseid, 'blockid' => $blockid)
);
$editnode = $settingsnode->add(
    get_string('editpage', 'block_staffenroll'), $editurl
);
$editnode->make_active();
$staffenroll = new staffenroll_form();

// pass data to form
$toform['blockid'] = $blockid;
$toform['courseid'] = $courseid;
$staffenroll->set_data($toform);

if($staffenroll->is_cancelled()) {
    // Cancelled forms redirect to the course main page.
    $courseurl = new moodle_url('/course/view.php', array('id' => $id));
    redirect($courseurl);
} else if ($fromform = $staffenroll->get_data()) {
    // We need to add code to appropriately act on and store the submitted data
    if (!$DB->insert_record('block_simplehtml', $fromform)) {
        print_error('inserterror', 'block_staffenroll');
    }
    else {
        // but now we just print it out for debugging
        print_object($fromform);
    }
    // but for now we will just redirect back to the course main page.
    // $courseurl = new moodle_url('/course/view.php', array('id' => $courseid));
    // redirect($courseurl);
} else {
    // form didn't validate or this is the first display
    $site = get_site();
    echo $OUTPUT->header();
    $staffenroll->display();
    echo $OUTPUT->footer();
}
