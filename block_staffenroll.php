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
    // prolly not needed, but since i'm learning
    // i've included it
    public function applicable_formats() {
        return array(
            'my' => true,
            'course-view' => true,
        );
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
            populateEnrollLink($contentText, $USER->id);
        }
        else {
            $contentText[] = 'cannot place block in this context.';
        }

        $this->content->text = implode("\n", $contentText);
        $this->content->footer = '';
        return $this->content;
    }
}

