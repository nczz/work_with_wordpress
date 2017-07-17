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

//open content block for VC
add_filter('content_block_post_type', '__return_true');

//使用 content block 時會被當作一般的 post 被安插其他處理，自己包過來用
//ref: https://tw.wordpress.org/plugins/custom-post-widget/
function knockers_custom_post_widget_shortcode($atts) {
	extract(shortcode_atts(array(
		'id' => '',
		'slug' => '',
		'class' => 'content_block',
		'suppress_content_filters' => 'yes', //預設不走 the_content 的事件，避免被其他方法給包過
		'title' => 'no',
		'title_tag' => 'h3',
		'only_img' => 'no', //僅輸出特色圖片連結
	), $atts));

	if ($slug) {
		$block = get_page_by_path($slug, OBJECT, 'content_block');
		if ($block) {
			$id = $block->ID;
		}
	}

	$content = "";

	if ($id != "") {
		$args = array(
			'post__in' => array($id),
			'post_type' => 'content_block',
		);

		$content_post = get_posts($args);

		foreach ($content_post as $post):
			$content .= '<div class="' . esc_attr($class) . '" id="custom_post_widget-' . $id . '">';
			if ($title === 'yes') {
				$content .= '<' . esc_attr($title_tag) . '>' . $post->post_title . '</' . esc_attr($title_tag) . '>';
			}
			if ($suppress_content_filters === 'no') {
				$content .= apply_filters('the_content', $post->post_content);
			} else {
				if (has_shortcode($post->post_content, 'content_block') || has_shortcode($post->post_content, 'ks_content_block')) {
					$content .= $post->post_content;
				} else {
					$content .= do_shortcode($post->post_content);
				}
			}
			$content .= '</div>';
		endforeach;
	}
	if ($only_img == "yes") {
		$featured_image = get_the_post_thumbnail_url($id, 'full');
		return $featured_image ? $featured_image : $content;
	}
	return $content;
}
add_shortcode('ks_content_block', 'knockers_custom_post_widget_shortcode');