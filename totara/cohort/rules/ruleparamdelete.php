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
 * @author Eugene Venter <eugene@catalyst.net.nz>
 * @package totara
 * @subpackage cohort/rules
 */
/**
 * This class is an ajax back-end for deleting a single rule param
 */
define('AJAX_SCRIPT', true);
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
require_once($CFG->dirroot . '/cohort/lib.php');

$ruleparamid = required_param('ruleparamid', PARAM_INT);

require_login();
require_sesskey();

$syscontext = context_system::instance();
require_capability('totara/cohort:managerules', $syscontext);

if (!$ruleparam = $DB->get_record('cohort_rule_params', array('id' => $ruleparamid), '*', MUST_EXIST)) {
    exit;
}

$return = json_encode(cohort_delete_param($ruleparam));

echo $return;

add_to_log(SITEID, 'cohort', 'delete rule param ' . $ruleparam->id, 'totara/cohort/rules.php');
