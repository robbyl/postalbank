<?php
// file: /theme/sometheme/lib.php
function theme_tpb_page_init(moodle_page $page) {
    $page->requires->jquery();
    
		 $page->requires->jquery_plugin('flexslider', 'theme_tpb');
		 $page->requires->jquery_plugin('custom', 'theme_tpb');
}