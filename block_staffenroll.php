<?php
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
        global $COURSE, $DB, $PAGE;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $contentText = array();
        $currentContext = $this->page->context;
        if($currentContext->contextlevel == CONTEXT_COURSE){
            $contentText[] = 'unenroll link';
        }
        else if($currentContext->contextlevel == CONTENT_USER){
            $contentText[] = 'enroll link';
        }
           
        $this->content->text = implode("<br/>", $contentText);
        $this->content->footer = '';
        return $this->content;
    }
}
