<?php
function block_staffenroll_images() {
    return array(
        html_writer::tag(
            'img', '',
            array('alt' => get_string('red', 'block_staffenroll'),
            'src' => "pix/red.png")
        ),
        html_writer::tag(
            'img', '',
            array('alt' => get_string('blue', 'block_staffenroll'),
            'src' => "pix/blue.png")
        ),
        html_writer::tag(
            'img', '',
            array('alt' => get_string('green', 'block_staffenroll'),
            'src' => "pix/green.png")
        )
    );
}
