<?php
require_once($CFG->dirroot . '/blocks/staffenroll/lib.php');

class block_staffenroll extends block_base {

    public function init() {
        $this->title = get_string('staffenroll', 'block_staffenroll');
    }


    // allows for global config variables
    function has_config() {
        return true;
    }


    // specifies which pages are legal to display block
    // in this case, front page and course view pages
    public function applicable_formats() {
        return array(
            'my' => true,
            'course-view' => true,
        );
    }


    // validates user, populates link and previous enrollments
    function populateEnrollLink($ct = array()) {
        $rolename = staffenroll_canenroll();
        if($rolename == 'none') {
            $ct[] = 'no permission to enroll';
            return;
        }

        $url = new moodle_url(
            '/blocks/staffenroll/browse.php'
        );
        $link_text = get_string(
            'allcourseslink',
            'block_staffenroll'
        );
        $ct[] = html_writer::link($url, $link_text);

        // get existing support staff course enrollments
        $enrollments = staffenroll_getuserenrollments();

        // add links to courses the user is currently enrolled as support staff
        foreach($enrollments as $crsid => $elist) {
            $url = new moodle_url(
                '/course/view.php',
                array('id' => $crsid)
            );
            foreach($elist as $e) {
                $course_label = $e['shortname'] or $crsid;
                $link_text = implode(' ', array(
                    $course_label,
                    '(' . $e['rolename'] . ')'
                ));
                $ct[] = html_writer::link($url, $link_text);
            }
        }
        return $ct;
    }


    function get_content() {
        global $COURSE, $USER;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $contentText = array();
        $currentContext = $this->page->context;
        if($currentContext->contextlevel == CONTEXT_COURSE){
            $contentText[] = 'unenroll link';
        }
        else if($currentContext->contextlevel == CONTEXT_USER){
            //$contentText[] = 'enroll link';
            $this->populateEnrollLink($contentText);
        }
        else {
            $contentText[] = 'cannot place block in this context.';
        }

        $this->content->text = implode("\n", $contentText);
        $this->content->footer = '';
        return $this->content;
    }
}

