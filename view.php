<?php

require_once('../../config.php');
require_once('staffenroll_form.php');

global $DB, $OUTPUT, $PAGE;

// Check for all required variables.
$courseid = required_param('courseid', PARAM_INT);

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourse', 'block_staffenroll', $courseid);
}

require_login($course);
$PAGE->set_url('/blocks/staffenroll/view.php', array('id' => $courseid));
$PAGE->set_pagelayout('standard');
$PAGE->set_heading(get_string('edithtml', 'block_staffenroll'));

$staffenroll = new staffenroll_form();
echo $OUTPUT->header();
$staffenroll->display();
echo $OUTPUT->footer();
