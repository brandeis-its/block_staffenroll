<?php

require_once($CFG->dirroot . '/config.php');
require_once($CFG->dirroot . '/blocks/staffenroll/lib.php');
require_once($CFG->dirroot . '/blocks/staffenroll/browse_form.php');

// ip validation moved to enroll.php

$parentid = optional_param('parent', '', PARAM_INT);
$courseid = optional_param('course', '', PARAM_INT);

// get courses or subcategories
$pagedata = staffenroll_getsubcategories($parentid);
if(! $pagedata) {
    $pagedata = staffenroll_getsubcourses($parentid);
}

// DEBUG
// should actually send in raw pagedata
$dbug = var_export($pagedata, true);
$args = array(
    'pagedata' => $dbug,
);
$browseform = new staffenroll_browse_form(NULL, $args);

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
$browseform->display();
echo $OUTPUT->footer();
