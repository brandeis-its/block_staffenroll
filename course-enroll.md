# COURSE ENROLL

This document outlines the steps in the existing support staff
enroll code in order to replicate them with more modern code.
Further complicating everything is the fact that this
functionality is broken out over 3 different plugins of 2
different types (blocks, local). This document is only concerned
with the enrollment path. The unenrollment path is similar in many
ways, but easier, as many of the checks are not necessary.

## ENROLL

The following actions take place within the enroll block.

### gets support roles

returns mostly hard coded array of hashes
one for staff
one for students

    - name
    - cap (ability)
    - roleid (from settings config)

### checks permissions

uses support roles data to validate user has\_capability()

### add link

adds link to block pointing to "local" code

### get existing enrollments

run custom SQL to grab current enrollments from db

### add links 

to moodle core top level course page for each enrolled course

## LOCAL

For the next part of the enroll processing control shifts to the
local block

### gets env

dev, test, or prod
used to determine IP restrictions

**idiotic, should be deprecated**
**(replaced by new ip/CIDR mask setting)**

### checks permissions

uses new local `$capabilities` data structure to validate whether
use has\_capability

**argh! duplicate functionality but different implementation**

### validates permissions

uses new local `$valid_params` data structure to validate whether
passed in params are valid

**superfluous- regexp checking of named params**
**validate within code using param value**

### sets optional params

    - parentid
    - courseid

### processes unenroll action


### processes enroll action

### gets subcategories

### gets courses

### sets up page

### generate subcats/courses table
