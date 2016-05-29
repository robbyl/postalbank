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
 * @author David Curry <david.curry@totaralms.com>
 * @package totara
 * @subpackage totara_core
 */

M.totara_assignindividual = M.totara_assignindividual || {

     Y: null,
     // optional php params and defaults defined here, args passed to init method
     // below will override these values
     config: {
        id:0,
        frameworkid:0,
     },

     init: function(Y, args){
         // Save a reference to the Y instance (all of its dependencies included).
         this.Y = Y;
         //
         // If defined, parse args into this module's config object.
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
            throw new Error('M.totara_assignindividual.init()-> jQuery dependency required for this module to function.');
        }

        // Goals dialog.
        // Note: assigntype=1 is referring to GOAL_ASSIGNMENT_INDIVIUDAL.
        (function() {
            var assignto = M.totara_assignindividual.config.id;
            var fid = M.totara_assignindividual.config.frameworkid;
            var url = M.cfg.wwwroot+ '/totara/hierarchy/prefix/goal/assign/';
            var saveurl = url + 'assign.php';
            var findurl = url + 'find.php';

            // Dialog & handler for hierarchy picker.
            var thandler = new totaraDialog_handler_assigngoaltreeview();
            var tbuttons = {};
            tbuttons[M.util.get_string('save','totara_core')] = function() { thandler._save(); }
            tbuttons[M.util.get_string('cancel','moodle')] = function() { thandler._cancel(); }
            var tdialog = new totaraDialog(
                'assigngoaltreeviewdialog',
                'nobutton',
                {
                    buttons: tbuttons,
                    title: '<h2>' + M.util.get_string('assigngoals', 'totara_hierarchy') + '</h2>'
                },
                findurl + '?assigntype=1&assignto=' + assignto + '&frameworkid=' + fid,
                thandler
            );
            tdialog.assigngoal_base_url = url;
            totaraDialogs['assigngoaltreeview'] = tdialog;

            // when the AJAX call to retrieve new HTML for the assigned competency
            // table completes we can set up the component action on the new select
            // element because it has a new, unique ID different from when rendered
            // on page load.
            var formid = 'switchframework';
            $(totaraDialogs.assigngoaltreeview.dialog).bind('ajaxComplete', function(event) {
                // to be double sure our newly appended DOM elements are ready to
                // have a listener bound by a component action generated ID, respond
                // when the attached parent node's 'contentready' event is fired.
                Y.on('contentready', function(e){
                    var selectid = Y.one('#'+formid+' select').get('id');
                    // call the original component action again so it handles the
                    // auto submission of a selected option based on the new select
                    M.core.init_formautosubmit(Y, formid, selectid);
                }, '#'+formid, Y);
            });


            // Bind open event to group_selector menu(s)
            // Also set their default value
            $(document).on('click', '#show-assignedgoals-dialog', function(event) {
                // Stop any default event occuring
                event.preventDefault();

                // Open default url
                var select = $(this);
                var id = M.totara_assignindividual.config.id;
                var sesskey = M.totara_assignindividual.config.sesskey;

                var dialog = totaraDialogs['assigngoaltreeview'];
                var url = dialog.assigngoal_base_url;
                var handler = dialog.handler;

                handler.responsetype = 'newgoal';
                handler.responsegoeshere = $('#company_goals_table');

                dialog.default_url = findurl + '?assigntype=1&assignto=' + id;
                dialog.saveurl = saveurl + '?assigntype=1&assignto=' + id + '&sesskey=' + sesskey + '&add=1' ;
                dialog.showhide_url = M.totara_assignindividual.config.showhide_url;
                dialog.showhide_text = M.totara_assignindividual.config.showhide_text;
                dialog.showhide_id = M.totara_assignindividual.config.id;
                dialog.open();

            });

        })();

    }
};

// A function to handle the responses generated by handlers
var assigngoal_handler_responsefunc = function(response) {
    if (response.substr(0,4) == 'DONE') {
        // Get all root elements in response
        var els = $(response.substr(4));

        // Update the assignments table.
        this.responsegoeshere.replaceWith(els);
        els.effect('pulsate', { times: 3 }, 2000);

        $('#assigngoal_action_box').show();

        // Close dialog
        this._dialog.hide();
    } else {
        this._dialog.render(response);
    }
}
totaraDialog_handler_assigngoaltreeview = function() {};
totaraDialog_handler_assigngoaltreeview.prototype = new totaraDialog_handler_treeview_multiselect();

/**
 * Serialize dropped items and send to url,
 * update table with result
 *
 * @param string URL to send dropped items to
 * @return void
 */
totaraDialog_handler_assigngoaltreeview.prototype._save = function() {
    // Serialize data
    var elements = $('.selected > div > span', this._container);
    var selected = this._get_ids(elements);
    var extrafields = $('.assigngrouptreeviewsubmitfield');

    // If they're trying to create a new rule but haven't selected anything, just exit.
    // (If they are updating an existing rule, we'll want to delete the selected ones.)
    if (!selected.length) {
        if (this.responsetype == 'new') {
            this._cancel();
            return;
        } else if (this.responsetype == 'update') {
            // Trigger the "delete" link, closing this dialog if it's successful.
            $('a.group-delete', this.responsegoeshere).trigger('click', {object: this, method: '_cancel'});
            return;
        }
    }

    // Check for any validation functions.
    var success = true;
    extrafields.each(
        function(intIndex) {
            if (typeof(this.assigngoal_validation_func) == 'function') {
                success = success && this.assigngoal_validation_func(this);
            }
        }
    );
    if (!success) {
        return;
    }
    $('#assigngoal_action_box').show();

    var selected_str = selected.join(',');

    // Add to url.
    var url = this._dialog.saveurl + '&selected=' + selected_str;
    extrafields.each(
        function(intIndex) {
            if ($(this).val() != null) {
                url = url + '&' + $(this).attr('name') + '=' + $(this).val();
            }
        }
    );

    // Display details button.
    if (selected.length > 0) {
        this._display_details();
    }

    // Send to server.
    this._dialog._request(url, {object: this, method: '_update'});
}

totaraDialog_handler_assigngoaltreeview.prototype._display_details = function () {
    var details = $(".companygoals.detailswrapper");
    var display_button = '';

    display_button += '<div class="singlebutton">';
    display_button += '<form action="'+this._dialog.showhide_url+'" method="get">';
    display_button += '<div>';
    display_button += '<input type="submit" id="showhide-goal-details" value="'+this._dialog.showhide_text+'"></input>';
    display_button += '<input type="hidden" name="id" value="'+this._dialog.showhide_id+'"></input>';
    display_button += '<input type="hidden" name="display" value="1"></input>';
    display_button += '</div>';
    display_button += '</form>';
    display_button += '</div>';

    details.replaceWith(display_button);
}

// TODO: T-11233 need to figure out a better way to share this common code between this and the formpicker.
totaraDialog_handler_assigngoaltreeview.prototype._update = assigngoal_handler_responsefunc;
