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
 * @author Simon Coggins <simon.coggins@totaralms.com>
 * @author Aaron Barnes <aaron.barnes@totaralms.com>
 * @package totara
 * @subpackage totara_hierarchy
 */

require_once $CFG->dirroot.'/totara/hierarchy/prefix/competency/evidenceitem/type/record.php';

/**
 * Course completion competency evidence type
 */
class competency_evidence_type_coursecompletion extends competency_evidence_type {

    /**
     * Evidence item type
     * @var string
     */
    public $itemtype = COMPETENCY_EVIDENCE_TYPE_COURSE_COMPLETION;

    /**
     * Return evidence name and link
     *
     * @return  string
     */
    public function get_name() {
        global $CFG, $DB;

        // Get course name
        $course = $DB->get_field('course', 'fullname', array('id' => $this->iteminstance));
        $url = new moodle_url('/course/view.php', array('id' => $this->iteminstance));
        return html_writer::link($url, format_string($course));
    }

    /**
     * Return evidence item type and link
     *
     * @return  string
     */
    public function get_type() {
        global $CFG;

        $name = $this->get_type_name();
        $url = new moodle_url('/report/completion/index.php', array('course' => $this->iteminstance));
        return html_writer::link($url, format_string($name));
    }

    /**
     * Get human readable type name
     *
     * @return  string
     */
    public function get_activity_type() {
        return '';
    }

    /**
     * Find user's who have completed this evidence type
     * @access  public
     * @return  void
     */
    public function cron() {

        global $CFG, $DB;

        // Only select course completions that have changed
        // since an evidence item evidence was last changed
        //
        // A note on the sub-query, it returns:
        //   scaleid | proficient
        // where proficient is the ID of the lowest scale
        // value in that scale that has the proficient flag
        // set to 1
        //
        // The sub-sub-query is needed to allow us to return
        // the ID, when the actual item is determined by
        // the sortorder
        $sql = "
            SELECT DISTINCT
                ccr.id AS id,
                ccrit.id AS itemid,
                ccrit.competencyid,
                cc.userid,
                ccr.timecreated,
                cc.timecompleted,
                proficient.proficient,
                cs.defaultid
            FROM
                {comp_criteria} ccrit
            INNER JOIN
                {comp} co
             ON ccrit.competencyid = co.id
            INNER JOIN
                {course} c
             ON ccrit.iteminstance = c.id
            INNER JOIN
                {course_completions} cc
            ON cc.course = c.id
            INNER JOIN
                {comp_scale_assignments} csa
            ON co.frameworkid = csa.frameworkid
            INNER JOIN
                {comp_scale} cs
                ON csa.scaleid = cs.id
            INNER JOIN
            (
                SELECT csv.scaleid, csv.id AS proficient
                FROM {comp_scale_values} csv
                INNER JOIN
                (
                    SELECT scaleid, MAX(sortorder) AS maxsort
                    FROM {comp_scale_values}
                    WHERE proficient = 1
                    GROUP BY scaleid
                ) grouped
                ON csv.scaleid = grouped.scaleid AND csv.sortorder = grouped.maxsort
            ) proficient
            ON cs.id = proficient.scaleid
            LEFT JOIN
                {comp_criteria_record} ccr
             ON ccr.itemid = ccrit.id
            AND ccr.userid = cc.userid
            WHERE
                ccrit.itemtype = 'coursecompletion'
            AND cc.id IS NOT NULL
            AND proficient.proficient IS NOT NULL
            AND
            (
                (
                ccr.proficiencymeasured <> proficient.proficient
                AND
                    (
                        ccr.timemodified < cc.timecompleted
                     OR ccr.timemodified < cc.timeenrolled
                     OR ccr.timemodified < cc.timestarted
                    )
                )
             OR ccr.proficiencymeasured IS NULL
            )
        ";

        // Loop through evidence itmes, and mark as complete
        if ($rs = $DB->get_recordset_sql($sql)) {
            foreach ($rs as $record) {

                if (debugging()) {
                    mtrace('.', '');
                }

                $evidence = new comp_criteria_record((array)$record, false);

                if ($record->timecompleted) {
                    $evidence->proficiencymeasured = $record->proficient;
                }
                else if ($record->defaultid) {
                    $evidence->proficiencymeasured = $record->defaultid;
                }
                else {
                    continue;
                }

                $evidence->save();
            }

            if (debugging() && isset($evidence)) {
                mtrace('');
            }
            $rs->close();
        }
    }
}
