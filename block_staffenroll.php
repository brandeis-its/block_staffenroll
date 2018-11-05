<?php
class block_staffenroll extends block_base {

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


    // allows for global config variables
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


    // populates block
    function get_content() {
        global $COURSE, $DB, $PAGE;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $context = context_course::instance($COURSE->id);
        $this->content = new stdClass;

        if (has_capability('block/staffenroll:managepages', $context)) {
            $url = new moodle_url('/blocks/staffenroll/view.php',
                array('blockid' => $this->instance->id, 'courseid' => $COURSE->id)
            );
            $this->content->footer = html_writer::link(
                $url, get_string('addpage', 'block_staffenroll')
            );
        } else {
            $this->content->footer = '';
        }

        // Check to see if we are in editing mode and that we can manage pages.
        $canmanage = has_capability('block/staffenroll:managepages', $context) &&
            $PAGE->user_is_editing($this->instance->id);
        $canview = has_capability('block/staffenroll:viewpages', $context);


        // NOTE: this is simple tutorial code rewritten
        $textContent = array();
        if (!empty($this->config->text)) {
            $textContent[] = $this->config->text;
        }

        $textContent[] = html_writer::start_tag('ul',
            array('class' => 'staffenroll')
        );
        $textContent[] = html_writer::start_tag('li');
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
        $textContent[] = html_writer::tag('a', $arbitrary,
            array('href' => 'https://random.org', 'target' => '_blank')
        );
        $textContent[] = html_writer::end_tag('li');

        $textContent[] = html_writer::start_tag('li');
        $textContent[] = html_writer::tag('a', 'lorem ipsum',
            array('href' => 'https://lipsum.com', 'target' => '_blank')
        );
        $textContent[] = html_writer::end_tag('li');
        $textContent[] = html_writer::end_tag('ul');




        // Check to see if we are in editing mode
        $canmanage = $PAGE->user_is_editing($this->instance->id);

        if (
            $staffenrollpages = $DB->get_records(
                'block_staffenroll',
                array('blockid' => $this->instance->id)
            )
        ) {
            $textContent[] = html_writer::start_tag('ul');
            foreach ($staffenrollpages as $sep) {
                if ($canmanage) {
                    // edit
                    $editparam = array(
                        'blockid' => $this->instance->id,
                        'courseid' => $COURSE->id,
                        'id' => $sep->id
                    );
                    $editurl = new moodle_url('/blocks/staffenroll/view.php', $editparam);
                    $editpicurl = new moodle_url('/pix/t/edit.gif');
                    $edit = html_writer::link(
                        $editurl,
                        html_writer::tag(
                            'img', '',
                            array(
                                'src' => $editpicurl,
                                'alt' => get_string('edit')
                            )
                        )
                    );

                    //delete
                    $deleteparam = array(
                        'id' => $simplehtmlpage->id,
                        'courseid' => $COURSE->id
                    );
                    $deleteurl = new moodle_url('/blocks/simplehtml/delete.php', $deleteparam);
                    $deletepicurl = new moodle_url('/pix/t/delete.gif');
                    $delete = html_writer::link(
                        $deleteurl, html_writer::tag(
                            'img', '',
                            array(
                                'src' => $deletepicurl,
                                'alt' => get_string('delete')
                            )
                        )
                    );
                } else {
                    $edit = '';
                    $delete = '';
                }
                $pageurl = new moodle_url(
                    '/blocks/staffenroll/view.php',
                    array(
                        'blockid' => $this->instance->id,
                        'courseid' => $COURSE->id,
                        'id' => $sep->id,
                        'viewpage' => true
                    )
                );
                $textContent[] = html_writer::start_tag('li');

                if ($canview) {
                    $textContent[] = html_writer::link($pageurl, $sep->pagetitle);
                } else {
                    $textContent[] = html_writer::tag('div', $sep->pagetitle);
                }

                $textContent[] = $edit;
                $textContent[] = $delete;
                $textContent[] = html_writer::end_tag('li');
            }

            $textContent[] = html_writer::end_tag('ul');
        }

        $this->content->text = implode("\n", $textContent);
        return $this->content;
    }


    // remove from db
    public function instance_delete() {
        global $DB;
        $DB->delete_records('block_staffenroll',
            array('blockid' => $this->instance->id));
    }


}

/*
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

