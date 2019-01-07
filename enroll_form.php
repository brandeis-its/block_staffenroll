<?php


require_once("{$CFG->libdir}/formslib.php");

class enroll_form extends moodleform {

    function definition() {

        $mform =& $this->_form;
        $mform->addElement('header','displayinfo', get_string('enrollheader', 'block_staffenroll'));
    }
}
