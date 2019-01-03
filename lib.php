<?php

// SETTINGS

function staffenroll_getroles($t = 'course') {
    global $DB;
    $contextRoles = array();
    if($t == 'course') {
        $contextRoles = get_roles_for_contextlevels(CONTEXT_COURSE);
    }
    elseif($t = 'system') {
        $contextRoles = get_roles_for_contextlevels(CONTEXT_SYSTEM);
    }
    else{
        error_log('!!! invalid type for staffenroll_getroles: ' . $t);
        return $contextRoles;
    }

    // $rclid Role Context Level ID
    $roles = array();
    foreach($contextRoles as $rclid => $rid) {
        $dbrole = $DB->get_record('role', array('id' => $rid));
        $roles[$rid] = $dbrole->name;
    }
    return $roles;
}


// time stamp key
function staffenroll_unexpiredcache($tsk) {
    $coursescategories = cache::make('block_staffenroll', 'coursescategories');
    $timestamp = $coursescategories->get($tsk);
    if($timestamp === false) {
        error_log('!!! invalid timestamp key: ' . $tsk);
        return false;
    }
    $now = time();
    $cacheexpiration = get_config('block_staffenroll', 'cacheexpiration');
    $secondsdiff = $now - $timestamp;

    // FIXME: this is just to verify expiration logic is working as expected
    // remove before release
    $error = array(
        '!!!  $timestamp: ' . $timestamp,
        '$now: ' . $now,
        '$secondsdiff: ' . $secondsdiff,
        '$cacheexpiration: ' . $cacheexpiration
    );
    $msg = implode(', ', $error);
    error_log($msg);

    if($secondsdiff < $cacheexpiration) {
        return true;
    }
    return false;
}

function staffenroll_getprohibitedcategorieslist() {
    global $DB;

    $coursescategories = cache::make('block_staffenroll', 'coursescategories');
    $cachekey = 'prohibitedcategorieslist';
    $cachetimestamp = 'pclgenerated';
    $ok = staffenroll_unexpiredcache($cachetimestamp);
    if($ok) {
        $pcl = $coursescategories->get($cachekey);
        if($pcl) {

            // FIXME: debugging, remove before release
            error_log("!!! returning cached prohibited categories");

            return $pcl;
        }
    }
    $pcl = array();

    // processing depends on path order of categories
    $results = $DB->get_records(
        'course_categories',
        array('visible' => 1),
        'path',
        'id, name, depth, path'
    );

    // maps ids to names so that paths can be "decoded"
    $displayname = NULL;
    foreach($results as $r) {
        $processedname = html_entity_decode($r->name);
        $idx = 'cat' . $r->id;
        $cachename = $coursescategories->get($idx);
        // processing could be sped up
        // by skipping check against processedname
        if(
            $cachename === false
            or
            $cachename != $processedname
        ) {
            $coursescategories->set($idx, $processedname);
        }

        if($r->depth == 1) {
            $displayname = $processedname;
        }
        else {
            $path = explode("/", $r->path);
            // remove empty first element
            array_shift($path);
            $catnames = array();
            foreach($path as $catid) {
                $pidx = 'cat' . $catid;
                $cachename = $coursescategories->get($pidx);
                if($cachename === false) {
                    $cachename = $pidx;
                }
                $catnames[] = $cachename;
            }
            $displayname = implode(':', $catnames);
        }
        $pcl[$idx] = $displayname;
    }
    $now = time();
    $coursescategories->set($cachetimestamp, $now);
    $coursescategories->set($cachekey, $pcl);

    // FIXME: debugging, remove before release
    error_log("!!! returning generated prohibited categories");

    return $pcl;
}


// BLOCK

// returns true if the current user can enroll as some type of support staff
function staffenroll_canenroll($courseid = 0) {
    global $USER;
    $context = NULL;
    // no courseid means user context
    if($courseid == 0) {
        $context = context_user::instance($USER->id);
    }
    else {
        $context = context_course::instance($courseid);
    }
    $capabilityrole = array(
        array(
            'capability' => 'block/staffenroll:staffenroll',
            'role' => 'staff'
        ),
        array(
            'capability' => 'block/staffenroll:studentenroll',
            'role' => 'student'
        )
    );
    foreach($capabilityrole as $cr) {
        $enroll =  has_capability($cr['capability'], $context);
        if($enroll) {
            return $cr['role'];
        }
    }
    return 'none';
}

// get existing enrollments for the current user as some kind of support staff
function staffenroll_getuserenrollments($userid = 0) {
    global $DB, $USER;
    $roleids = array();
    $tmp = get_config('block_staffenroll', 'staffrole');
    $roleids[] = intval($tmp);
    $tmp = get_config('block_staffenroll', 'studentrole');
    $roleids[] = intval($tmp);
    $totalroleids = count($roleids);
    $roleidsql = '';
    if($totalroleids == 0) {
        $error = 'no matching roles in db';
        $sql = 'staffenroll_getroles()';
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

    if($userid == 0) {
        $userid = $USER->id;
    }
    $query = implode(' ', array(
        "SELECT c.id AS courseid, c.idnumber,",
        "c.shortname, r.name AS rolename,",
        "r.id AS roleid",
        "FROM mdl_role r, mdl_role_assignments ra,",
        " mdl_context x, mdl_course c",
        "WHERE",
        $roleidsql,
        "AND r.id=ra.roleid",
        "AND ra.userid =",
        $userid,
        "AND ra.contextid = x.id AND c.id = x.instanceid",
        "ORDER BY c.idnumber"
    ));
    $enrollments = array();
    $records = $DB->get_records_sql($query);
    foreach($records as $r) {
        if(! isset($enrollments[$r->courseid])) {
            $enrollments[$r->courseid] = array();
        }
        $enrollments[$r->courseid][] = array(
            'idnumber' => $r->idnumber,
            'shortname' => $r->shortname,
            'rolename' => $r->rolename,
            'roleid' => $r->roleid
        );
    }
    return $enrollments;
}



// BROWSECOURSES

// based on
// https://stackoverflow.com/questions/594112/matching-an-ip-to-a-cidr-mask-in-php-5
function staffenroll_validatenetworkhost() {
    $hostip = $_SERVER['REMOTE_ADDR'];
    $ip = ip2long($hostip);
    $allowednetworks = get_config('block_staffenroll', 'allowednetworks');
    $networks = explode("\n", $allowednetworks);
    foreach($networks as $n) {
        //list ($subnet, $bits) = explode('/', $range);
        list ($subnet, $bits) = explode('/', $n);
        if ($bits === null) {
            $bits = 32;
        }
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - $bits);
        $subnet &= $mask; # nb: in case the supplied subnet wasn't correctly aligned
        if(($ip & $mask) == $subnet) {
            return true;
        }
    }
    return false;
}

function staffenroll_getsubcategorylist($subcats = array()) {
    $items = array();
    foreach($subcats as $sc) {
        $url = new moodle_url(
            '/blocks/staffenroll/browse.php',
            array('parentid' => $sc['id'] )
        );
        $text = $sc['name'];
        if(isset($sc['description'])) {
            $desc = trim($sc['description']);
            if(preg_match('/\w+/', $desc)) {
                $text = implode(' ', array(
                    $desc,
                    '(' . $sc['name'] . ')'
                ));
            }
        }
        $link = html_writer::link($url, $text);
        $items[] = $link;
    }
    if(count($items) > 0) {
        return html_writer::alist($items);
    }
    return html_writer::div('no categories found');
}

function staffenroll_getsubcategories($pid) {
    global $DB;

    $coursescategories = cache::make('block_staffenroll', 'coursescategories');
    $cachekey = 'pcat' . $pid;
    $cachetimestamp = $cachekey . 'generated';
    $ok = staffenroll_unexpiredcache($cachetimestamp);
    if($ok) {
        $subcats = $coursescategories->get($cachekey);
        if($subcats) {
            return $subcats;
        }
    }

    $results = $DB->get_records(
        'course_categories',
        array('parent' => $pid),
        'sortorder',
        'id, name, description'
    );

    $subcats = array();
    $rawpc = get_config(
        'block_staffenroll',
        'prohibitedcategories'
    );
    $prohibitedcategories = explode(',', $rawpc);
    foreach($results as $r) {
        $skip = false;
        $key = 'cat' . $r->id;
        foreach($prohibitedcategories as $pc) {
            if($key == $pc) {
                $skip = true;
                break;
            }
        }

        if($skip) { continue; }

            $subcats[] = array(
                'id'    => $r->id,
                'name'  => $r->name,
                'description' => $r->description
            );
    }
    $now = time();
    $coursescategories->set($cachetimestamp, $now);
    $coursescategories->set($cachekey, $subcats);
    return $subcats;
}

/*
function support_staff_enroll_get_subcats_table($subcats) {
    $table = new html_table();

    $table_head_cell = new html_table_cell(
        get_string('subcats_table_head', 'local_support_staff_enroll')
    );

    $table_head_cell->colspan = 2;

    $table->head = array($table_head_cell);

    $table->data = array();
    foreach ($subcats as $subcat) {
        $subcat_url = new moodle_url(
            '/local/support_staff_enroll/courses_view.php',
            array( 'parent' => $subcat['id'] )
        );

        $subcat_link = html_writer::link( $subcat_url, $subcat['name'] );

        $table->data[] = array( $subcat_link, $subcat['descr'] );
    }

    return html_writer::table($table);
}
 */
function staffenroll_getsubcourselist($subcrs = array()) {
    $items = array();
    foreach($subcrs as $sc) {
        $url = new moodle_url(
            '/blocks/staffenroll/enroll.php',
            array('courseid' => $sc['id'] )
        );
        $link = html_writer::link($url, $sc['fullname']);
        $items[] = $link;
    }
    if(count($items) > 0) {
        return html_writer::alist($items);
    }
    return html_writer::div('no courses found');
}

function staffenroll_getsubcourseslist($subcrs = array()) {
    $items = array();
    foreach($subcrs as $sc) {
        $subitems = array();
        $studenturl = new moodle_url(
            '/blocks/staffenroll/enroll.php',
            array(
                'courseid' => $sc['id'],
                'type' => 'student'
            )
        );
        $studentlink = html_writer::link($studenturl, 'student');
        $subitems[] = $studentlink;
        $staffurl = new moodle_url(
            '/blocks/staffenroll/enroll.php',
            array(
                'courseid' => $sc['id'],
                'type' => 'staff'
            )
        );
        $stafflink = html_writer::link($staffurl, 'staff');
        $subitems[] = $stafflink;
        $sublist = html_writer::alist($subitems);
        $text = $sc['shortname'];
        $fullname = trim($sc['fullname']);
        if(count($fullname) > 0) {
            $text = $fullname;
        }
        $summary = trim($sc['summary']);
        $i = preg_match('/\w+/', $summary);
        if($i == 1) {
            $text .= ' (' . $summary . ')';
        }
        $item = implode(' ', array(
            $text,
            $sublist
        ));
        $items[] = $item;
    }
    if(count($items) > 0) {
        return html_writer::alist($items);
    }
    return html_writer::div('no courses found');
}

function staffenroll_getsubcourses($pid) {
    global $DB, $USER;

    $coursescategories = cache::make('block_staffenroll', 'coursescategories');
    $cachekey = 'pcrs' . $pid;
    $cachetimestamp = $cachekey . 'generated';
    $ok = staffenroll_unexpiredcache($cachetimestamp);
    $subcrs = array();
    if($ok) {
        return $coursescategories->get($cachekey);
    }
    else {
        $records = $DB->get_records(
            'course',
            array('category' => $pid),
            'sortorder',
            'id, idnumber, shortname, fullname, summary, category'
        );
        foreach($records as $r) {
            $subcrs[] = array(
                'id' => $r->id,
                'idnumber' => $r->idnumber,
                'shortname' => $r->shortname,
                'fullname' => $r->fullname,
                'summary' => $r->summary,
                'category' => $r->category
            );
        }
    }

    $enrollments = staffenroll_getuserenrollments($USER->id);
    $courses = array();
    foreach($subcrs as $c) {
        $ok = staffenroll_canenroll($c['id']);
        if(! $ok or $c['id'] == 1) {
            // homepage not course
            continue;
        }

        $roles = array();
        if(isset($enrollments[$c['id']])) {
            $roles = $enrollments[$c['id']];
        }
        $c['roles'] = $roles;
        $courses[] = $c;
    }
    $now = time();
    $coursescategories->set($cachetimestamp, $now);
    $coursescategories->set($cachekey, $courses);
    return $courses;
}


function staffenroll_getbreadcrumbs($categoryid = 0) {
    global $DB;

    $breadcrumbs = array();
    $linktext = get_string('breadcrumblinktext', 'block_staffenroll');
    $breadcrumbs[] = array(
        'name' => $linktext,
        'href' => 'browse.php',
    );

    $category = false;
    if($categoryid != 0) {
        $category = $DB->get_record(
            'course_categories',
            array('id' => $categoryid)
        );
    }

    if(! $category) {
        return $breadcrumbs;
    }

    $categoryids = explode("/", $category->path);
    foreach($categoryids as $id) {
        $results = $DB->get_record('course_categories', array('id' => $id));
        if(! $results) {
            // FIXME: throw exception
            error_log('missing category for id: ' . $id);
            continue;
        }
        $href = new moodle_url(
            '/blocks/staffenroll/browse.php',
            array('parentid' => $results->id)
        );

        $breadcrumbs[] = array(
            'name' => $results->name,
            'href' => $href,
        );
    }
    return $breadcrumbs;
}
