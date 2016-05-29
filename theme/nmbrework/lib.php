<?php
// file: /theme/sometheme/lib.php
function theme_nmbrework_page_init(moodle_page $page) {
    $page->requires->jquery();
    
		 $page->requires->jquery_plugin('flexslider', 'theme_nmbrework');
		 $page->requires->jquery_plugin('custom', 'theme_nmbrework');
}