<?php

// SETTINGS

function staffenroll_getcourseroles() {
    $courseRoles = array();
    $roleids = get_roles_for_contextlevels(CONTEXT_COURSE);
    foreach ($roleids as $rid) {
        $dbrole = $DB->get_record('role', array('id' => $rid));
        $courseRoles[$rid] = $dbrole->name;
    }
    return $courseRoles;
}

function staffenroll_getsystemroles() {
    $systemRoles = array();
    $roleids = get_roles_for_contextlevels(CONTEXT_SYSTEM);
    foreach ($roleids as $rid) {
        $dbrole = $DB->get_record('role', array('id' => $rid));
        $systemRoles[$rid] = $dbrole->name;
    }
    return $systemRoles;
}


// BLOCK

// returns true if the current user can enroll as some type of support staff
function staffenroll_canenroll($courseid) {
    $context = context_course::instance($courseid);
    $enroll =  has_capability(
        'block/staffenroll:staffenroll',
        $context
    );
    if($enroll) {
        return true;
    }
    $enroll =  has_capability(
        'block/studentenroll:studentenroll',
        $context
    );
    if($enroll) {
        return true;
    }

    return false;
}

// get existing enrollments for the current user as some kind of support staff
function staffenroll_getenrollments() {
    global $USER, $DB;
    $courseroles = staffenroll_getcourseroles();

    // get a list of roleids
    $roleids = array_map(
        function($e) { return $e['roleid']; },
        $courseroles
    );
    $totalroleids = count($roleids);
    $roleidsql = '';
    if($totalroleids == 0) {
        // FIXME: this is an error
        // handle it in some way
        return false;
    }
    else if($totalroleids == 1){
        $roleidsql = implode(' ', array(
            'where roleid =',
            $roleids[0]
        ));
    }
    else {
        $roleidsql = implode(' ', array(
            'where roleid in (',
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
    foreach($enrollments as $enroll) {
        $url = new moodle_url(
            '/course/view.php',
            array('id' => $enrollment->courseid)
        );
        $course_label = $enrollment->course_shortname or $enrollment->courseid;
        $link_text = implode(' ', array(
            $course_label,
            '(' . $enrollment->role_name . ')'
        ));
        $ct[] = html_writer::link($url, $link_text);
    }
    return $ct;
}
