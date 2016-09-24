<?php

//修正管理後台頁尾顯示
function dashboard_footer_design() {
	echo 'Design by <a href="http://www.knockers.com.tw">Knockers</a>';
}
add_filter('admin_footer_text', 'dashboard_footer_design');
//修正管理後台頁尾顯示
function dashboard_footer_developer() {
	echo '<br/><span id="footer-thankyou">Developed by <a href="https://www.mxp.tw">一介資男</a></span>';
}
add_filter('admin_footer_text', 'dashboard_footer_developer');
//修正管理後台顯示
function clean_my_admin_head() {
	$screen = get_current_screen();
	$str = '';
	if (is_admin() && ($screen->id == 'dashboard')) {
		$str .= '<style>#wp-version-message { display: none; } #footer-upgrade {display: none;}</style>';
	}
	echo $str;
}
add_action('admin_head', 'clean_my_admin_head');
//優化主題樣式相關
function optimize_theme_setup() {
	//整理head資訊
	remove_action('wp_head', 'wp_generator');
	remove_action('wp_head', 'wlwmanifest_link');
	remove_action('wp_head', 'rsd_link');
	remove_action('wp_head', 'wp_shortlink_wp_head');
	add_filter('the_generator', '__return_false');
	add_filter('show_admin_bar', '__return_false');
	remove_action('wp_head', 'print_emoji_detection_script', 7);
	remove_action('wp_print_styles', 'print_emoji_styles');
	remove_action('wp_head', 'feed_links_extra', 3);
	//移除css, js資源載入時的版本資訊
	function remove_version_query($src) {
		if (strpos($src, 'ver=')) {
			$src = remove_query_arg('ver', $src);
		}
		return $src;
	}
	add_filter('style_loader_src', 'remove_version_query', 999);
	add_filter('script_loader_src', 'remove_version_query', 999);
}
add_action('after_setup_theme', 'optimize_theme_setup');
