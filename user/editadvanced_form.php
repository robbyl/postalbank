<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Form for editing a users profile
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core_user
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    //  It must be included from a Moodle page.
}

require_once($CFG->dirroot.'/lib/formslib.php');

/**
 * Class user_editadvanced_form.
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_editadvanced_form extends moodleform {

    /**
     * Define the form.
     */
    public function definition() {
        global $USER, $CFG, $COURSE;

        $mform = $this->_form;
        $editoroptions = null;
        $filemanageroptions = null;
        $userid = $USER->id;

        if (is_array($this->_customdata)) {
            if (array_key_exists('editoroptions', $this->_customdata)) {
                $editoroptions = $this->_customdata['editoroptions'];
            }
            if (array_key_exists('filemanageroptions', $this->_customdata)) {
                $filemanageroptions = $this->_customdata['filemanageroptions'];
            }
            if (array_key_exists('userid', $this->_customdata)) {
                $userid = $this->_customdata['userid'];
            }
        }

        // Accessibility: "Required" is bad legend text.
        $strgeneral  = get_string('general');
        $strrequired = get_string('required');

        // Add some extra hidden fields.
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'course', $COURSE->id);
        $mform->setType('course', PARAM_INT);

        // Print the required moodle fields first.
        $mform->addElement('header', 'moodle', $strgeneral);

        $mform->addElement('text', 'username', get_string('username'), 'size="20"');
        $mform->addRule('username', $strrequired, 'required', null, 'client');
        $mform->setType('username', PARAM_RAW);

        $auths = core_component::get_plugin_list('auth');
        $enabled = get_string('pluginenabled', 'core_plugin');
        $disabled = get_string('plugindisabled', 'core_plugin');
        $authoptions = array($enabled => array(), $disabled => array());
        $cannotchangepass = array();
        foreach ($auths as $auth => $unused) {
            $authinst = get_auth_plugin($auth);
            $passwordurl = $authinst->change_password_url();
            if (!($authinst->can_change_password() && empty($passwordurl))) {
                if (!$userid and $authinst->is_internal()) {
                    // This is unlikely but we can not create account without password
                    // when plugin uses passwords, we need to set it initially at least.
                } else {
                    $cannotchangepass[] = $auth;
                }
            }
            if (is_enabled_auth($auth)) {
                $authoptions[$enabled][$auth] = get_string('pluginname', "auth_{$auth}");
            } else {
                $authoptions[$disabled][$auth] = get_string('pluginname', "auth_{$auth}");
            }
        }
        $mform->addElement('selectgroups', 'auth', get_string('chooseauthmethod', 'auth'), $authoptions);
        $mform->addHelpButton('auth', 'chooseauthmethod', 'auth');

        if (get_config('totara_sync', 'element_user_enabled')) {
            $mform->addElement('advcheckbox', 'totarasync', get_string('totarasync', 'tool_totara_sync').'?');
            $mform->addHelpButton('totarasync', 'totarasync', 'tool_totara_sync');
        }

        $mform->addElement('advcheckbox', 'suspended', get_string('suspended', 'auth'));
        $mform->addHelpButton('suspended', 'suspended', 'auth');

        $mform->addElement('checkbox', 'createpassword', get_string('createpassword', 'auth'));
        $mform->disabledIf('createpassword', 'auth', 'in', $cannotchangepass);

        if (!empty($CFG->passwordpolicy)) {
            $mform->addElement('static', 'passwordpolicyinfo', '', print_password_policy());
        }
        $mform->addElement('passwordunmask', 'newpassword', get_string('newpassword'), 'size="20"');
        $mform->addHelpButton('newpassword', 'newpassword');
        $mform->setType('newpassword', PARAM_RAW);
        $mform->disabledIf('newpassword', 'createpassword', 'checked');

        $mform->disabledIf('newpassword', 'auth', 'in', $cannotchangepass);

        $mform->addElement('advcheckbox', 'preference_auth_forcepasswordchange', get_string('forcepasswordchange'));
        $mform->addHelpButton('preference_auth_forcepasswordchange', 'forcepasswordchange');
        $mform->disabledIf('preference_auth_forcepasswordchange', 'createpassword', 'checked');

        // Shared fields.
        useredit_shared_definition($mform, $editoroptions, $filemanageroptions);

        // Next the customisable profile fields.
        profile_definition($mform, $userid);

        if ($userid == -1) {
            $btnstring = get_string('createuser');
        } else {
            $btnstring = get_string('updatemyprofile');
        }

        $this->add_action_buttons(false, $btnstring);
    }

    /**
     * Extend the form definition after data has been parsed.
     */
    public function definition_after_data() {
        global $USER, $CFG, $DB, $OUTPUT;

        $mform = $this->_form;
        if ($userid = $mform->getElementValue('id')) {
            $user = $DB->get_record('user', array('id' => $userid));
        } else {
            $user = false;
        }

        // If language does not exist, use site default lang.
        if ($langsel = $mform->getElementValue('lang')) {
            $lang = reset($langsel);
            // Check lang exists.
            if (!get_string_manager()->translation_exists($lang, false)) {
                $langel =& $mform->getElement('lang');
                $langel->setValue($CFG->lang);
            }
        }

        // User can not change own auth method.
        if ($userid == $USER->id) {
            $mform->hardFreeze('auth');
            $mform->hardFreeze('preference_auth_forcepasswordchange');
        }

        // Admin must choose some password and supply correct email.
        if (!empty($USER->newadminuser)) {
            $mform->addRule('newpassword', get_string('required'), 'required', null, 'client');
            if ($mform->elementExists('suspended')) {
                $mform->removeElement('suspended');
            }
        }

        // Require password for new users.
        if ($userid > 0) {
            if ($mform->elementExists('createpassword')) {
                $mform->removeElement('createpassword');
            }
        }

        if ($user and is_mnet_remote_user($user)) {
            // Only local accounts can be suspended.
            if ($mform->elementExists('suspended')) {
                $mform->removeElement('suspended');
            }
        }
        if ($user and ($user->id == $USER->id or is_siteadmin($user))) {
            // Prevent self and admin mess ups.
            if ($mform->elementExists('suspended')) {
                $mform->hardFreeze('suspended');
            }
        }

        // Print picture.
        if (empty($USER->newadminuser)) {
            if ($user) {
                $context = context_user::instance($user->id, MUST_EXIST);
                $fs = get_file_storage();
                $hasuploadedpicture = ($fs->file_exists($context->id, 'user', 'icon', 0, '/', 'f2.png') || $fs->file_exists($context->id, 'user', 'icon', 0, '/', 'f2.jpg'));
                if (!empty($user->picture) && $hasuploadedpicture) {
                    $imagevalue = $OUTPUT->user_picture($user, array('courseid' => SITEID, 'size' => 64));
                } else {
                    $imagevalue = get_string('none');
                }
            } else {
                $imagevalue = get_string('none');
            }
            $imageelement = $mform->getElement('currentpicture');
            $imageelement->setValue($imagevalue);

            if ($user && $mform->elementExists('deletepicture') && !$hasuploadedpicture) {
                $mform->removeElement('deletepicture');
            }
        }

        // Next the customisable profile fields.
        profile_definition_after_data($mform, $userid);
    }

    /**
     * Validate the form data.
     * @param array $usernew
     * @param array $files
     * @return array|bool
     */
    public function validation($usernew, $files) {
        global $CFG, $DB;

        $usernew = (object)$usernew;
        $usernew->username = trim($usernew->username);

        $user = $DB->get_record('user', array('id' => $usernew->id));
        $err = array();

        if (!$user and !empty($usernew->createpassword)) {
            if ($usernew->suspended) {
                // Show some error because we can not mail suspended users.
                $err['suspended'] = get_string('error');
            }
        } else {
            if (!empty($usernew->newpassword)) {
                $errmsg = ''; // Prevent eclipse warning.
                if (!check_password_policy($usernew->newpassword, $errmsg)) {
                    $err['newpassword'] = $errmsg;
                }
            } else if (!$user) {
                $auth = get_auth_plugin($usernew->auth);
                if ($auth->is_internal()) {
                    // Internal accounts require password!
                    $err['newpassword'] = get_string('required');
                }
            }
        }

        if (empty($usernew->username)) {
            // Might be only whitespace.
            $err['username'] = get_string('required');
        } else if (!$user or $user->username !== $usernew->username) {
            // Check new username does not exist.
            if ($DB->record_exists('user', array('username' => $usernew->username, 'mnethostid' => $CFG->mnet_localhost_id))) {
                $err['username'] = get_string('usernameexists');
            }
            // Check allowed characters.
            if ($usernew->username !== core_text::strtolower($usernew->username)) {
                $err['username'] = get_string('usernamelowercase');
            } else {
                if ($usernew->username !== clean_param($usernew->username, PARAM_USERNAME)) {
                    $err['username'] = get_string('invalidusername');
                }
            }
        }

        if (!$user or $user->email !== $usernew->email) {
            if (!validate_email($usernew->email)) {
                $err['email'] = get_string('invalidemail');
            } else if ($DB->record_exists('user', array('email' => $usernew->email, 'mnethostid' => $CFG->mnet_localhost_id))) {
                $err['email'] = get_string('emailexists');
            }
        }

        // Check idnumber uniqueness.
        if(!empty($usernew->idnumber) && totara_idnumber_exists('user', $usernew->idnumber, $usernew->id)) {
            $err['idnumber'] = get_string('idnumberexists', 'totara_core');
        }

        // If idnumber is empty, ensure sync is not turned on.
        if (empty($usernew->idnumber) && !empty($usernew->totarasync)) {
            $err['totarasync'] = get_string('error:emptyidnumberwithsync', 'totara_core');
        }

        // Next the customisable profile fields.
        $err += profile_validation($usernew, $files);

        if (count($err) == 0) {
            return true;
        } else {
            return $err;
        }
    }
}


