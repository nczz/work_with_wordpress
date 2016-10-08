<?php
/**
 * Plugin Name: Mxp Dev Tools
 * Plugin URI: https://www.mxp.tw
 * Description: 整理開發WordPress上常用的外掛，也是嘗試使用4.6版後改良的非同步AJAX安裝技術。 TODO: 更新功能尚待補完～
 * Version: 1.1.0
 * Author: Chun
 * Author URI: https://wwww.mxp.tw
 * License: MIT
 */

if (!defined('WPINC')) {
	die;
}
class MxpDevTools {
	static $VERSION = '1.1.0';
	private $api_url = '';
	protected static $instance = null;
	public $plugin_slug = 'mxp_wp_dev_tools';
	private function __construct() {
		$this->init();
	}
	public function init() {
		add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'add_action_links'));
		add_action('admin_enqueue_scripts', array($this, 'load_assets'));
		add_action('admin_menu', array($this, 'create_plugin_menu'));
		add_action('wp_ajax_mxp_install_plugin', array($this, 'mxp_ajax_install_plugin'));
		add_action('wp_ajax_mxp_activate_plugin', array($this, 'mxp_ajax_activate_plugin'));
		wp_cache_flush();
	}
	public static function get_instance() {
		if (null == self::$instance && is_super_admin()) {
			self::$instance = new self;
		}
		return self::$instance;
	}
	public function add_action_links($links) {
		$mxp_links = array(
			'<a href="' . admin_url('admin.php?page=mxp_wp_dev_tools') . '"><font color=red>點此設定</font></a>',
		);
		return array_merge($links, $mxp_links);
	}
	public function load_assets() {
		wp_register_script($this->plugin_slug . '-plugins-list', plugin_dir_url(__FILE__) . 'includes/assets/js/plugins-list/app.js', array('jquery'), false, false);
	}
	public function create_plugin_menu() {
		add_menu_page('Mxp.TW 開發常用工具箱', '開發工具箱', 'administrator', $this->plugin_slug, array($this, 'main_page_cb'), 'dashicons-admin-generic');
		//add_submenu_page($this->plugin_slug, 'Plugins List', 'Plugins List', 'administrator', 'mxp-plugin-bulk-installation', array($this, 'plugins_page_cb'));
	}
	public function page_wraper($title, $cb) {
		echo '<div class="wrap" id="mxp"><h1>' . $title . '</h1>';
		call_user_func($cb);
		echo '</div>';
	}
	public function main_page_cb() {
		$this->page_wraper('開發常用外掛', function () {
			require_once 'includes/class_plugins_list_table.php';
			$plugins_list = new Mxp_Plugins_List_Table();
			$plugins_list->prepare_items();
			$plugins_list->display();
			wp_localize_script($this->plugin_slug . '-plugins-list', 'Mxp_AJAX', array(
				'ajaxurl' => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce('mxp-ajax-nonce-for-plugin-list'),
			));

			wp_enqueue_script($this->plugin_slug . '-plugins-list');
		});
	}
	// public function plugins_page_cb() {
	// }
	public function mxp_ajax_install_plugin() {
		$nonce = $_POST['nonce'];
		if (!wp_verify_nonce($nonce, 'mxp-ajax-nonce-for-plugin-list')) {
			wp_send_json_error(array('status' => false, 'data' => array('msg' => '錯誤的請求來源')));
		}
		$activated = $_POST['activated'];
		$file = $_POST['file'];
		$dlink = $_POST['dlink'];
		$slug = $_POST['slug'];
		$version = $_POST['version'];
		$name = $_POST['name'];
		if (!isset($activated) || !isset($dlink) || !isset($slug) || !isset($version) || !isset($name)) {
			wp_send_json_error(array('status' => false, 'data' => array('msg' => '錯誤的請求資料')));
		}
		if ($activated === 'true' || $file != 'false') {
			wp_send_json_error(array('status' => false, 'data' => array('msg' => '已經安裝')));
		}
		include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		//code reference from wp-admin/includes/ajax-actions.php
		$skin = new WP_Ajax_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader($skin);
		$result = $upgrader->install($dlink);
		if (defined('WP_DEBUG') && WP_DEBUG) {
			$status['debug'] = $skin->get_upgrade_messages();
		}
		if (is_wp_error($result)) {
			$status['errorCode'] = $result->get_error_code();
			$status['errorMessage'] = $result->get_error_message();
			wp_send_json_error($status);
		} elseif (is_wp_error($skin->result)) {
			$status['errorCode'] = $skin->result->get_error_code();
			$status['errorMessage'] = $skin->result->get_error_message();
			wp_send_json_error($status);
		} elseif ($skin->get_errors()->get_error_code()) {
			$status['errorMessage'] = $skin->get_error_messages();
			wp_send_json_error($status);
		} elseif (is_null($result)) {
			global $wp_filesystem;
			$status['errorCode'] = 'unable_to_connect_to_filesystem';
			$status['errorMessage'] = __('Unable to connect to the filesystem. Please confirm your credentials.');
			// Pass through the error from WP_Filesystem if one was raised.
			if ($wp_filesystem instanceof WP_Filesystem_Base && is_wp_error($wp_filesystem->errors) && $wp_filesystem->errors->get_error_code()) {
				$status['errorMessage'] = esc_html($wp_filesystem->errors->get_error_message());
			}
			wp_send_json_error($status);
		}
		if (!function_exists('install_plugin_install_status')) {
			require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		}
		$pluginInfo = install_plugin_install_status(array('name' => $name, 'slug' => $slug, 'version' => $version));
		$status['info'] = json_encode($pluginInfo);
		wp_send_json_success($status);
	}
	public function mxp_ajax_activate_plugin() {
		$nonce = $_POST['nonce'];
		if (!wp_verify_nonce($nonce, 'mxp-ajax-nonce-for-plugin-list')) {
			wp_send_json_error(array('activated' => false, 'data' => array('msg' => '錯誤的請求')));
		}
		$file = $_POST['file'];
		if (!isset($file)) {
			wp_send_json_error(array('activated' => false, 'data' => array('msg' => '找不到啟動來源')));
		}
		activate_plugins($file);
		if (is_plugin_active($file)) {
			wp_send_json_success(array('activated' => true));
		} else {
			wp_send_json_error(array('activated' => false));
		}
	}

}

add_action('plugins_loaded', array('MxpDevTools', 'get_instance'));