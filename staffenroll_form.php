<?php
require_once("{$CFG->libdir}/formslib.php");
require_once($CFG->dirroot.'/blocks/simplehtml/lib.php');

class staffenroll_form extends moodleform {

    function definition() {
        $mform =& $this->_form;
        $mform->addElement('header','displayinfo', get_string('textfields', 'block_staffenroll'));

        // add page title element.
        $mform->addElement('text', 'pagetitle', get_string('pagetitle', 'block_staffenroll'));
        $mform->setType('pagetitle', PARAM_RAW);
        $mform->addRule('pagetitle', null, 'required', null, 'client');

        // add display text field
        $mform->addElement('htmleditor', 'displaytext', get_string('displayedhtml', 'block_staffenroll'));
        $mform->setType('displaytext', PARAM_RAW);
        $mform->addRule('displaytext', null, 'required', null, 'client');

        // add filename selection.
        $mform->addElement('filepicker', 'filename', get_string('file'), null, array('accepted_types' => '*'));

        // add picture fields grouping
        $mform->addElement('header', 'picfield', get_string('picturefields', 'block_staffenroll'), null, false);

        // add display picture yes / no option
        $mform->addElement('selectyesno', 'displaypicture', get_string('displaypicture', 'block_staffenroll'));
        $mform->setDefault('displaypicture', 1);

        // add image selector radio buttons
        $images = block_simplehtml_images();
        $radioarray = array();
        for ($i = 0; $i < count($images); $i++) {
            $radioarray[] =& $mform->createElement('radio', 'picture', '', $images[$i], $i);
        }
        $mform->addGroup($radioarray, 'radioar', get_string('pictureselect', 'block_staffenroll'), array(' '), FALSE);
    }
}
