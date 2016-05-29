<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2010 onwards Totara Learning Solutions LTD
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Tests importing courses from a generated csv file
 *
 * To test, run this from the command line from the $CFG->dirroot
 * vendor/bin/phpunit importcourse_testcase totara/completionimport/tests/importcourse_test.php
 *
 * @package    totara_completionimport
 * @subpackage phpunit
 * @author     Russell England <russell.england@catalyst-eu.net>
 * @copyright  Catalyst IT Ltd 2013 <http://catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/totara/completionimport/lib.php');
require_once($CFG->libdir . '/csvlib.class.php');
require_once($CFG->libdir . '/completionlib.php');

define('COURSE_HISTORY_IMPORT_USERS', 11);
define('COURSE_HISTORY_IMPORT_COURSES', 11);
define('COURSE_HISTORY_IMPORT_CSV_ROWS', 100); // Must be less than user * course counts.

class importcourse_testcase extends advanced_testcase {

    public function test_import() {
        global $DB, $CFG;

        $this->resetAfterTest(true);

        $importname = 'course';
        $pluginname = 'totara_completionimport_' . $importname;
        $csvdateformat = get_default_config($pluginname, 'csvdateformat', TCI_CSV_DATE_FORMAT);
        $csvdelimiter = get_default_config($pluginname, 'csvdelimiter', TCI_CSV_DELIMITER);
        $csvseparator = get_default_config($pluginname, 'csvseparator', TCI_CSV_SEPARATOR);

        $this->setAdminUser();

        // Create courses with completion enabled.
        $generatorstart = time();
        $this->assertEquals(1, $DB->count_records('course')); // Site course.
        $coursedefaults = array('enablecompletion' => COMPLETION_ENABLED);
        for ($i = 1; $i <= COURSE_HISTORY_IMPORT_USERS; $i++) {
            $this->getDataGenerator()->create_course($coursedefaults);
        }
        // Site course + generated courses.
        $this->assertEquals(COURSE_HISTORY_IMPORT_USERS+1, $DB->count_records('course'),
                'Record count mismatch for courses');

        // Create users
        $this->assertEquals(2, $DB->count_records('user')); // Guest + Admin.
        for ($i = 1; $i <= COURSE_HISTORY_IMPORT_COURSES; $i++) {
            $this->getDataGenerator()->create_user();
        }
        // Guest + Admin + generated users.
        $this->assertEquals(COURSE_HISTORY_IMPORT_COURSES+2, $DB->count_records('user'),
                'Record count mismatch for users');

        // Manual enrol should be set.
        $this->assertEquals(COURSE_HISTORY_IMPORT_COURSES, $DB->count_records('enrol', array('enrol'=>'manual')),
                'Manual enrol is not set for all courses');

        // Generate import data - product of user and course tables - exluding site course and admin/guest user.
        $fields = array('username', 'courseshortname', 'courseidnumber', 'completiondate', 'grade');
        $csvexport = new csv_export_writer($csvdelimiter, $csvseparator);
        $csvexport->add_data($fields);

        $uniqueid = $DB->sql_concat('u.username', 'c.shortname');
        $sql = "SELECT  {$uniqueid} AS uniqueid,
                        u.username,
                        c.shortname AS courseshortname,
                        c.idnumber AS courseidnumber
                FROM    {user} u,
                        {course} c
                WHERE   u.id > 2
                AND     c.id > 1";
        $imports = $DB->get_recordset_sql($sql, null, 0, COURSE_HISTORY_IMPORT_CSV_ROWS);
        if ($imports->valid()) {
            $count = 0;
            foreach ($imports as $import) {
                $data = array();
                $data['username'] = $import->username;
                $data['courseshortname'] = $import->courseshortname;
                $data['courseidnumber'] = $import->courseidnumber;
                $data['completiondate'] = date($csvdateformat, strtotime(date('Y-m-d') . ' -' . rand(1, 365) . ' days'));
                $data['grade'] = rand(1, 100);
                $csvexport->add_data($data);
                $count++;
            }
            // Create records to save them as evidence.
            $countevidence = 2;
            for ($i = 1; $i <= $countevidence; $i++) {
                $lastrecord = $data;
                $data['username'] = $lastrecord['username'];
                $data['courseshortname'] = ($i == 1) ? 'mycourseshortname' : $lastrecord['courseshortname'];
                $data['courseidnumber'] = 'XXXY';
                $data['completiondate'] = $lastrecord['completiondate'];
                $data['grade'] = rand(1, 100);
                $csvexport->add_data($data);
                $count++;
            }
        }
        $imports->close();
        $this->assertEquals(COURSE_HISTORY_IMPORT_CSV_ROWS + $countevidence, $count, 'Record count mismatch when creating CSV file');

        // Save the csv file generated by csvexport.
        $temppath = $CFG->dataroot . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR;
        if (!file_exists($temppath)) {
            mkdir($temppath, $CFG->directorypermissions, true);
        }
        $filename = tempnam($temppath, 'imp');
        copy($csvexport->path, $filename);

        // Time info for load testing - 4.4 minutes for 10,000 csv rows on postgresql.
        $generatorstop = time();

        $importstart = time();
        import_completions($filename, $importname, $importstart, true);
        $importstop = time();

        $importtablename = get_tablename($importname);
        $this->assertEquals(COURSE_HISTORY_IMPORT_CSV_ROWS + $countevidence, $DB->count_records($importtablename),
                'Record count mismatch in the import table ' . $importtablename);
        $this->assertEquals($countevidence, $DB->count_records('dp_plan_evidence'),
                'There should be two evidence records');
        $this->assertEquals(COURSE_HISTORY_IMPORT_CSV_ROWS, $DB->count_records('course_completions'),
                'Record count mismatch in the course_completions table');
        $this->assertEquals(COURSE_HISTORY_IMPORT_CSV_ROWS, $DB->count_records('user_enrolments'),
                'Record count mismatch in the user_enrolments table');
    }
}
