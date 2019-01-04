<?php

require_once('../../config.php');
require_once($CFG->dirroot . '/blocks/staffenroll/lib.php');

$site = get_site();
$parentid = optional_param('parentid', 0, PARAM_INT);
$PAGE->set_cacheable(false);

if($parentid) {
    $pageurl = new moodle_url(
        '/blocks/staffenroll/browse.php',
        array('parentid' => $parentid)
    );
    $PAGE->set_url($pageurl);
    $PAGE->set_category_by_id($parentid);
    $PAGE->set_pagetype('course-index-category');
    $PAGE->set_pagelayout('coursecategory');
    require_login();
}
else {
    $pageurl = new moodle_url(
        '/blocks/staffenroll/browse.php'
    );
    $PAGE->set_url($pageurl);
    $PAGE->set_context(context_system::instance());
}

$title = get_string('pluginname', 'block_staffenroll');
$PAGE->set_title($title);
$PAGE->set_heading($title);

$breadcrumbs = staffenroll_getbreadcrumbs($parentid);
foreach ($breadcrumbs as $bc) {
    $PAGE->navbar->add($bc['name'], $bc['href']);
}

// get courses or subcategories
$pagedata = staffenroll_getsubcategories($parentid);
$pagehtml = NULL;
if(! $pagedata) {
    $pagedata = staffenroll_getsubcourses($parentid);
    $pagehtml = staffenroll_getsubcourselist($pagedata, $parentid);
}
else {
    $pagehtml = staffenroll_getsubcategorylist($pagedata);
}

// print the header
echo $OUTPUT->header();
echo $pagehtml;
echo $OUTPUT->footer();
