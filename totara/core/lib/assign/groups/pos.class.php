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
 * @author Ciaran Irvine <ciaran.irvine@totaralms.com>
 * @package totara
 * @subpackage core
 */

/**
 * Base pos grouping assignment class.
 *
 * Most of the functionality is provided by the hierarchy class but
 * extension possible in this class.
 */
global $CFG;
require_once($CFG->dirroot.'/totara/core/lib/assign/hierarchy.class.php');

class totara_assign_core_grouptype_pos extends totara_assign_core_grouptype_hierarchy {

    protected $prefix = 'position';
    protected $grouptype = 'pos';

}
