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
</head>

<body <?php echo $OUTPUT->body_attributes('three-column'); ?>>

<?php echo $OUTPUT->standard_top_of_body_html() ?>

<header role="banner" class="navbar navbar-fixed-top">
    <nav role="navigation" class="navbar-inner">
    <div class="topheader">
        <ul class="nav pull-right">
            <li><?php echo $OUTPUT->page_heading_menu(); ?></li>
            <li class="navbar-text"><?php echo $OUTPUT->login_info() ?></li>
            <li class="lang"><?php echo $OUTPUT->lang_menu(); ?></li>
        </ul>
    </div>

        <div class="container-fluid">
            <a class="brand" href="<?php echo $CFG->wwwroot;?>"><img src="<?php echo $CFG->wwwroot .'/theme/'. current_theme().'/pix/logo.png' ?>" alt="<?php echo $PAGE->heading ?>" /></a>
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <div class="nav-collapse collapse">
                
                <?php if ($showmenu) { ?>
                    <?php if ($hascustommenu) { ?>
                    <div id="custommenu"><?php echo $custommenu; ?></div>
                    <?php } else { ?>
                    <div id="totaramenu"><?php echo $totaramenu; ?></div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </nav>
</header>

<div id="page" class="container-fluid">

    <header id="page-header" class="clearfix">
        <div id="page-navbar">
            <nav class="breadcrumb-button"><?php echo $OUTPUT->page_heading_button(); ?></nav>
            <?php echo $OUTPUT->navbar(); ?>
        </div>
        <div id="course-header">
            <?php echo $OUTPUT->course_header(); ?>
        </div>
    </header>

    <div id="page-content" class="row-fluid">
        <div id="region-bs-main-and-pre" class="span9">
            <div class="row-fluid">
                <section id="region-main" class="span8 pull-right">
                    <?php
                    echo $OUTPUT->course_content_header();
                    echo $OUTPUT->main_content();
                    echo $OUTPUT->course_content_footer();
                    ?>
                </section>
                <?php echo $OUTPUT->blocks('side-pre', 'span4 desktop-first-column'); ?>
            </div>
        </div>
        <?php echo $OUTPUT->blocks('side-post', 'span3'); ?>
    </div>

    <footer id="page-footer">
        <div id="course-footer"><?php echo $OUTPUT->course_footer(); ?></div>
        <p class="helplink"><?php echo $OUTPUT->page_doc_link(); ?></p>
        <?php
        echo $OUTPUT->login_info();
        echo $OUTPUT->home_link();
        echo $OUTPUT->standard_footer_html();
        ?>
    </footer>

    <?php echo $OUTPUT->standard_end_of_body_html() ?>

</div>
</body>
</html>
