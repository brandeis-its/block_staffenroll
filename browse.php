<?php

require_once('../../config.php');
require_once($CFG->dirroot . '/blocks/staffenroll/lib.php');
require_once($CFG->dirroot . '/blocks/staffenroll/browse_form.php');

// ip validation moved to enroll.php
// "logic" mostly stolen from course/index.php



$site = get_site();
$title = get_string('block_staffenroll', 'pluginname');
$PAGE->set_title($title);
$PAGE->set_heading($title);
$pageurl = new moodle_url(
    '/blocks/staffenroll/browse.php',
    array('parentid' => $parentid)
);
$PAGE->set_url($pageurl);
$PAGE->set_cacheable(false);

$parentid = optional_param('parentid', 0, PARAM_INT);
if($parentid) {
    $PAGE->set_category_by_id($parentid);
    $PAGE->set_pagetype('course-index-category');
    $PAGE->set_pagelayout('coursecategory');
}
else {
    $PAGE->set_context(context_system::instance());
}

$breadcrumbs = staffenroll_getbreadcrumbs($parentid);
foreach ($breadcrumbs as $bc) {
    $PAGE->navbar->add($bc['name'], $bc['href']);
}

// get courses or subcategories
$pagedata = staffenroll_getsubcategories($parentid);
$pagehtml = NULL;
if(! $pagedata) {
    $pagedata = staffenroll_getsubcourses($parentid);
    $pagehtml = staffenroll_getsubcourselist($pagedata);
}
else {
    $pagehtml = staffenroll_getsubcategorylist($pagedata);
}

// print the header
echo $renderer->header();
echo $pagehtml;
echo $renderer->footer();
