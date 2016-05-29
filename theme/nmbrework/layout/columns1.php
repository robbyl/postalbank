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
 * Synergy Learning's Base Responsive Theme
 *
 * DO NOT MODIFY THIS THEME!
 * COPY IT FIRST, THEN RENAME THE COPY AND MODIFY IT INSTEAD.
 *
 * @package   theme_nmbrework
 * @copyright 2013 Synergy Learning
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$custommenu = $OUTPUT->custom_menu();
$hascustommenu = !empty($custommenu);

$haslogininfo = (empty($PAGE->layout_options['nologininfo']));
$showmenu = empty($PAGE->layout_options['nocustommenu']);

if ($showmenu && !$hascustommenu) {
    // load totara menu
    $menudata = totara_build_menu();
    $totara_core_renderer = $PAGE->get_renderer('totara_core');
    $totaramenu = $totara_core_renderer->print_totara_menu($menudata);
}

echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes(); ?>>
<head>
    <title><?php echo $OUTPUT->page_title(); ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->favicon(); ?>" />
    <?php echo $OUTPUT->standard_head_html() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <script type="text/javascript" src="//use.typekit.net/jke4zbf.js"></script>
	<script type="text/javascript">try{Typekit.load();}catch(e){}</script>
</head>

<body <?php echo $OUTPUT->body_attributes('one-column'); ?>>

<?php echo $OUTPUT->standard_top_of_body_html() ?>

<header role="banner" class="navbar navbar-fixed-top">
    <nav role="navigation" class="navbar-inner">
        <div class="container-fluid">
            
            <div class="nav-collapse collapse">
            <?php if ($showmenu) { ?>
                    <?php if ($hascustommenu) { ?>
                    <div id="custommenu"><?php echo $custommenu; ?></div>
                    <?php } else { ?>
                    <div id="totaramenu"><?php echo $totaramenu; ?></div>
                    <?php } ?>
                <?php } ?>
            </div>
            
            <a class="brand" href="<?php echo $CFG->wwwroot;?>"><img src="<?php echo $CFG->wwwroot .'/theme/'. current_theme().'/pix/logo.png' ?>" alt="<?php echo $PAGE->heading ?>" /></a>
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>

             
            
            <div class="nav-collapse collapse">
                <ul class="nav pull-right">
                    <li><?php echo $OUTPUT->page_heading_menu(); ?></li>
                    <li class="lang"><?php echo $OUTPUT->lang_menu(); ?></li>
                    <?php if (!isloggedin()) { ?>
                    <li class="navbar-text"><?php echo $OUTPUT->login_info() ?></li>
                    <?php } else { ?>
                    <li class="navbar-text"><?php echo $OUTPUT->login_info() ?><?php echo $OUTPUT->user_picture($USER, array('size'=>80)); ?></li>
                    <?php } ?>
                </ul>
                
            </div>
        </div>
    </nav>
</header>

    <header id="page-header" class="clearfix">
        <div id="page-navbar">
            <nav class="breadcrumb-button"><?php echo $OUTPUT->page_heading_button(); ?></nav>
            <?php echo $OUTPUT->navbar(); ?>
            <div id="top-search">
                        <form action="<?php echo $CFG->wwwroot ?>/course/search.php" method="get">
                            <input type="text" size="12" name="search" alt="Search Courses" value="<?php echo get_string('searchcourses', 'theme_nmbrework'); ?>" onFocus="this.value = this.value=='<?php echo get_string('searchcourses', 'theme_nmbrework'); ?>'?'':this.value;" onBlur="this.value = this.value==''?'<?php echo get_string('searchcourses', 'theme_nmbrework'); ?>':this.value;" />
                            <input type="submit" value="Go" title="Go" />
                        </form>
                    </div>
        </div>
        <div id="course-header">
            <?php echo $OUTPUT->course_header(); ?>
        </div>
        
    </header>

<div id="page" class="container-fluid">


    <div id="page-content">
        <div id="region-bs-main-and-pre">
            <section id="region-main">
                <?php
                echo $OUTPUT->course_content_header();
                echo $OUTPUT->main_content();
                echo $OUTPUT->course_content_footer();
                ?>
            </section>
        </div>
    </div>
    </div>

    <footer id="page-footer">
        <div id="footerbox1">
            <h2>Live Discuss Forum</h2>
            <p>Join to share experience and learn.</p>
            <p> </p>
            <a href="#" class="footerbtn">Join Now</a>
        </div>
         <div id="footerbox2">
            <h2>Latest News</h2>
            <p>Open to View latest information on learning opportunities and events.</p>
            <a href="#" class="footerbtn">View</a>
        </div>
        
        <p class="copy">© National Microfinance Bank 2014</p>
    </footer>

    <?php echo $OUTPUT->standard_end_of_body_html() ?>


</body>
</html>
