<?php

class block_staffenroll_edit_form extends block_edit_form {

    protected function specific_definition($mform) {

        // Section header title according to language file.
        $mform->addElement('header', 'config_header', get_string('blocksettings', 'block'));

        $mform->addElement('text', 'config_footer', get_string('blockfooter', 'block_staffenroll'));
        $mform->setDefault('config_footer', 'default footer');
        $mform->setType('config_text', PARAM_RAW);        
    }
}
