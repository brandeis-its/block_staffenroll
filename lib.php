<?php

// SETTINGS

function staffenroll_getcourseroles() {
    global $DB;
    $courseRoles = array();
    $roles = get_roles_for_contextlevels(CONTEXT_COURSE);

    // $rclid Role Context Level ID
    foreach ($roles as $rclid => $rid) {
        $dbrole = $DB->get_record('role', array('id' => $rid));
        $courseRoles[$rid] = $dbrole->name;
    }
    return $courseRoles;
}

function staffenroll_getsystemroles() {
    global $DB;
    $systemRoles = array();
    $roles = get_roles_for_contextlevels(CONTEXT_SYSTEM);
    foreach ($roles as $rclid => $rid) {
        $dbrole = $DB->get_record('role', array('id' => $rid));
        $systemRoles[$rid] = $dbrole->name;
    }
    return $systemRoles;
}


// BLOCK

// returns true if the current user can enroll as some type of support staff
function staffenroll_canenroll($courseid) {
    $context = context_course::instance($courseid);
    $capabilities = array(
        'block/staffenroll:staffenroll',
        'block/studentenroll:studentenroll'
    );
    foreach($capabilities as $c) {
        $enroll =  has_capability($c, $context);
        if($enroll) {
            return true;
        }
    }
    return false;
}

// get existing enrollments for the current user as some kind of support staff
function staffenroll_getenrollments() {
    global $USER, $DB;
    $courseroles = staffenroll_getcourseroles();
    // roleids
    $roleids = array_keys($courseroles);
    $totalroleids = count($roleids);
    $roleidsql = '';
    if($totalroleids == 0) {
        $error = 'no matching roles in db';
        $sql = 'staffenroll_getcourseroles()';
        throw new dml_read_exception($error, $sql);
    }
    else if($totalroleids == 1){
        $roleidsql = implode(' ', array(
            'where r.id =',
            $roleids[0]
        ));
    }
    else {
        $roleidsql = implode(' ', array(
            'where r.id in (',
            join(', ', $roleids),
            ')'
        ));
    }

    $query = implode(' ', array(
        "SELECT c.id AS courseid, c.idnumber AS course_idnumber,",
        "c.shortname AS course_shortname, r.name AS role_name,",
        "r.id AS roleid",
        "FROM mdl_role r, mdl_role_assignments ra,",
        " mdl_context x, mdl_course c",
        "WHERE",
        $roleidsql,
        "AND r.id=ra.roleid",
        "AND ra.userid =",
        $USER->id,
        "AND ra.contextid = x.id AND c.id = x.instanceid",
        "ORDER BY c.idnumber"
    ));
    $enrollments = $DB->get_records_sql($query);
    return $enrollments;
}

function populateEnrollLink($ct = array(), $courseid = 0) {
    if(! staffenroll_canenroll($courseid)) {
        $ct[] = 'no permission to enroll';
        return;
    }

    $url = new moodle_url(
        '/local/support_staff_enroll/courses_view.php'
    );
    $link_text = get_string(
        'allcourseslink',
        'block_staffenroll'
    );
    $ct[] = html_writer::link($url, $link_text);

    // get existing support staff course enrollments
    $enrollments = staffenroll_getenrollments();

    // add links to courses the user is currently enrolled as support staff
    foreach($enrollments as $e) {
        $url = new moodle_url(
            '/course/view.php',
            array('id' => $e->courseid)
        );
        $course_label = $e->course_shortname or $e->courseid;
        $link_text = implode(' ', array(
            $course_label,
            '(' . $e->role_name . ')'
        ));
        $ct[] = html_writer::link($url, $link_text);
    }
    return $ct;
}
