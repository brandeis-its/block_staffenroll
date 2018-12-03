# COURSE ENROLL

This document outlines the steps in the existing support staff
enroll code in order to replicate them with more modern code.
Further complicating everything is the fact that this
functionality is broken out over 3 different plugins of 2
different types (blocks, local). This document is only concerned
with the enrollment path. The unenrollment path is similar in many
ways, but easier, as many of the checks are not necessary.

# FORMAT KEY
## Plugin type
### subroutine
#### (called from subroutine)
functionality

[commentary]

## Enroll

The following actions take place within the enroll block.

### get support roles

returns mostly hard coded array of hashes: one for staff, one for
students

* name
* cap (ability)
* roleid (from settings config)

### checks permissions

uses support roles data to validate user `has\_capability()`

### add link

adds link to block pointing to "local" code

### get existing enrollments

run custom SQL to grab current enrollments from db

### add links

to moodle core top level course page for each enrolled course


## Local

For the next part of the enroll processing control shifts to the
local block.

### gets env

dev, test, or prod: used to determine IP restrictions

[should be deprecated, replaced by new ip/CIDR mask setting]

### checks permissions

uses local `$capabilities` data structure to validate whether use
has\_capability

[duplicate functionality but uses different implementation (SQL)]

### validates permissions

uses new local `$valid_params` data structure to validate whether
passed in params are valid

[excessive validation (maybe just when variables are actually used)]

### sets optional params

* parentid
* courseid

### processes unenroll action

checks submitted params and if the right one is there, calls
enroll function with enroll/unenroll params

#### (enroll user)

validates params (again), finds appropriate roleid
(student/staff), gets moodle context (again), checks to make sure
that course in not in "special courses", completes manual
enroll/unenroll and triggers appropriate event

[outside of duplicate processing, prohibited categories need to
be configurable (settings)]

### processes enroll action

validates params, checks for duplicate enrollments, check for
prohibited categories (later duplicated)

checks submitted params and if the right one is there, calls
enroll function (same as unenroll (above)) with enroll/unenroll
params

### gets subcategories

run SQL to get categories 1 level down

### gets courses

validate params (really?)

#### (get enrollments)

run SQL query, return results in hash keyed by courseid

#### (get permissions)

grabs system context, creates another custom data structure with
list of possible permissions and their availability to current
user

cycle through courses, return data structure with course and
permissions data integrated

### sets up page

call moodle functions for generating page content, populate
breadcrumbs

### generate subcats/courses table

call moodle functions to create a page of subcategories, or
courses within a subcategory, either as an HTML table

[can we use built in moodle functions to do this work?]

