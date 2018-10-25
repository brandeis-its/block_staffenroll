<?php

class block_staffenroll_edit_form extends block_edit_form {

    protected function specific_definition($mform) {

        // Section header title according to language file.
        $mform->addElement('header', 'config_header', get_string('blocksettings', 'block_staffenroll'));

        $mform->addElement('text', 'config_title', get_string('blocktitle', 'block_simplehtml'));
        $mform->setDefault('config_title', 'default config title');
        $mform->setType('config_title', PARAM_TEXT);

        $mform->addElement('text', 'config_footer', get_string('blockfooter', 'block_staffenroll'));
        $mform->setDefault('config_footer', 'default config footer');
        $mform->setType('config_footer', PARAM_RAW);
    }
}
