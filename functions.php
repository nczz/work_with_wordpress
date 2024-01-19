<?php
//最佳化主題樣式相關
function mxp_optimize_theme_setup() {
    //整理head資訊
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    add_filter('the_generator', '__return_false');
    //管理員等級的角色不要隱藏 admin bar
    $user          = wp_get_current_user();
    $allowed_roles = array('editor', 'administrator', 'author');
    if (!array_intersect($allowed_roles, $user->roles)) {
        add_filter('show_admin_bar', '__return_false');
    }
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
    add_filter('widget_text', 'do_shortcode');
    // 讓主題支援使用 WC 的方法
    if (class_exists('WooCommerce')) {
        add_theme_support('woocommerce');
        add_theme_support('wc-product-gallery-zoom');
        add_theme_support('wc-product-gallery-lightbox');
        add_theme_support('wc-product-gallery-slider');
    }
}
add_action('after_setup_theme', 'mxp_optimize_theme_setup');

// 停用表情符號 & 嵌入 & RSS 等功能
function mxp_disable_emojis_tinymce($plugins) {
    if (is_array($plugins)) {
        return array_diff($plugins, array('wpemoji'));
    } else {
        return array();
    }
}
function disable_embeds_tiny_mce_plugin($plugins) {
    return array_diff($plugins, array('wpembed'));
}
function disable_embeds_rewrites($rules) {
    foreach ($rules as $rule => $rewrite) {
        if (false !== strpos($rewrite, 'embed=true')) {
            unset($rules[$rule]);
        }
    }
    return $rules;
}
function mxp_disable_functions() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    remove_action('rest_api_init', 'wp_oembed_register_route');
    add_filter('embed_oembed_discover', '__return_false');
    remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'wp_oembed_add_host_js');
    add_filter('tiny_mce_plugins', 'disable_embeds_tiny_mce_plugin');
    add_filter('rewrite_rules_array', 'disable_embeds_rewrites');
    remove_filter('pre_oembed_result', 'wp_filter_pre_oembed_result', 10);
    add_filter('tiny_mce_plugins', 'mxp_disable_emojis_tinymce');
    remove_action('wp_head', 'feed_links_extra', 3);
    remove_action('wp_head', 'feed_links', 2);
}
add_action('init', 'mxp_disable_functions');

// 停用自己站內的引用
function mxp_disable_self_ping(&$links) {
    $home = get_option('home');
    foreach ($links as $l => $link) {
        if (0 === strpos($link, $home)) {
            unset($links[$l]);
        }
    }
}
add_action('pre_ping', 'mxp_disable_self_ping');

function mxp_disable_rss_feed_function() {
    wp_die(__('無提供 RSS 服務，請返回 <a rel="noopener" href="' . esc_url(home_url('/')) . '">首頁</a>!'));
}
add_action('do_feed', 'mxp_disable_rss_feed_function', 1);
add_action('do_feed_rdf', 'mxp_disable_rss_feed_function', 1);
add_action('do_feed_rss', 'mxp_disable_rss_feed_function', 1);
add_action('do_feed_rss2', 'mxp_disable_rss_feed_function', 1);
add_action('do_feed_atom', 'mxp_disable_rss_feed_function', 1);
add_action('do_feed_rss2_comments', 'mxp_disable_rss_feed_function', 1);
add_action('do_feed_atom_comments', 'mxp_disable_rss_feed_function', 1);

function mxp_admin_menu_modify_for_user() {
    global $submenu, $menu, $pagenow;
    $user          = wp_get_current_user();
    $allowed_roles = array('administrator');
    //不是管理員，都把下面的設定選項移除
    if (!array_intersect($allowed_roles, $user->roles)) {
        if (isset($submenu['themes.php'])) {
            foreach ($submenu['themes.php'] as $index => $menu_item) {
                foreach ($menu_item as $value) {
                    if (strpos($value, 'customize') !== false) {
                        unset($submenu['themes.php'][$index]);
                    }
                    // if (strpos($value, 'edit_theme_options') !== false) {
                    //     unset($submenu['themes.php'][$index]);
                    // }
                }
            }
        }
        if (isset($submenu['options-general.php'])) {
            foreach ($submenu['options-general.php'] as $index => $menu_item) {
                foreach ($menu_item as $value) {
                    if (strpos($value, 'menu_editor') !== false) {
                        unset($submenu['options-general.php'][$index]);
                    }
                    if (strpos($value, 'adminimize-options') !== false) {
                        unset($submenu['options-general.php'][$index]);
                    }
                    if (strpos($value, 'settings-user-role-editor.php') !== false) {
                        unset($submenu['options-general.php'][$index]);
                    }
                }
            }
        }
        if (isset($submenu['users.php'])) {
            foreach ($submenu['users.php'] as $index => $menu_item) {
                foreach ($menu_item as $value) {
                    if (strpos($value, 'users-user-role-editor.php') !== false) {
                        unset($submenu['users.php'][$index]);
                    }
                }
            }
        }
    }
}
add_action('admin_init', 'mxp_admin_menu_modify_for_user', 100);

// 隱藏主題「自訂」的連結方法
function mxp_remove_customize_link() {
    $user          = wp_get_current_user();
    $allowed_roles = array('administrator');
    //不是管理員，都把下面的設定選項移除
    if (!array_intersect($allowed_roles, $user->roles)) {
        $customize_url = add_query_arg('return', urlencode(remove_query_arg(wp_removable_query_args(), wp_unslash($_SERVER['REQUEST_URI']))), 'customize.php');
        remove_submenu_page('themes.php', $customize_url);
    }
}
add_action('admin_menu', 'mxp_remove_customize_link');

//open content block for VC
add_filter('content_block_post_type', '__return_true');

//使用 content block 時會被當作一般的 post 被安插其他處理，自己包過來用
//ref: https://tw.wordpress.org/plugins/custom-post-widget/
function knockers_custom_post_widget_shortcode($atts) {
    extract(shortcode_atts(array(
        'id'                       => '',
        'slug'                     => '',
        'class'                    => 'content_block',
        'suppress_content_filters' => 'yes', //預設不走 the_content 的事件，避免被其他方法給包過
        'title'                    => 'no',
        'title_tag'                => 'h3',
        'only_img'                 => 'no', //僅輸出特色圖片連結
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
            'post__in'  => array($id),
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

//阻止縮圖浪費空間
function mxp_remove_attachment_metadata_size($data, $attachment_id) {
    if (isset($data['sizes'])) {
        // 清空設定的大小，強迫輸出原圖
        $data['sizes'] = array();
    }
    return $data;
}
add_filter('wp_get_attachment_metadata', 'mxp_remove_attachment_metadata_size', 11, 2);
add_filter('intermediate_image_sizes', '__return_empty_array');
add_filter('intermediate_image_sizes_advanced', '__return_empty_array');
add_filter('fallback_intermediate_image_sizes', '__return_empty_array');
// 禁用 WC 背景縮圖功能
add_filter('woocommerce_background_image_regeneration', '__return_false');
//取消預設 2560 寬高限制
add_filter('big_image_size_threshold', '__return_false');

//上傳檔案時判斷為圖片時自動加上標題、替代標題、摘要、描述等內容
function ks_set_image_meta_upon_image_upload($post_ID) {

    if (wp_attachment_is_image($post_ID)) {
        $my_image_title = get_post($post_ID)->post_title;
        $my_image_title = preg_replace('%\s*[-_\s]+\s*%', ' ', $my_image_title);
        $my_image_title = ucwords(strtolower($my_image_title));
        $my_image_meta  = array(
            'ID'           => $post_ID,
            'post_title'   => $my_image_title,
            'post_excerpt' => $my_image_title,
            'post_content' => $my_image_title,
        );
        update_post_meta($post_ID, '_wp_attachment_image_alt', $my_image_title);
        wp_update_post($my_image_meta);
    }
}
add_action('add_attachment', 'ks_set_image_meta_upon_image_upload');

function ks_add_theme_caps() {
    $roles = array('editor', 'contributor', 'author', 'shop_manager');
    foreach ($roles as $key => $role) {
        //取得授權角色
        if ($role = get_role($role)) {
            //開通權限，對權限控管，可以補上預設的權限來避免設定疏失
            $role->add_cap('unfiltered_html');
            $role->add_cap('edit_theme_options');
            //開通 https://tw.wordpress.org/plugins/contact-form-cfdb7/ 這外掛使用權限
            $role->add_cap('cfdb7_access');
            //開通 https://tw.wordpress.org/plugins/contact-form-entries/ 這外掛使用權限
            $role->add_cap('vxcf_leads_edit_entries');
            $role->add_cap('vxcf_leads_edit_settings');
            $role->add_cap('vxcf_leads_read_entries');
            $role->add_cap('vxcf_leads_read_license');
            $role->add_cap('vxcf_leads_read_settings');
            $role->add_cap('vx_crmperks_view_plugins');
            $role->add_cap('vx_crmperks_edit_addons');
            $role->add_cap('vx_crmperks_view_addons');
            //開通 WP Rocket v3.4.4 之後的使用權限
            $role->add_cap('rocket_manage_options');
            $role->add_cap('rocket_purge_cache');
            $role->add_cap('rocket_purge_opcache');
            $role->add_cap('rocket_purge_cloudflare_cache');
            $role->add_cap('rocket_preload_cache');
            $role->add_cap('rocket_regenerate_critical_css');
        }
    }
}
add_action('admin_init', 'ks_add_theme_caps');

//給CF7啟用短碼機制
function mxp_cf7_do_shortcode() {
    add_filter('wpcf7_form_elements', 'do_shortcode');
    add_filter('wpcf7_autop_or_not', '__return_false');
}
add_action('admin_init', 'mxp_cf7_do_shortcode');

/**
 * 如果VC還沒解開角色授權使用的BUG，可以編輯外掛 plugins/js_composer/include/classes/core/access/class-vc-role-access-controller.php 檔案強制修正 can 方法中處理權限的部份。
 **/
function ks_custom_post_type_support_vc($support, $type) {
    $allow_post_type = array('post', 'page', 'content_block');
    return in_array($type, $allow_post_type);
}
add_filter('vc_is_valid_post_type_be', 'ks_custom_post_type_support_vc', 999, 2);

//如果使用CF7，5.1版後都會因為使用reCaptcha導致每頁都會顯示徽章，使用這方法避免
function mxp_remove_recaptcha_badge() {
    echo '<style>.grecaptcha-badge{ visibility: collapse !important; }</style>';
}
add_action('wp_footer', 'mxp_remove_recaptcha_badge');

//使用 instant.page 加速網站頁面讀取
function mxp_add_instant_page() {
    echo '<script src="//instant.page/5.2.0" type="module" integrity="sha384-jnZyxPjiipYXnSU0ygqeac2q7CVYMbh84q0uHVRRxEtvFPiQYbXWUorga2aqZJ0z"></script>';
}
add_action('wp_footer', 'mxp_add_instant_page');

//開啟隱私權頁面修改權限
function add_privacy_page_edit_cap($caps, $cap, $user_id, $args) {
    if ('manage_privacy_options' === $cap) {
        $manage_name = is_multisite() ? 'manage_network' : 'manage_options';
        $caps        = array_diff($caps, [$manage_name]);
    }
    return $caps;
}
add_filter('map_meta_cap', 'add_privacy_page_edit_cap', 10, 4);

//去除有管理權限之外人的通知訊息
function mxp_hide_update_msg_non_admins() {
    $user = wp_get_current_user();
    if (!in_array('administrator', (array) $user->roles)) {
        // non-admin users
        echo '<style>#setting-error-tgmpa>.updated settings-error notice is-dismissible, .update-nag, .updated { display: none; }</style>';
    }
    //隱藏非管理人員的更新通知
    if (!current_user_can('update_core')) {
        remove_action('admin_notices', 'update_nag', 3);
    }
}
add_action('admin_head', 'mxp_hide_update_msg_non_admins', 1);

//修改「網站遭遇技術性問題」通知信收件人
function mxp_change_recovery_mode_email($email, $url) {
    $email['to'] = 'im@mxp.tw'; //收件人
    // $email['subject'] //主旨
    // $email['message'] //內文
    // $email['headers'] //信件標頭
    return $email;
}
add_filter('recovery_mode_email', 'mxp_change_recovery_mode_email', 11, 2);

// 刪除文章前的防呆提醒機制
function mxp_delete_post_confirm_hook() {
    ?>
    <script>
jQuery(document).ready(function(){
    jQuery(".submitdelete").click(function() {
        if (!confirm("確定要刪除嗎？")){
            return false;
        }
    });
    jQuery('#doaction').click(function(){
        var top_action = jQuery('#bulk-action-selector-top').val();
        if ('trash'==top_action){
            if (!confirm("確定要刪除嗎？")){
                return false;
            }
        }
    });
    jQuery('#doaction2').click(function(){
        var bottom_action = jQuery('#bulk-action-selector-bottom').val();
        if ('trash'==bottom_action){
            if (!confirm("確定要刪除嗎？")){
                return false;
            }
        }
    });
    jQuery('#delete_all').click(function(){
        if (!confirm("確定要清空嗎？此動作執行後無法回復。")){
            return false;
        }
    });
});
</script>
<?php
}
add_action('admin_footer', 'mxp_delete_post_confirm_hook');

//限制上傳大小以及轉正JPG影像（如果有EXIF資訊的話）https://www.mxp.tw/9318/
function mxp_max_image_size_and_image_orientation($file) {
    $limit = 500; // 500kb 上限
    $size  = $file['size'] / 1024;
    if (!version_compare(get_bloginfo('version'), '5.3', '>=')) {
        // v5.3 後已經內建 https://developer.wordpress.org/reference/classes/wp_image_editor_imagick/maybe_exif_rotate/
        mxp_apply_new_orientation($file['tmp_name']);
    }
    $is_image = strpos($file['type'], 'image') !== false;
    if ($is_image && $size > $limit) {
        $file['error'] = "上傳的影像大小請小於 {$limit}kb";
    }
    return $file;
}
function mxp_apply_new_orientation($path_to_jpg) {
    // 使用 GD 函式庫，沒的話就算了不處理
    if (!extension_loaded('gd') ||
        !function_exists('gd_info') ||
        !function_exists('exif_imagetype') ||
        !function_exists('imagecreatefromjpeg') ||
        !function_exists('exif_read_data') ||
        !function_exists('imagerotate') ||
        !function_exists('imagejpeg') ||
        !function_exists('imagedestroy')) {
        return false;
    }
    if (exif_imagetype($path_to_jpg) == IMAGETYPE_JPEG) {
        $image = @imagecreatefromjpeg($path_to_jpg);
        $exif  = exif_read_data($path_to_jpg);
        if (!empty($exif['Orientation'])) {
            switch ($exif['Orientation']) {
            case 3:
                $image = imagerotate($image, 180, 0);
                break;
            case 6:
                $image = imagerotate($image, 90, 0);
                break;
            case 8:
                $image = imagerotate($image, -90, 0);
                break;
            }
        }
        if ($image) {
            imagejpeg($image, $path_to_jpg);
            imagedestroy($image);
        }
    }
}
add_filter('wp_handle_upload_prefilter', 'mxp_max_image_size_and_image_orientation');

//開放 Post SMTP 設定頁面權限
function mxp_option_page_capability_postman_group($cap) {
    return 'edit_pages';
}
add_filter("option_page_capability_postman_group", 'mxp_option_page_capability_postman_group', 10, 1);

//預設關閉 XML_RPC
add_filter('xmlrpc_enabled', '__return_false');
//輸出 X-Frame-Options HTTP Header
add_action('send_headers', 'send_frame_options_header', 10, 0);
//關閉 HTTP Header 中出現的 Links
add_filter('oembed_discovery_links', '__return_null');
remove_action('wp_head', 'rest_output_link_wp_head', 10);
remove_action('template_redirect', 'rest_output_link_header', 11);
remove_action('template_redirect', 'wp_shortlink_header', 11);
// 關閉 wp-json 首頁顯示的 API 清單
add_filter('rest_index', '__return_empty_array');
// 沒登入的使用者都無法呼叫 wp/users 這隻 API。不建議完全封鎖掉，會導致有些後台功能運作失靈
if (function_exists('is_user_logged_in') && !is_user_logged_in()) {
    add_filter('rest_user_query', '__return_null');
    add_filter('rest_prepare_user', '__return_null');
}

function mxp_security_headers($headers) {
    $headers['X-XSS-Protection']                  = '1; mode=block';
    $headers['X-Content-Type-Options']            = 'nosniff';
    $headers['X-Content-Security-Policy']         = "default-src 'self'; script-src 'self'; connect-src 'self'";
    $headers['X-Permitted-Cross-Domain-Policies'] = "none";
    $headers['Strict-Transport-Security']         = 'max-age=31536000; includeSubDomains; preload';
    return $headers;
}
add_filter('wp_headers', 'mxp_security_headers');

//內對外請求管制方法
function mxp_block_external_request($preempt, $parsed_args, $url) {
    $block_urls = array(
        "wpemaillog.com",
        "cdn.aioseo.com",
        "aioseo.com",
        "licensing-cdn.aioseo.com",
        "updates.wpbakery.com",
        "my.yoast.com",
    );
    // $whitelist_urls = array(
    //     "api.wordpress.org",
    //     "downloads.wordpress.org",
    // );
    $request_domain = parse_url($url, PHP_URL_HOST);
    if (in_array($request_domain, $block_urls, true)) {
        return new WP_Error('http_request_block', '不允許的對外請求路徑' . "\n:: {$url}", $url);
    }
    return $preempt;
}
add_filter("pre_http_request", "mxp_block_external_request", 11, 3);

// 預設不直接更新大版本，僅安全性版本更新
add_filter('allow_major_auto_core_updates', '__return_false');

// 後台不顯示 Freemius 的提示通知
add_filter('fs_show_admin_notice_post-smtp', function ($show, $msg) {
    return false;
}, 11, 2);

// 預設不顯示出系統輸出的作者連結與頁面，避免資安問題
function mxp_hide_author_for_safe($link) {
    return '小編';
}
add_filter('the_author_posts_link', 'mxp_hide_author_for_safe', 11, 1);

// 預設作者的連結都不顯示
function mxp_hide_author_link($link, $author_id, $author_nicename) {
    return '#';
}
add_filter('author_link', 'mxp_hide_author_link', 3, 100);

// 在文章、頁面列表和其他CPT列表頁面中顯示文章的 ID
function mxp_show_post_id_admin_columns($columns) {
    $columns['post_id'] = 'ID';
    return $columns;
}
// 在文章、頁面列表和其他CPT列表頁面中顯示文章的 ID 值
function mxp_show_post_id_admin_column_content($column_name, $post_id) {
    if ($column_name == 'post_id') {
        echo $post_id;
    }
}
$all_post_type = get_post_types(array('_builtin' => false));
add_filter('manage_posts_columns', 'mxp_show_post_id_admin_columns');
add_filter('manage_pages_columns', 'mxp_show_post_id_admin_columns');
add_action('manage_posts_custom_column', 'mxp_show_post_id_admin_column_content', 10, 2);
add_action('manage_pages_custom_column', 'mxp_show_post_id_admin_column_content', 10, 2);
foreach ($all_post_type as $key => $mxp_cpt) {
    add_filter('manage_' . $mxp_cpt . '_posts_columns', 'mxp_show_post_id_admin_columns');
}
foreach ($all_post_type as $key => $mxp_cpt) {
    add_filter('manage_' . $mxp_cpt . '_posts_custom_column', 'mxp_show_post_id_admin_column_content', 10, 2);
}
// 顯示使用者編號
function add_user_id_column($columns) {
    $columns['mxp_user_id'] = 'ID';
    return $columns;
}
add_filter('manage_users_columns', 'add_user_id_column');
function show_user_id_column_content($value, $column_name, $user_id) {
    if ('mxp_user_id' === $column_name) {
        return $user_id;
    }
    return $value;
}
add_action('manage_users_custom_column', 'show_user_id_column_content', 10, 3);

function mxp_add_custom_fields_for_user_search($user_query) {
    global $wpdb;
    if (isset($user_query->query_vars['search']) && !empty($user_query->query_vars['search']) && is_admin() && isset($_REQUEST['s']) && !empty($_REQUEST['s'])) {
        $search_term = str_replace('*', '%', $user_query->query_vars['search']);
        $user_query->query_from .= " JOIN {$wpdb->usermeta} usrmeta ON usrmeta.user_id = {$wpdb->users}.ID";
        // 加入客製化欄位條件
        $custom_fields = array(
            'billing_address_1',
            'billing_address_2',
            'billing_city',
            'billing_company',
            'billing_country',
            'billing_email',
            'billing_first_name',
            'billing_last_name',
            'billing_phone',
            'billing_postcode',
            'billing_state',
            'billing_video_playback_gmail',
            'description',
            'first_name',
            'ks_address',
            'ks_birth_year',
            'ks_cell',
            'ks_city_district',
            'ks_cn_first_name',
            'ks_cn_last_name',
            'ks_country',
            'ks_country_code',
            'ks_en_first_name',
            'ks_en_last_name',
            'ks_gender',
            'ks_google_addr',
            'ks_line_id',
            'ks_postal_code',
            'ks_recommender',
            'ks_state_province_city',
            'ks_wechat_id',
            'last_name',
            'nickname',
            'shipping_address_1',
            'shipping_address_2',
            'shipping_city',
            'shipping_company',
            'shipping_country',
            'shipping_email',
            'shipping_first_name',
            'shipping_last_name',
            'shipping_method',
            'shipping_phone',
            'shipping_postcode',
            'shipping_state',
            'twitter',
        );
        $where = array();
        foreach ($custom_fields as $key => $field) {
            $where[] = "usrmeta.meta_key LIKE '" . esc_sql($field) . "' AND usrmeta.meta_value LIKE '" . esc_sql($search_term) . "'";
        }
        // 組合搜尋條件
        $user_query->query_where .= ' OR (' . implode(' OR ', $where) . ')';
    }
}

// add_action('pre_user_query', 'mxp_add_custom_fields_for_user_search', 11, 1);

// 每次更新完後檢查 xmlrpc.php 還在不在，在就砍掉，避免後患
function mxp_after_upgrade_hook($upgrader_object, $options) {
    // if ($options['action'] == 'update' && $options['type'] == 'plugin') {
    //     foreach ($options['plugins'] as $each_plugin) {
    //         if ($each_plugin == $current_plugin_path_name) {
    //             // 比對當前外掛的更新
    //         }
    //     }
    // }
    if (file_exists(ABSPATH . 'xmlrpc.php')) {
        unlink(ABSPATH . 'xmlrpc.php');
    }
    if (file_exists(ABSPATH . 'wp-admin/install.php')) {
        unlink(ABSPATH . 'wp-admin/install.php');
    }
}
add_action('upgrader_process_complete', 'mxp_after_upgrade_hook', 10, 2);

// 關閉 heartbeat 功能
function mxp_stop_heartbeat_function() {
    wp_deregister_script('heartbeat');
}
add_action('init', 'mxp_stop_heartbeat_function', 1);

// 取消站內的全球大頭貼功能，全改為預設大頭貼
function mxp_pre_get_empty_avatar_data($args, $id_or_email) {
    //email md5 wordpress.gravatar@mxp.tw
    if (is_ssl()) {
        $url = 'https://secure.gravatar.com/avatar/fcb68cd8e48d96e2bc306b17422e34ea?s=192&d=mm&r=g';
    } else {
        $url = 'http://0.gravatar.com/avatar/fcb68cd8e48d96e2bc306b17422e34ea?s=192&d=mm&r=g';
    }
    $args['url'] = $url;
    return $args;
}
add_filter('pre_get_avatar_data', 'mxp_pre_get_empty_avatar_data', PHP_INT_MAX, 2);

//遮蔽所有留言中留言人提供的網址
function mxp_get_comment_author_url($url, $id, $comment) {
    return "";
}
add_filter('get_comment_author_url', 'mxp_get_comment_author_url', 11, 3);

if (!function_exists('wpdb_bulk_insert')) {
    //一次大量新增資料的資料庫操作方法  Ref: https://gist.github.com/pauln/884e1a229d439640fbe35e848852fe0b
    function wpdb_bulk_insert($table, $rows) {
        global $wpdb;

        // Extract column list from first row of data
        $columns = array_keys($rows[0]);
        asort($columns);
        $columnList = '`' . implode('`, `', $columns) . '`';

        // Start building SQL, initialise data and placeholder arrays
        $sql          = "INSERT INTO `$table` ($columnList) VALUES\n";
        $placeholders = array();
        $data         = array();

        // Build placeholders for each row, and add values to data array
        foreach ($rows as $row) {
            ksort($row);
            $rowPlaceholders = array();

            foreach ($row as $key => $value) {
                $data[]            = $value;
                $rowPlaceholders[] = is_numeric($value) ? '%d' : '%s';
            }

            $placeholders[] = '(' . implode(', ', $rowPlaceholders) . ')';
        }

        // Stitch all rows together
        $sql .= implode(",\n", $placeholders);

        // Run the query.  Returns number of affected rows.
        return $wpdb->query($wpdb->prepare($sql, $data));
    }
}

if (!function_exists('mxp_get_error_backtrace')) {
    // WordPress PHP 偵錯用的方法
    function mxp_get_error_backtrace($last_error_file = __FILE__, $for_irc = false) {

        $backtrace = debug_backtrace(0);
        $call_path = array();
        foreach ($backtrace as $bt_key => $call) {
            if (!isset($call['args'])) {
                $call['args'] = array('');
            }

            if (in_array($call['function'], array(__FUNCTION__, 'mxp_get_error_backtrace'))) {
                continue;
            }

            $path = '';
            if (!$for_irc) {
                $path = isset($call['file']) ? str_replace(ABSPATH, '', $call['file']) : '';
                $path .= isset($call['line']) ? ':' . $call['line'] : '';
            }

            if (isset($call['class'])) {
                $call_type = $call['type'] ? $call['type'] : '???';
                $path .= " {$call['class']}{$call_type}{$call['function']}()";
            } elseif (in_array($call['function'], array('do_action', 'apply_filters'))) {
                if (is_object($call['args'][0]) && !method_exists($call['args'][0], '__toString')) {
                    $path .= " {$call['function']}(Object)";
                } elseif (is_array($call['args'][0])) {
                    $path .= " {$call['function']}(Array)";
                } else {
                    $path .= " {$call['function']}('{$call['args'][0]}')";
                }
            } elseif (in_array($call['function'], array('include', 'include_once', 'require', 'require_once'))) {
                $file = 0 == $bt_key ? $last_error_file : $call['args'][0];
                $path .= " {$call['function']}('" . str_replace(ABSPATH, '', $file) . "')";
            } else {
                $path .= " {$call['function']}()";
            }

            $call_path[] = trim($path);
        }

        return implode(', ' . PHP_EOL, $call_path);
    }
}
// 關閉後臺的「網站活動」區塊
function mxp_remove_dashboard_widgets() {
    remove_meta_box('dashboard_activity', 'dashboard', 'normal');
}
add_action('wp_dashboard_setup', 'mxp_remove_dashboard_widgets');

// 不要發通知給終端使用者
function mxp_wp_send_new_user_notification_to_user($bool, $user) {
    return false;
}
add_filter('wp_send_new_user_notification_to_user', 'mxp_wp_send_new_user_notification_to_user', 11, 2);

// 改收件人為客戶信箱
function mxp_wp_new_user_notification_email_admin($wp_new_user_notification_email_admin, $user, $blogname) {
    $wp_new_user_notification_email_admin['to'] = 'im@mxp.tw';
    return $wp_new_user_notification_email_admin;
}
add_filter('wp_new_user_notification_email_admin', 'mxp_wp_new_user_notification_email_admin', 11, 3);

// 取代管理員信箱為客戶信箱
function mxp_password_change_email($pass_change_email, $user, $userdata) {
    $pass_change_email['message'] = str_replace('###ADMIN_EMAIL###', 'im@mxp.tw', $pass_change_email['message']);

    return $pass_change_email;
}
add_filter('password_change_email', 'mxp_password_change_email', 11, 3);
add_filter('email_change_email', 'mxp_password_change_email', 11, 3);

// 給發信標題全都加上請勿回覆字樣
function mxp_wp_mail_add_subject_prefix($atts) {
    $atts['subject'] = "(請勿回覆/No-Reply) " . $atts['subject'];
    return $atts;
}
add_filter('wp_mail', 'mxp_wp_mail_add_subject_prefix', 11, 1);

// 使用者登入後轉址回指定位置
function mxp_redirect_to_after_login() {
    if (!is_user_logged_in()) {
        $redirect_to = isset($_GET['redirect_to']) ? $_GET['redirect_to'] : '';
        if (strpos($redirect_to, get_site_url()) === 0) {
            setcookie('mxp_redirect_to', $redirect_to);
            setcookie('mxp_redirect_to_count', 0);
        }
    } else {
        if (isset($_COOKIE['mxp_redirect_to']) && $_COOKIE['mxp_redirect_to'] != '' && isset($_COOKIE['mxp_redirect_to_count']) && $_COOKIE['mxp_redirect_to_count'] != 0) {
            setcookie("mxp_redirect_to", "", time() - 3600);
            setcookie('mxp_redirect_to_count', 1);
            wp_redirect($_COOKIE['mxp_redirect_to']);
            exit;
        }
    }
}
add_action('template_redirect', 'mxp_redirect_to_after_login', -1);

/**
 ** 選擇性新增程式碼片段
 **/
// // 如果要關閉某些 CPT 的區塊編輯器可以啟用此區
// function mxp_disable_gutenberg($current_status, $post_type) {
//     if ($post_type === 'page') {
//         return false;
//     }
//     return $current_status;
// }
// add_filter('use_block_editor_for_post_type', 'mxp_disable_gutenberg', 11, 2);
// //修正管理後台頁尾顯示
// function dashboard_footer_design() {
//     echo 'Design by <a href="http://www.knockers.com.tw">Knockers</a>';
// }
// add_filter('admin_footer_text', 'dashboard_footer_design');
// //修正管理後台頁尾顯示
// function dashboard_footer_developer() {
//     echo '<br/><span id="footer-thankyou">Developed by <a href="https://www.mxp.tw">一介資男</a></span>';
// }
// add_filter('admin_footer_text', 'dashboard_footer_developer');
// //修正管理後台顯示
// function clean_my_admin_head() {
//     $screen = get_current_screen();
//     $str    = '';
//     if (is_admin() && ($screen->id == 'dashboard')) {
//         $str .= '<style>#wp-version-message { display: none; } #footer-upgrade {display: none;}</style>';
//     }
//     echo $str;
// }
// add_action('admin_head', 'clean_my_admin_head');

// 補上客製化檔案格式支援
// function mxp_custom_mime_types($mime_types) {
//     $mime_types['zip']  = 'application/zip';
//     $mime_types['rar']  = 'application/x-rar-compressed';
//     $mime_types['tar']  = 'application/x-tar';
//     $mime_types['gz']   = 'application/x-gzip';
//     $mime_types['gzip'] = 'application/x-gzip';
//     $mime_types['tiff'] = 'image/tiff';
//     $mime_types['tif']  = 'image/tiff';
//     $mime_types['bmp']  = 'image/bmp';
//     $mime_types['svg']  = 'image/svg+xml';
//     $mime_types['psd']  = 'image/vnd.adobe.photoshop';
//     $mime_types['ai']   = 'application/postscript';
//     $mime_types['indd'] = 'application/x-indesign';
//     $mime_types['eps']  = 'application/postscript';
//     $mime_types['rtf']  = 'application/rtf';
//     $mime_types['txt']  = 'text/plain';
//     $mime_types['wav']  = 'audio/x-wav';
//     $mime_types['csv']  = 'text/csv';
//     $mime_types['xml']  = 'application/xml';
//     $mime_types['flv']  = 'video/x-flv';
//     $mime_types['swf']  = 'application/x-shockwave-flash';
//     $mime_types['vcf']  = 'text/x-vcard';
//     $mime_types['html'] = 'text/html';
//     $mime_types['htm']  = 'text/html';
//     $mime_types['css']  = 'text/css';
//     $mime_types['js']   = 'application/javascript';
//     $mime_types['json'] = 'application/json';
//     $mime_types['ico']  = 'image/x-icon';
//     $mime_types['otf']  = 'application/x-font-otf';
//     $mime_types['ttf']  = 'application/x-font-ttf';
//     $mime_types['woff'] = 'application/x-font-woff';
//     $mime_types['ics']  = 'text/calendar';
//     $mime_types['ppt']  = 'application/vnd.ms-powerpoint';
//     $mime_types['pot']  = 'application/vnd.ms-powerpoint';
//     $mime_types['pps']  = 'application/vnd.ms-powerpoint';
//     $mime_types['pptx'] = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
//     $mime_types['doc']  = 'application/msword';
//     $mime_types['docx'] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
//     return $mime_types;
// }
// add_filter('upload_mimes', 'mxp_custom_mime_types', 1, 1);

// 修正針對 ico 上傳的解析問題
// function mxp_getimagesize_mimes_to_exts($img_arr) {
//     $img_arr['image/x-icon']             = 'ico';
//     $img_arr['image/vnd.microsoft.icon'] = 'ico';
//     return $img_arr;
// }
// add_filter('getimagesize_mimes_to_exts', 'mxp_getimagesize_mimes_to_exts', 11, 1);

// function logger($file, $data) {
//     file_put_contents(
//         ABSPATH . "wp-content/{$file}.txt",
//         '===' . date('Y-m-d H:i:s', time()) . '===' . PHP_EOL . $data . PHP_EOL,
//         FILE_APPEND
//     );
// }
// //引用 WooCommerce 設定程式碼片段
// include dirname(__FILE__). '/wc-settings.php';
// //引用 Knockers 網站狀態追蹤程式碼片段
// include dirname(__FILE__) . '/ks_server_checker.php';