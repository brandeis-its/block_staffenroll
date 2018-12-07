<?php

// this processing path taken from
// https://docs.moodle.org/dev/Blocks_Advanced
require_once("{$CFG->libdir}/formslib.php");

class staffenroll_browse_form extends moodleform {

    function definition() {

        $mform =& $this->_form;
        $pagehtml = $this->_customdata['pagehtml'];

        $mform->addElement(
            'header',
            'displayinfo',
            get_string('pluginname', 'block_staffenroll')
        );
        $mform->addElement('html', $pagehtml);
    }
}
