<?php

require_once('../../config.php');
require_once($CFG->dirroot . '/blocks/staffenroll/lib.php');
require_once($CFG->dirroot . '/blocks/staffenroll/browse_form.php');

// ip validation moved to enroll.php


// get site information to use in the page
$site = get_site();
// set the page settings and navigation
$PAGE->set_context(context_system::instance());
$PAGE->set_url($CFG->wwwroot . '/block/staffenroll/browse.php');
$PAGE->set_heading($site->fullname);
$PAGE->set_title(strip_tags($site->fullname));
$PAGE->set_cacheable(false);

$parentid = optional_param('parentid', '', PARAM_INT);
$courseid = optional_param('courseid', '', PARAM_INT);

$breadcrumbs = staffenroll_getbreadcrumbs($parentid);
foreach ($breadcrumbs as $bc) {
    $PAGE->navbar->add($bc['name'], $bc['href']);
}

// get courses or subcategories
$pagedata = staffenroll_getsubcategories($parentid);
$pagehtml = NULL;
if(! $pagedata) {
    $pagedata = staffenroll_getsubcourses($parentid);
    // FIXME: generate $pagehtml
}
else {
    $pagehtml = staffenroll_getsubcategorylist($pagedata);
}

$args = array(
    'pagehtml' => $pagehtml,
);
$browseform = new staffenroll_browse_form(NULL, $args);
// print the header
echo $OUTPUT->header();
$browseform->display();
echo $OUTPUT->footer();
