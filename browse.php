<?php

require_once($CFG->dirroot . '/config.php');
require_once($CFG->dirroot . '/blocks/staffenroll/lib.php');

global $DB;

$ok = staffenroll_validatenetworkhost();

$parentid = optional_param('parent', '', PARAM_INT);
$courseid = optional_param('course', '', PARAM_INT);

/*
 * FIXME: handle unenrol somewhere else

// this handles if they are unenrolling
// unenroll them then redirect to the main page
$action = isset($_REQUEST['enrl_action']) ? $_REQUEST['enrl_action'] : NULL;
if (isset($action) && $action == 'unenroll') {
    $course = $DB->get_record('course', array('id' => $courseid));
    if (!$course) {
        print_error('cannot_retrieve_course', 'local_support_staff_enroll');
    }

    support_staff_enroll_enroll_user($USER, $course, '', 'unenroll');

    redirect($CFG->wwwroot);
}
 */

/*
 * FIXME: this might be better handled in another file

 // if they chose to enroll, enroll them then redirect to course
if (isset($_REQUEST['enroll'])) {
    $type = $_REQUEST['enrl_type'];

    if (!isset($courseid) || !isset($type)) {
        print_error('must_supply_crs_and_enrl_type',
                     'local_support_staff_enroll');
    }

    $enrollments = support_staff_enroll_get_enrollments($USER->id);
    if (isset($enrollments[$courseid])) {
        print_error('already_enrolled', 'local_support_staff_enroll');
    }

    $allowed = support_staff_enroll_can_enroll_as($type, $env);
    if (!$allowed) {
        print_error('no_permission_enroll', 'local_support_staff_enroll');
    }

    $course = $DB->get_record('course', array('id' => $courseid));
    if (!$course) {
        print_error('cannot_retrieve_course', 'local_support_staff_enroll');
    }

    support_staff_enroll_enroll_user($USER, $course, $type);

    redirect($CFG->wwwroot.'/course/view.php?id='.$course->id);
}
 */

// get courses and subcategories
$subcategories = staffenroll_getsubcategories($parentid);
// FIXME: $env ? c'mon ...
$courses = staffenroll_getcourses($parentid, $USER->id, $env);

// output page

// get site information to use in the page
$site = get_site();

// set the page settings and navigation
$PAGE->set_context(context_system::instance());
$PAGE->set_url($CFG->wwwroot . '/block/staffenroll/browsecourses.php');
$PAGE->set_heading($site->fullname);
$PAGE->set_title(strip_tags($site->fullname));
$PAGE->set_cacheable(false);

$breadcrumbs = staffenroll_getbreadcrumbs($parentid);
foreach ($breadcrumbs as $bc) {
    $PAGE->navbar->add($bc['name'], $bc['href']);
}

// print the header
echo $OUTPUT->header();

echo html_writer::tag('h2', get_string('pluginname',
                                        'local_support_staff_enroll'));

if ($subcategories || $courses) {
    if ($subcategories) {
        echo support_staff_enroll_get_subcats_table($subcategories);
    }

    if ($courses) {
        echo support_staff_enroll_get_courses_table($courses);
    }
} else {
    $msg = get_string('no_courses_or_subcats', 'local_support_staff_enroll');
    echo html_writer::tag('p', $msg);
}

// Print the footer
echo $OUTPUT->footer();

?>
