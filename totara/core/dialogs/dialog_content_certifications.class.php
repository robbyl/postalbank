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
 * @author David Curry <david.curry@totaralms.com>
 * @package totara
 * @subpackage totara_core/dialogs
 */

/**
 * certification/category dialog generator
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/totara/core/dialogs/dialog_content.class.php');
require_once($CFG->dirroot . '/totara/program/lib.php');
require_once($CFG->dirroot . '/totara/certification/lib.php');
require_once($CFG->dirroot . '/totara/coursecatalog/lib.php');
require_once($CFG->libdir . '/datalib.php');

/**
 * Class for generating single select certification dialog markup
 *
 * @access  public
 */
class totara_dialog_content_certifications extends totara_dialog_content {

    /**
     * Type of search to perform (generally relates to dialog type)
     *
     * @access  public
     * @var     string
     */
    public $searchtype = 'program';

    /**
     * Current category (e.g., show children of this category)
     *
     * @access  public
     * @var     integer
     */
    public $categoryid;


    /**
     * Categories at this level (indexed by category ID)
     *
     * @access  public
     * @var     array
     */
    public $categories = array();


    /**
     * Courses at this level
     *
     * @access  public
     * @var     array
     */
    public $certifications = array();



    /**
     * Set current category
     *
     * @see     totara_dialog_hierarchy::categoryid
     *
     * @access  public
     * @param   $categoryid     int     Category id
     */
    public function __construct($categoryid) {

        $this->categoryid = $categoryid;

        // If category ID doesn't equal 0, must be only loading the tree
        if ($this->categoryid > 0) {
            $this->show_treeview_only = true;
        }

        // Load child categories
        $this->load_categories();

        // Load child courses
        $this->load_certifications();
    }


    /**
     * Load categories to display
     *
     * @access  public
     */
    public function load_categories() {
        global $DB;

        // If category 0, make fake object
        if (!$this->categoryid) {
            $parent = new stdClass();
            $parent->id = 0;
        }
        else {
            // Load category
            if (!$parent = $DB->get_record('course_categories', array('id' => $this->categoryid))) {
                print_error('error:categoryidincorrect', 'totara_core');
            }
        }

        // Load child categories
        $categories = coursecat::get($parent->id)->get_children();

        $category_ids = array();
        foreach ($categories as $cat) {
            $category_ids[] = $cat->id;
        }

        // Get item counts for categories
        $category_item_counts = (count($category_ids) > 0) ? totara_get_category_item_count($category_ids, 'certification') : array();

        // Fix array to be indexed by prefixed id's (so it doesn't conflict with course id's)
        foreach ($categories as $category) {
            $item_count = array_key_exists($category->id, $category_item_counts) ? $category_item_counts[$category->id] : 0;

            //Dont show category if there are no items in it
            if ($item_count > 0) {
                $c = new stdClass();
                $c->id = 'cat'.$category->id;
                $c->fullname = $category->name;

                $this->categories[$c->id] = $c;
            }
        }

        // Also fill parents array
        $this->parent_items = $this->categories;

        // Make categories unselectable
        $this->disabled_items = $this->parent_items;
    }


    /**
     * Load certifications to display
     *
     * @access  public
     */
    public function load_certifications() {
        if ($this->categoryid) {
            $certs = certif_get_certifications($this->categoryid, "p.fullname ASC", 'p.id, p.fullname, p.sortorder, p.visible');
            $this->certifications = $certs;
        }
    }


    /**
     * Generate markup, but first merge categories and certifications together
     *
     * @access  public
     * @return  string
     */
    public function generate_markup() {

        // Merge categories and certification (certifications to follow categories)
        $this->items = array_merge($this->categories, $this->certifications);

        return parent::generate_markup();
    }

    /**
     * Check if certification is accessible before displaying in search results
     *
     * @access  public
     * @param   $certifid  integer
     * @return  boolean
     */
    public function search_can_display_result($certifid) {
        $cert = new program($certifid);
        return $cert->is_accessible();
    }
}
