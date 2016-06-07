<?php
// file: /theme/sometheme/lib.php
function theme_ilearn_page_init(moodle_page $page) {
    $page->requires->jquery();
    
		 $page->requires->jquery_plugin('flexslider', 'theme_ilearn');
		 $page->requires->jquery_plugin('custom', 'theme_ilearn');
}