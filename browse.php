<?php

require_once($CFG->dirroot . '/config.php');
require_once($CFG->dirroot . '/blocks/staffenroll/lib.php');

// ip validation moved to enroll.php

$parentid = optional_param('parent', '', PARAM_INT);
$courseid = optional_param('course', '', PARAM_INT);

// get courses or subcategories
$pagedata = staffenroll_getsubcategories($parentid);
if(! $pagedata) {
    $pagedata = staffenroll_getsubcourses($parentid);
}

// output page

// get site information to use in the page
$site = get_site();

// set the page settings and navigation
$PAGE->set_context(context_system::instance());
$PAGE->set_url($CFG->wwwroot . '/block/staffenroll/browse.php');
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
