<?php
require_once('../../config.php');

$courseid = required_param('courseid', PARAM_INT);
$id = optional_param('id', 0, PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_INT);
$course = $DB->get_record('course', array('id' => $courseid));
if (! $course) {
    print_error('invalidcourse', 'block_staffenroll', $courseid);
}

require_login($course);
require_capability(
    'block/staffenroll:managepages',
    context_course::instance($courseid)
);

$staffenrollpage = $DB->get_record('block_staffenroll',
    array('id' => $id)
);
if(! $staffenrollpage) {
    print_error('nopage', 'block_staffenroll', '', $id);
}

$site = get_site();
$PAGE->set_url('/blocks/staffenroll/view.php',
    array('id' => $id, 'courseid' => $courseid)
);
//$heading = $site->fullname . ' :: ' . $course->shortname . ' :: ' . $staffenrollpage->pagetitle;
$heading = implode(' :: ', array(
    $site->fullname,
    $course->shortname,
    $staffenrollpage->pagetitle
));
$PAGE->set_heading($heading);
echo $OUTPUT->header();
if (!$confirm) {
    $optionsno = new moodle_url('/course/view.php',
        array('id' => $courseid)
    );
    $optionsyes = new moodle_url(
        '/blocks/staffenroll/delete.php',
        array(
            'id' => $id,
            'courseid' => $courseid,
            'confirm' => 1,
            'sesskey' => sesskey()
        )
    );
    echo $OUTPUT->confirm(
        get_string(
            'deletepage',
            'block_staffenroll',
            $staffenrollpage->pagetitle
        ),
        $optionsyes, $optionsno
    );
} else {
    if (confirm_sesskey()) {
        if (
            ! $DB->delete_records(
                'block_staffenroll',
                array('id' => $id)
            )
        ) {
            print_error('deleteerror', 'block_staffenroll');
        }
    } else {
        print_error('sessionerror', 'block_staffenroll');
    }
    $url = new moodle_url('/course/view.php',
        array('id' => $courseid)
    );
    redirect($url);
}
echo $OUTPUT->footer();
