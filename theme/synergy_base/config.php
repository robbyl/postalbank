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
 * Moodle's synergy_base theme, an example of how to make a Bootstrap theme
 *
 * DO NOT MODIFY THIS THEME!
 * COPY IT FIRST, THEN RENAME THE COPY AND MODIFY IT INSTEAD.
 *
 * For full information about creating Moodle themes, see:
 * http://docs.moodle.org/dev/Themes_2.0
 *
 * @package   theme_synergy_base
 * @copyright 2013 Moodle, moodle.org
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$THEME->name = 'synergy_base';
$THEME->parents = array('bootstrapbase');
$THEME->sheets = array('totara_jquery_ui_dialog', 'totara', 'navigation', 'style', 'media' );
$THEME->rendererfactory = 'theme_overridden_renderer_factory';
$THEME->javascripts[] = 'respond';
$THEME->javascripts_footer[] = 'synergy_base';
$THEME->javascripts_footer[] = 'hidetinymce';

$THEME->hidefromselector = true;

$THEME->layouts = array(

// pages that need the full width of the page - no blocks shown at all
    // this is only used by totara pages
    'noblocks' => array(
        'file' => 'columns1.php',
        'regions' => array(),
        'options' => array('noblocks'=>true, 'langmenu'=>true),
    )

);