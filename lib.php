<?php

// get information about support staff roles, and which permission
// is required to enroll in each role
function staffenroll_get_support_roles() {
    $roles = array(
        'student_support' => array(
            'name'   => 'Student',
            'cap'    => 'local/support_staff_enroll:enroll_as_student_support',
            'roleid' => get_config( 'support_staff_enroll',
            'student_support_role' ),
        ),

        'staff_support' => array(
            'name'   => 'Staff',
            'cap'    => 'local/support_staff_enroll:enroll_as_staff_support',
            'roleid' => get_config( 'support_staff_enroll',
            'staff_support_role' ),
        ),
    );

    return $roles;
}

// get a link to the support staff enrollment course browser
function staffenroll_get_all_courses_link() {
    $url = new moodle_url('/local/support_staff_enroll/courses_view.php');

    $link_text = get_string( 'all_courses_link_text',
        'block_staffenroll' );

    return html_writer::link($url, $link_text);
}

// returns 1 if the current user can enroll as some type of support staff
function staffenroll_can_enroll($roles) {
    $context = context_system::instance();

    foreach ($roles as $type => $data) {
        if ( has_capability( $data['cap'], $context) ) {
            return 1;
        }
    }

    return 0;
}

// get existing enrollments for the current user as some kind of support staff
function staffenroll_get_enrollments($roles) {
    global $USER, $DB;

    // get a list of roleids
    $roleids = array_map( function($e) { return $e['roleid']; }, $roles );

    $query  = "select c.id as courseid, c.idnumber as course_idnumber, c.shortname as course_shortname,"
        . " r.name as role_name, r.id as roleid from"
        . " mdl_role r, mdl_role_assignments ra, mdl_context x, mdl_course c"
        . " where r.id in ('"
        . join("','", $roleids) . "')"
        . " and r.id=ra.roleid and ra.userid = " . $USER->id
        . " and ra.contextid = x.id and c.id = x.instanceid"
        . " order by c.idnumber";

    $enrollments = $DB->get_records_sql($query);

    return $enrollments;
}

function populateEnrollLink($ct = array()) {

    // get information about support staff roles, and which permission
    // is required to enroll in each role
    $roles = staffenroll_get_support_roles();

    // if the current user has permission, show the link to find
    // a course to enroll in as a support staff person
    if ( staffenroll_can_enroll($roles) ) {
        $ct[]
            = staffenroll_get_all_courses_link($roles);
    }

    // get existing support staff enrollments
    $enrollments = staffenroll_get_enrollments($roles);

    // add links to courses the user is currently enrolled as support staff
    foreach ($enrollments as $enrollment) {
        $url = new moodle_url( '/course/view.php', array( 'id' => $enrollment->courseid ) );

        $course_label = $enrollment->course_shortname or $enrollment->courseid;
        $link_text = implode( ' ', array(
            $course_label,
            '(' . $enrollment->role_name . ')'
        ) );

        $ct[] = html_writer::link($url, $link_text);
    }

    return $ct;
}
// end
