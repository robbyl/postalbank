YUI.add('moodle-totara_core-totaramenu', function (Y, NAME) {

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
 * @author Brian Barnes <brian.barnes@totaralms.com>
 * @package totara
 * @subpackage totara_core
 */

M.coremenu = M.coremenu || {};
NS = M.coremenu.setfocus = M.coremenu.setfocus || {};

/**
 * Set up the menu
 *
 * No arguments are required
 *
 * @method init
 */
NS.init = function() {
    if (typeof $ === 'undefined') {
        alert('jQuery is required for this to work');
    }

    $('#totaramenu, #custommenu').delegate('> ul > li > a', 'focus', function() {
        $(this).closest('ul').find('ul').removeAttr('style');
        $(this).siblings('ul').show();
    });

};

}, '@VERSION@', {"requires": ["jquery"]});
