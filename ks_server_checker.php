<?php
function mxp_wp_diagnostic_info() {
    global $table_prefix;
    global $wpdb;
    $diagnostic_info = array();
    /*
     * WordPress & Server Environment
     */

    $diagnostic_info['site_url']   = site_url();
    $diagnostic_info['home_url']   = home_url();
    $diagnostic_info['WordPress']  = get_bloginfo('version', 'display');
    $diagnostic_info['Web_Server'] = !empty($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : '';
    $diagnostic_info['PHP']        = "";
    if (function_exists('phpversion')) {
        $diagnostic_info['PHP'] = phpversion();
    }
    $diagnostic_info['MySQL']            = $wpdb->db_version();
    $diagnostic_info['ext_mysqli']       = empty($wpdb->use_mysqli) ? 'no' : 'yes';
    $diagnostic_info['PHP_Memory_Limit'] = "";
    if (function_exists('ini_get')) {
        $diagnostic_info['PHP_Memory_Limit'] = ini_get('memory_limit');
    }
    $diagnostic_info['WP_MEMORY_LIMIT'] = WP_MEMORY_LIMIT;
    $diagnostic_info['Memory_Usage']    = size_format(memory_get_usage(true));

    $diagnostic_info['WP_HTTP_BLOCK_EXTERNAL'] = "";
    if (!defined('WP_HTTP_BLOCK_EXTERNAL') || !WP_HTTP_BLOCK_EXTERNAL) {
        $diagnostic_info['WP_MEMORY_LIMIT'] = "none";
    } else {
        $accessible_hosts = (defined('WP_ACCESSIBLE_HOSTS')) ? WP_ACCESSIBLE_HOSTS : '';
        if (empty($accessible_hosts)) {
            $diagnostic_info['WP_ACCESSIBLE_HOSTS'] = "all";
        } else {
            $diagnostic_info['WP_ACCESSIBLE_HOSTS'] = $accessible_hosts;
        }
    }
    $diagnostic_info['WP_Locale']              = get_locale();
    $diagnostic_info['WP_UPLOADS_BY_MY']       = get_option('uploads_use_yearmonth_folders') ? 'Enabled' : 'Disabled';
    $diagnostic_info['WP_DEBUG']               = (defined('WP_DEBUG') && WP_DEBUG) ? 'Yes' : 'No';
    $diagnostic_info['WP_DEBUG_LOG']           = (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) ? 'Yes' : 'No';
    $diagnostic_info['WP_DEBUG_DISPLAY']       = (defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY) ? 'Yes' : 'No';
    $diagnostic_info['SCRIPT_DEBUG']           = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? 'Yes' : 'No';
    $diagnostic_info['WP_MAX_UPLOAD_SIZE']     = size_format(wp_max_upload_size());
    $diagnostic_info['PHP_max_execution_time'] = "";
    if (function_exists('ini_get')) {
        $diagnostic_info['PHP_max_execution_time'] = ini_get('max_execution_time');
    }
    $diagnostic_info['WP_CRON'] = (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON) ? 'Disabled' : 'Enabled';

    $diagnostic_info['allow_url_fopen'] = "";
    $allow_url_fopen                    = ini_get('allow_url_fopen');
    if (empty($allow_url_fopen)) {
        $diagnostic_info['allow_url_fopen'] = "Disabled";
    } else {
        $diagnostic_info['allow_url_fopen'] = "Enabled";
    }

    $diagnostic_info['OpenSSL'] = "";
    if (defined('OPENSSL_VERSION_TEXT')) {
        $diagnostic_info['OpenSSL'] = OPENSSL_VERSION_TEXT;
    } else {
        $diagnostic_info['OpenSSL'] = "Disabled";
    }

    $diagnostic_info['PHP_GD'] = "";
    if (extension_loaded('gd') && function_exists('gd_info')) {
        $gd_info                   = gd_info();
        $diagnostic_info['PHP_GD'] = isset($gd_info['GD Version']) ? $gd_info['GD Version'] : 'Enabled';
    } else {
        $diagnostic_info['PHP_GD'] = 'Disabled';
    }

    $diagnostic_info['Imagick'] = "";
    if (extension_loaded('imagick') && class_exists('Imagick') && class_exists('ImagickPixel')) {
        $diagnostic_info['Imagick'] = 'Enabled';
    } else {
        $diagnostic_info['Imagick'] = 'Disabled';
    }

    /*
     * Settings
     */

    $theme_info                      = wp_get_theme();
    $diagnostic_info['Active_Theme'] = array();
    $diagnostic_info['Parent_Theme'] = array();
    if (!empty($theme_info) && is_a($theme_info, 'WP_Theme')) {
        if (file_exists($theme_info->get_stylesheet_directory())) {
            $diagnostic_info['Active_Theme']['Name']    = $theme_info->get('Name');
            $diagnostic_info['Active_Theme']['Version'] = $theme_info->get('Version');
            $diagnostic_info['Active_Theme']['Folder']  = $theme_info->get_stylesheet();
        }
        if (is_child_theme()) {
            $parent_info = $theme_info->parent();
            if (!empty($parent_info) && is_a($parent_info, 'WP_Theme')) {
                $diagnostic_info['Parent_Theme']['Name']    = $parent_info->get('Name');
                $diagnostic_info['Parent_Theme']['Version'] = $parent_info->get('Version');
                $diagnostic_info['Parent_Theme']['Folder']  = $parent_info->get_stylesheet();
            }
        } else {
            $diagnostic_info['Parent_Theme']['Name']    = "";
            $diagnostic_info['Parent_Theme']['Version'] = "";
            $diagnostic_info['Parent_Theme']['Folder']  = "";
        }
    }

    $diagnostic_info['Active_Plugins'] = array();
    $diagnostic_info['MU_Plugins']     = array();
    $active_plugins                    = (array) get_option('active_plugins', array());
    if (is_multisite()) {
        $network_active_plugins = wp_get_active_network_plugins();
        $active_plugins         = array_map(function ($path) {
            $plugin_dir = trailingslashit(WP_PLUGIN_DIR);
            $plugin     = str_replace($plugin_dir, '', $path);
            return $plugin;
        }, $network_active_plugins);
    }

    foreach ($active_plugins as $plugin) {
        $diagnostic_info['Active_Plugins'][] = mxp_get_plugin_details(WP_PLUGIN_DIR . '/' . $plugin);
    }

    $mu_plugins = wp_get_mu_plugins();
    if ($mu_plugins) {
        foreach ($mu_plugins as $mu_plugin) {
            $diagnostic_info['MU_Plugins'][] = mxp_get_plugin_details($mu_plugin);
        }
    }

    return $diagnostic_info;
}

function mxp_get_plugin_details($plugin_path, $suffix = '') {
    $plugin_data = get_plugin_data($plugin_path);
    if (empty($plugin_data['Name'])) {
        return basename($plugin_path);
    }
    return array("Name" => $plugin_data['Name'], "Version" => $plugin_data['Version'], "Author" => strip_tags($plugin_data['AuthorName']));
}

function mxp_add_cron_schedules($schedules) {
    $schedules['ks_custom_2h'] = array(
        'interval' => 60, // 2 hours in seconds.
        'display'  => "兩小時一次",
    );
    return $schedules;
}
add_filter('cron_schedules', 'mxp_add_cron_schedules');

function mxp_cronjob_register() {
    if (!wp_next_scheduled('mxp_plugin_update_cron')) {
        wp_schedule_event(time(), 'ks_custom_2h', 'mxp_plugin_update_cron');
    }
}
add_action('wp', 'mxp_cronjob_register');

function mxp_cronjob_do_action() {
    $diagnostic_info = mxp_wp_diagnostic_info();
    $req             = array(
        'domain'       => parse_url($diagnostic_info['site_url'], PHP_URL_HOST),
        'php'          => $diagnostic_info['PHP'],
        'mysql'        => $diagnostic_info['MySQL'],
        'wp'           => $diagnostic_info['WordPress'],
        'theme'        => $diagnostic_info['Active_Theme']['Name'] . "_" . $diagnostic_info['Active_Theme']['Version'],
        'parent_theme' => $diagnostic_info['Parent_Theme']['Name'] . "_" . $diagnostic_info['Parent_Theme']['Version'],
        'json'         => json_encode($diagnostic_info),
    );
    $response = wp_remote_post('https://live.mxp.tw/webhook/test.php', array(
        'method'      => 'POST',
        'timeout'     => 10,
        'redirection' => 5,
        'httpversion' => '1.1',
        'blocking'    => false,
        'headers'     => array('Content-Type' => 'application/json'),
        'body'        => wp_json_encode($req),
        'cookies'     => array(),
        'sslverify'   => false,
        'data_format' => 'body',
    )
    );

    if (is_wp_error($response)) {
        $error_message = $response->get_error_message();
        error_log($req['domain'] . "_CRONJOB_ERROR: $error_message");
    }
    error_log($req['domain'] . "_CRONJOB_DONE");
}
add_action('mxp_plugin_update_cron', 'mxp_cronjob_do_action');
