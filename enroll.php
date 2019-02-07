<?php

require_once('../../config.php');
require_once($CFG->dirroot . '/blocks/staffenroll/lib.php');

global $DB;

$site = get_site();

$courseid = required_param('courseid', PARAM_INT);
$parentid = required_param('parentid', PARAM_INT);

$coursecontext = context_course::instance($courseid);
$PAGE->set_context($coursecontext);

$pageurl = new moodle_url(
    '/blocks/staffenroll/enroll.php',
    array('courseid' => $courseid, 'parentid' => $parentid)
);
$PAGE->set_url($pageurl);

$titleParts = array();
$titleParts[] = get_string('pluginname', 'block_staffenroll');
$titleParts[] = get_string('course', 'block_staffenroll');
$titleParts[] = $courseid;
$title = implode(': ', $titleParts);
$PAGE->set_title($title);
$PAGE->set_heading($title);

$breadcrumbs = staffenroll_getbreadcrumbs($parentid);
foreach ($breadcrumbs as $bc) {
    $PAGE->navbar->add($bc['name'], $bc['href']);
}

// CHECKS

$errors = array();
// validate IP addr
$ok = staffenroll_validatenetworkhost();
if(! $ok) {
    $msg = implode(': ', array(
        get_string('invalidip', 'block_staffenroll'),
        $_SERVER['REMOTE_ADDR']
    ));
    $errors[] = $msg;
}


// avoid duplicate enrollment
$enrollments = array();
if(isset($_SESSION['block_staffenroll']['userstaffenrollments'])) {
    $enrollments = $_SESSION['block_staffenroll']['userstaffenrollments'];
}
else {
    $enrollments = staffenroll_getuserstaffenrollments();
}
foreach($enrollments as $e) {
    if($e['id'] == $courseid) {
        $msg = implode(': ', array(
            get_string('alreadyenrolled', 'block_staffenroll'),
            $courseid
        ));
        $errors[] = $msg;
        break;
    }
}

// able to enroll as staff/student
$ccIdx = 'canenrollcourse' . $courseid;
$canenroll = NULL;
if(isset($_SESSION['block_staffenroll'][$ccIdx])) {
    $canenroll = $_SESSION['block_staffenroll'][$ccIdx];
}
else {
    $canenroll = staffenroll_canenroll($courseid);
    $_SESSION['block_staffenroll'][$ccIdx] = $canenroll;
}

if($canenroll == 'none') {
    $msg = implode(' ', array(
        $USER->username,
        get_string('insufficientpermissions', 'block_staffenroll')
    ));
    $errors[] = $msg;
}

// course exists
$course = $DB->get_record('course', array('id' => $courseid));
if (!$course) {
    $msg = implode(' ', array(
        get_string('missingcourse', 'block_staffenroll'),
        $courseid
    ));
    $errors[] = $msg;
}

// try enrolling
if(count($errors) < 1) {
    $errors = staffenroll_enroll($courseid, $canenroll);
}

if(count($errors) > 0) {
    $errorHTML = staffenroll_generateerrorlist($errors);
    echo $OUTPUT->header();
    echo html_writer::alist($errors);
    echo $OUTPUT->footer();
}

$courseUrl = implode('', array(
    $CFG->wwwroot,
    '/couse/view.php?id=',
    $courseid
));
redirect($couseUrl);

