<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2010 onwards Totara Learning Solutions LTD
 *
 * This competency is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This competency is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author David Curry <david.curry@totaralms.com>
 * @package totara
 * @subpackage totara_hierarchy
 */


namespace totara_hierarchy\event;
defined('MOODLE_INTERNAL') || die();

class competency_deleted extends \core\event\base {

    /**
     * Initialise the event data.
     */
    protected function init() {
        $this->data['objecttable'] = 'comp';
        $this->data['crud'] = 'd';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventdeleted', 'hierarchy_competency');
    }

    /**
     * Returns non-localised description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The competency {$this->objectid} was deleted by user {$this->userid}";
    }

    /**
     * Returns relevant URL.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/totara/hierarchy/item/delete.php', array('prefix' => 'competency', 'id' => $this->objectid));
    }

    /**
     * Returns the name of the legacy event.
     *
     * @return string legacy event name
     */
    public static function get_legacy_eventname() {
        return 'competency_deleted';
    }

    /**
     * Returns the legacy event data.
     *
     * @return \stdClass the course that was created
     */
    protected function get_legacy_eventdata() {
        return $this->get_record_snapshot('comp', $this->objectid);
    }

    /**
     * Return legacy data for add_to_log().
     *
     * @return array
     */
    protected function get_legacy_logdata() {
        return array(SITEID, 'competency', 'deleted', 'delete.php?id=' . $this->objectid, 'ID: ' . $this->objectid);
    }

}
