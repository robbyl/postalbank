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
 * @author Valerii Kuznetsov <valerii.kuznetsov@totaralms.com>
 * @package totara
 * @subpackage coursecatalog
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->dirroot . '/totara/reportbuilder/lib.php');

$debug = optional_param('debug', 0, PARAM_INT);

$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('noblocks');
$PAGE->set_totara_menu_selected('course');
$PAGE->set_url('/totara/coursecatalog/courses.php');

if ($CFG->forcelogin) {
    require_login();
}

$renderer = $PAGE->get_renderer('totara_reportbuilder');
$strheading = get_string('searchcourses', 'totara_core');
$shortname = 'catalogcourses';

if (!$report = reportbuilder_get_embedded_report($shortname, null, false, 0)) {
    print_error('error:couldnotgenerateembeddedreport', 'totara_reportbuilder');
}

$logurl = $PAGE->url->out_as_local_url();

\totara_reportbuilder\event\report_viewed::create_from_report($report)->trigger();

$report->include_js();

$fullname = get_string('courses', 'moodle');
$pagetitle = format_string(get_string('findlearning', 'totara_core') . ': ' . $fullname);

$PAGE->navbar->add($fullname, new moodle_url("/totara/coursecatalog/courses.php"));
$PAGE->navbar->add(get_string('search'));
$PAGE->set_title($pagetitle);
$PAGE->set_button($report->edit_button());
$PAGE->set_heading(format_string($SITE->fullname));
echo $OUTPUT->header();

if ($debug) {
    $report->debug($debug);
}

$countfiltered = $report->get_filtered_count();
$countall = $report->get_full_count();

$heading = $strheading . ': ' .
    $renderer->print_result_count_string($countfiltered, $countall);
echo $OUTPUT->heading($heading);

echo $renderer->print_description($report->description, $report->_id);

$report->display_search();
$report->display_sidebar_search();

$report->display_table();

echo $OUTPUT->footer();
