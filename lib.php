<?php

// SETTINGS

function staffenroll_getcourseroles() {
    global $DB;
    $courseRoles = array();
    $roles = get_roles_for_contextlevels(CONTEXT_COURSE);

    // $rclid Role Context Level ID
    foreach($roles as $rclid => $rid) {
        $dbrole = $DB->get_record('role', array('id' => $rid));
        $courseRoles[$rid] = $dbrole->name;
    }
    return $courseRoles;
}

function staffenroll_getsystemroles() {
    global $DB;
    $systemRoles = array();
    $roles = get_roles_for_contextlevels(CONTEXT_SYSTEM);
    foreach($roles as $rclid => $rid) {
        $dbrole = $DB->get_record('role', array('id' => $rid));
        $systemRoles[$rid] = $dbrole->name;
    }
    return $systemRoles;
}

function staffenroll_getcurrentcategories() {
    global $DB;
    $cc = array();
    $categoryidname = cache::make('block_staffenroll', 'categoryidname');
    $generated = $categoryidname->get('generated');
    if($generated === false) {
        $generated = 0;
    }
    $now = time();
    $categoryidnameexpiration = get_config('block_staffenroll', 'categoryidnameexpiration');
    $secondsdiff = $now - $generated;
    if($secondsdiff < $categoryidnameexpiration) {
        // do math to see if it's longer than default
        $cc = $categoryidname->get('currentcategories');
        if($cc != false) {
            return $cc;
        }
    }

    // processing depends on path order of categories
    $results = $DB->get_records(
        'course_categories',
        array('visible' => 1),
        'path',
        'id,name,depth,path'
    );
    // maps ids to names so that paths can be "decoded"
    $displayname = NULL;
    foreach($results as $r) {
        $processedname = html_entity_decode($r->name);
        $cachename = $categoryidname->get($r->id);
        // processing could be sped up
        // by skipping check against processedname
        if(
            $cachename === false
            or
            $cachename != $processedname
        ) {
            $categoryidname->set($r->id, $processedname);
        }
        if($r->depth == 1) {
            $displayname = $processedname;
        }
        else {
            $path = explode("/", $r->path);
            // remove last element ($processedname)
            $catnames = array();
            foreach($path as $p) {
                $cachename = $categoryidname->get($p);
                if($cachename === false) {
                    $cachename = 'catid' . $p;
                }
                $catnames[] = $cachename;
            }
            $displayname = implode(':', $catnames);
        }
        $cc[$r->id] = $displayname;
    }
    // $categoryidname
    $categoryidname->set('generated', $now);
    $categoryidname->set('currentcategories', $cc);
    return $cc;
}


// BLOCK

// returns true if the current user can enroll as some type of support staff
// FIXME: this should return one of three values
// student, staff, none
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
    $roleids = array_keys($courseroles);
    $totalroleids = count($roleids);
    $roleidsql = '';
    if($totalroleids == 0) {
        $error = 'no matching roles in db';
        $sql = 'staffenroll_getcourseroles()';
        error_log($error);
        throw new dml_read_exception($error, $sql);
    }
    else if($totalroleids == 1){
        $roleidsql = implode(' ', array(
            'r.id =',
            $roleids[0]
        ));
    }
    else {
        $roleidsql = implode(' ', array(
            'r.id in(',
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



// BROWSECOURSES

function staffenroll_getsubcategories($parentid) {
    global $DB;

    /*
     * FIXME: delete this once we know new SQL works
     $query = "select id,name,description from mdl_course_categories "
     . "where parent = ? "
     . "order by sortorder";

    $results = $DB->get_records_sql( $query, array($parentid) );
     */

    $results = $DB->get_records(
        'course_categories',
        array('parent' => $parentid),
        'sortorder',
        'id, name, description'
    );

    $categories = array();
    foreach($results as $r) {
        $categories[] = array(
            'id'    => $r->id,
            'name'  => $r->name,
            'description' => $r->description
        );
    }
    return $categories;
}


function staffenroll_getuserenrollments($userid) {
    global $DB;

    $query = implode(" ", array(
        "SELECT ra.id, c.id AS courseid, r.name",
        "FROM mdl_role_assignments ra, mdl_role r,",
        "mdl_course c, mdl_context cx",
        "WHERE ra.userid = ? AND ra.roleid = r.id",
        "AND ra.contextid = cx.id",
        "AND cx.instanceid = c.id AND cx.contextlevel = ?"
    ));

    $results = $DB->get_records_sql(
        $query,
        array($userid, CONTEXT_COURSE)
    );

    $enrollments = array();
    foreach($results as $r) {
        if(isset($enrollments[$r->courseid])) {
            $enrollments[$r->courseid][] =
                array('role' => $r->name);
        }
        else {
            $enrollments[$r->courseid] = array();
            $enrollments[$r->courseid][] =
                array('role' => $r->name);
        }
    }
    return $enrollments;
}

/*
 * FIXME: i don't think this is needed at all
 function staffenroll_getpermissions($env) {
     global $capabilities;

     $context = context_system::instance();

     $permissions = array();
     foreach($capabilities as $type => $capability) {
         $permissions[ 'can_' . $type ]
             = support_staff_enroll_can_enroll_as($type, $env);
     }

     return $permissions;
}
 */


//$courses = staffenroll_getcourses($parentid, $USER->id, $env);
function staffenroll_getcourses($pid, $userid, $env) {
    global $DB;
    $dbCourses = $DB->get_records(
        'course',
        array('category' => $pid),
        'sortorder'
    );
/*
    if(! $courses ) {
        return array();
    }
 */
    $enrollments = staffenroll_getuserenrollments($userid);

    $courses = array();
    foreach($dbCourses as $c) {
        $ok = staffenroll_canenroll($c->id);
        if($c->id == 1 or ! $ok) {
            // homepage not course
            continue;
        }

        $roles = NULL;
        if(isset($enrollments[$c->id])) {
            $roles = $enrollments[$c->id];
        }

        // array_push( $output, array_merge($data, $permissions) );
        $courses[] = array(
            'id'        => $c->id,
            'idnumber'  => $c->idnumber,
            'shortname' => $c->shortname,
            'summary'   => $c->summary,
            //'teachers'  => support_staff_enroll_get_instructors($course->id),
            'roles'     => $roles,
        );
    }
    return $courses;
}


function staffenroll_getbreadcrumbs($categoryid) {
    global $DB;

    $breadcrumbs = array();
    $linktext = get_string('breadcrumblinktext', 'staffenroll');
    $breadcrumbs[] = array(
        'name' => $link_text,
        'href' => 'browsecourses.php',
    );

    $category = $DB->get_record(
        'course_categories',
        array('id' => $categoryid)
    );

    // FIXME: this should be an exception
    if(! $category) {
        error_log('ERR: no categories returned from id: ' . $categoryid);
        return $breadcrumbs;
    }

    $categoryids = explode("/", $category->path);
    foreach($categoryids as $id) {
        $results = $DB->get_record('course_categories', array('id' => $id));
        if(! $results) {
            // FIXME: throw exception
            error_log('missing category for id: ' . $id);
        }
        $href = new moodle_url(
            '/blocks/staffenroll/browsecourses.php',
            array('parent' => $results->id)
        );

        $breadcrumbs[] = array(
            'name' => $results->name,
            'href' => $href,
        );
    }
    return $breadcrumbs;
}
