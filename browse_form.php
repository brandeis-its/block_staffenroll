<?php

// this processing path taken from
// https://docs.moodle.org/dev/Blocks_Advanced
require_once("{$CFG->libdir}/formslib.php");

class staffenroll_browse_form extends moodleform {

    function definition() {

        $mform =& $this->_form;
        $pagedata = $this->_customdata['pagedata'];

        $mform->addElement(
            'header',
            'displayinfo',
            get_string('pluginname', 'block_staffenroll')
        );
        $mform->addElement('html', "<pre>$pagedata</pre>");
    }
}

/*
 * FIXME: display code from browse.php
 * needs to be reworked for form

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

 */
