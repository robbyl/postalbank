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
 * Totara navigation edit page.
 *
 * @package    totara
 * @subpackage navigation
 * @author     Oleg Demeshev <oleg.demeshev@totaralms.com>
 */

namespace totara_appraisal\totara\menu;

use \totara_core\totara\menu\menu as menu;

class appraisal extends \totara_core\totara\menu\item {

    protected function get_default_title() {
        return get_string('appraisal', 'totara_appraisal');
    }

    protected function get_default_url() {
        global $CFG, $USER;

        require_once($CFG->dirroot . '/totara/appraisal/lib.php');

        $isappraisalenabled = totara_feature_visible('appraisals');
        $viewownappraisals = $isappraisalenabled && \appraisal::can_view_own_appraisals($USER->id);
        $viewappraisals = $isappraisalenabled && ($viewownappraisals || \appraisal::can_view_staff_appraisals($USER->id));

        $feedbackmenu = new \totara_feedback360\totara\menu\feedback360(array());
        $viewfeedback = $feedbackmenu->get_visibility();

        $goalmenu = new \totara_hierarchy\totara\menu\mygoals(array());
        $viewgoals = $goalmenu->get_visibility();

        if ($viewownappraisals) {
            return '/totara/appraisal/myappraisal.php?latest=1';
        } else if ($viewappraisals) {
            return '/totara/appraisal/index.php';
        } else if ($viewfeedback) {
            return '/totara/feedback360/index.php';
        } else if ($viewgoals) {
            return '/totara/hierarchy/prefix/goal/mygoals.php';
        }
    }

    public function get_default_sortorder() {
        return 30000;
    }

    public function get_default_visibility() {
        return menu::SHOW_WHEN_REQUIRED;
    }

    protected function check_visibility() {
        global $CFG, $USER;

        require_once($CFG->dirroot . '/totara/appraisal/lib.php');

        $isappraisalenabled = totara_feature_visible('appraisals');
        $viewownappraisals = $isappraisalenabled && \appraisal::can_view_own_appraisals($USER->id);
        $viewappraisals = $isappraisalenabled && ($viewownappraisals || \appraisal::can_view_staff_appraisals($USER->id));

        $feedbackmenu = new \totara_feedback360\totara\menu\feedback360(array());
        $viewfeedback = $feedbackmenu->get_visibility();

        $goalmenu = new \totara_hierarchy\totara\menu\mygoals(array());
        $viewgoals = $goalmenu->get_visibility();

        if ($viewappraisals || $viewfeedback || $viewgoals) {
            return menu::SHOW_ALWAYS;
        } else {
            return menu::HIDE_ALWAYS;
        }
    }
}
