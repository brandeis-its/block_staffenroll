require_once("{$CFG->libdir}/formslib.php");

class staffenroll_form extends moodleform {

    function definition() {
        $mform =& $this->_form;
        $mform->addElement('header','displayinfo', get_string('textfields', 'block_staffenroll'));
    }
}
