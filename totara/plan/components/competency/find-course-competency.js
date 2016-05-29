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
 * @author Maria Torres <maria.torres@totaralms.com>
 * @package totara
 * @subpackage totara_plan
 */
M.totara_find_course_competency = M.totara_find_course_competency || {

    Y: null,
    // optional php params and defaults defined here, args passed to init method
    // below will override these values
    config: {},
    // public handler reference for the dialog
    totaraDialog_handler_preRequisite: null,

    /**
     * module initialisation method called by php js_init_call()
     *
     * @param object    YUI instance
     * @param string    args supplied in JSON format
     */
    init: function(Y, args){
        // save a reference to the Y instance (all of its dependencies included)
        this.Y = Y;

        // if defined, parse args into this module's config object
        if (args) {
            var jargs = Y.JSON.parse(args);
            for (var a in jargs) {
                if (Y.Object.owns(jargs, a)) {
                    this.config[a] = jargs[a];
                }
            }
        }

        // check jQuery dependency is available
        if (typeof $ === 'undefined') {
            throw new Error('M.totara_positionuser.init()-> jQuery dependency required for this module to function.');
        }

        // Create handler for the dialog
        this.totaraDialog_handler_preRequisite = function() {
            // Base url
            var baseurl = '';
        }

        this.totaraDialog_handler_preRequisite.prototype = new totaraDialog_handler_treeview_multiselect();

        /**
         * Add a row to a table on the calling page
         * Also hides the dialog and any no item notice
         *
         * @param string    HTML response
         * @return void
         */
        this.totaraDialog_handler_preRequisite.prototype._update = function(response) {

            // Hide dialog
            this._dialog.hide();

            // Remove no item warning (if exists)
            $('.noitems-'+this._title).remove();

            // Grab table
            var table = $('table.dp-plan-component-items');

            // If table found
            if (table.size()) {
                table.replaceWith(response);
            }
            else {
                // Add new table
                $('div#dp-competency-courses-container').prepend(response);
            }

            // Grab remove button
            $('input#remove-selected-course').show();
        };

        var url = M.cfg.wwwroot + '/totara/plan/components/competency/';
        var saveurl = url + 'update-course-competency.php?planid='+this.config.plan_id+'&competencyid='+this.config.competency_id+'&update=';

        var handler = new this.totaraDialog_handler_preRequisite();
        handler.baseurl = url;

        var buttonsObj = {};
        buttonsObj[M.util.get_string('cancel','moodle')] = function() { handler._cancel() }
        buttonsObj[M.util.get_string('save','totara_core')] = function() { handler._save(saveurl) }

        totaraDialogs['evidence'] = new totaraDialog(
            'assigncoursesbycompetency',
            'show-course-dialog-competency',
            {
                buttons: buttonsObj,
                title: '<h2>' + M.util.get_string('addlinkedcoursescompetency', 'totara_plan') + '</h2>'
            },
            url+'find-course.php?planid='+this.config.plan_id+'&competencyid='+this.config.competency_id+'&competency_search=1',
            handler
        );
    }
};
