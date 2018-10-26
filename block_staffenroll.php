<?php
class block_staffenroll extends block_list {
    public function init() {
        $this->title = get_string('staffenroll', 'block_staffenroll');
    }


    // called after init() and before anything else
    public function specialization() {
        if (isset($this->config)) {
            if (! empty($this->config->title)) {
                $this->title = $this->config->title;
            }
        }
    }


    // FIXME: this may not be necessary
    // allows for global config variables
    // across all instances
    function has_config() {
        return true;
    }


    // This results in the block having all its normal HTML attributes, as
    // inherited from the base block class, plus our additional class name.
    // may be helpful in styling
    public function html_attributes() {
        $attributes = parent::html_attributes(); // Get default values
        $attributes['class'] .= ' block_'. $this->name(); // Append our class to class attribute
        return $attributes;
    }


    // specifies which pages are legal to display block
    // in this case, front page and course view pages
    // prolly not needed, but since i'm learning
    // i've included it
    public function applicable_formats() {
        return array(
            'site-index' => true,
            'course-view' => true,
        );
    }


    function get_content() {
        global $COURSE;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();

        $footerURL = new moodle_url(
            '/blocks/supportenroll/view.php',
            array('blockid' => $this->instance->id, 'courseid' => $COURSE->id)
        );
        $this->content->footer = html_writer::link(
            $footerURL,
            get_string('addpage', 'block_supportenroll')
        );

        $arbIdx = get_config('block_staffenroll', 'arbitrary');
        // FIXME: should be global
        // this needs to match what's written in settings.php
        $arbOptions = array(
            '',
            'dogs',
            'tumbleweeds',
            'satellites'
        );
        $arbitrary = $arbOptions[$arbIdx];

        $this->content->items[] = html_writer::tag('a', $arbitrary,
            array('href' => 'https://random.org', 'target' => '_blank'));
        $this->content->icons[] = html_writer::empty_tag('img',
            array('src' => '/blocks/staffenroll/pix/item.png', 'class' => 'icon'));
        $this->content->items[] = html_writer::tag('a', 'lorem ipsum',
            array('href' => 'https://lipsum.com', 'target' => '_blank'));
        $this->content->icons[] = html_writer::empty_tag('img',
            array('src' => '/blocks/staffenroll/pix/item.png', 'class' => 'icon'));

        return $this->content;
    }
}

/*
    // FIXME: is empty footer necessary?
    $this->content->footer = '';

    // get information about support staff roles, and which permission is
    // required to enroll in each role
    $roles = support_staff_enrollments_get_support_roles();

    // if the current user has permission, show the link to find
    // a course to enroll in as a support staff person
    if ( support_staff_enrollments_can_enroll($roles) ) {
        $this->content->items[]
            = support_staff_enrollments_get_all_courses_link($roles);
    }

    // get existing support staff enrollments
    $enrollments = support_staff_enrollments_get_enrollments($roles);

    // add links to courses the user is currently enrolled as support staff
    foreach ($enrollments as $enrollment) {
        $url = new moodle_url( '/course/view.php', array( 'id' => $enrollment->courseid ) );

        $course_label = $enrollment->course_shortname or $enrollment->courseid;
        $link_text = implode( ' ', array(
            $course_label,
            '(' . $enrollment->role_name . ')'
        ) );

        $this->content->items[] = html_writer::link( $url, $link_text );
    }
 */
