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

function block_staffenroll_print_page($staffenroll, $return = false) {
    global $OUTPUT, $COURSE;

    $display = $OUTPUT->heading($staffenroll->pagetitle);
    $display .= $OUTPUT->box_start();
    if($staffenroll->displaydate) {
        $display .= html_writer::start_tag('div',
            array('class' => 'staffenroll displaydate')
        );
        $display .= userdate($staffenroll->displaydate);
        $display .= html_writer::end_tag('div');
    }

    $display .= clean_text($staffenroll->displaytext);

    //close the box
    $display .= $OUTPUT->box_end();

    if ($staffenroll->displaypicture) {
        $display .= $OUTPUT->box_start();
        $images = block_staffenroll_images();
        $display .= $images[$staffenroll->picture];
        $display .= html_writer::start_tag('p');
        $display .= clean_text($staffenroll->description);
        $display .= html_writer::end_tag('p');
        $display .= $OUTPUT->box_end();
    }

    if($return) {
        return $display;
    } else {
        echo $display;
    }
}
