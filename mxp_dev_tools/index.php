<?php
/**
 * Plugin Name: Mxp Dev Tools
 * Plugin URI: https://goo.gl/2gLq18
 * Description: 整理開發WordPress上常用的外掛，也是嘗試使用4.6版後改良的非同步AJAX安裝技術。 TODO: 更新功能尚待補完～
 * Version: 1.2.3
 * Author: Chun
 * Author URI: https://www.mxp.tw/contact/
 * License: MIT
 */

if (!defined('WPINC')) {
	die;
}
class MxpDevTools {
	static $VERSION = '1.2.3';
	private $themeforest_api_base_url = 'https://api.envato.com/v3';
	protected static $instance = null;
	public $plugin_slug = 'mxp_wp_dev_tools';
	private function __construct() {
		$this->init();
	}
	public function init() {
		add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'add_action_links'));
		add_action('admin_enqueue_scripts', array($this, 'load_assets'));
		add_action('admin_menu', array($this, 'create_plugin_menu'));
		add_action('admin_notices', array($this, 'init_notice'));
		add_action('wp_ajax_mxp_install_plugin', array($this, 'mxp_ajax_install_plugin'));
		add_action('wp_ajax_mxp_activate_plugin', array($this, 'mxp_ajax_activate_plugin'));
		add_action('wp_ajax_mxp_install_theme', array($this, 'mxp_ajax_install_theme'));
		add_action('wp_ajax_mxp_dismiss_notice', array($this, 'mxp_ajax_dismiss_notice'));
		wp_cache_flush();
	}
	public static function get_instance() {
		if (!isset(self::$instance) && is_super_admin()) {
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
		wp_register_script($this->plugin_slug . '-themeforest-list', plugin_dir_url(__FILE__) . 'includes/assets/js/themeforest-list/app.js', array('jquery'), false, false);
		wp_register_script($this->plugin_slug . '-dashboard', plugin_dir_url(__FILE__) . 'includes/assets/js/dashboard/app.js', array('jquery'), false, false);
		wp_localize_script($this->plugin_slug . '-dashboard', 'Mxp_AJAX_dashboard', array(
			'ajaxurl' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('mxp-ajax-nonce-for-dashboard'),
		));
		wp_enqueue_script($this->plugin_slug . '-dashboard');
	}
	public function create_plugin_menu() {
		add_menu_page('Mxp.TW 開發常用工具箱', '開發工具箱', 'administrator', $this->plugin_slug, array($this, 'main_page_cb'), 'dashicons-admin-generic');
		add_submenu_page($this->plugin_slug, 'Themeforest List', 'Themeforest List', 'administrator', 'mxp-themeforest-list', array($this, 'themeforest_page_cb'));
	}
	public function init_notice() {
		if (isset($_COOKIE[$this->plugin_slug . '-dissmis-notice-init'])) {
			return;
		}
		echo '<div id="init" class="notice notice-success"><p>工具箱中的清單會即時比對最新版本的外掛，所以讀取速度較為緩慢，還請稍候！<button data-key="init" class="mxp-dissmis-btn button action">確認</button></p></div>';
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
	public function themeforest_page_cb() {

		$this->page_wraper('Themeforest <a href="https://goo.gl/Oh9cK5" target="_blank">授權碼</a>', function () {
			echo '<form name="themeforest" method="get"><input type="hidden" name="page" value="mxp-themeforest-list"><input type="text" name="code" value="" size="40"/><input type="submit" value="送出" class="button action"/></form>';
		});

		if (isset($_GET['code']) && !empty($_GET['code'])) {
			$auth_code = sanitize_text_field($_GET['code']);
			$response = wp_remote_get($this->themeforest_api_base_url . '/market/buyer/list-purchases', array(
				'headers' => array('Authorization' => 'Bearer ' . $auth_code),
			));
			if (is_array($response) && !is_wp_error($response) && $response['response']['code'] == 200) {
				$resp = json_decode($response['body'], true);
				if ($resp && !isset($resp['error']) && $resp['count'] != '0') {
					$datas = $resp['results'];
					$themes = [];
					$plugins = [];
					$others = [];
					for ($i = 0; $i < count($datas); ++$i) {
						$dobj = wp_remote_get($this->themeforest_api_base_url . '/market/buyer/download?item_id=' . $datas[$i]['item']['id'], array(
							'headers' => array('Authorization' => 'Bearer ' . $auth_code),
						));
						if (is_array($dobj) && !is_wp_error($dobj) && $dobj['response']['code'] == 200) {
							$dlinks = json_decode($dobj['body'], true);
							if (!$dlinks) {
								echo '<br/>發生錯誤，請回報下列錯誤資訊至：im@mxp.tw<br/><br/><pre>' . esc_html(print_r($dobj, true)) . '</pre>';
								wp_die();
							}
						} else {
							echo '<br/>發生錯誤，請回報下列錯誤資訊至：im@mxp.tw<br/><br/><pre>' . esc_html(print_r($dobj, true)) . '</pre>';
							wp_die();
						}
						if (isset($datas[$i]['item']['wordpress_theme_metadata'])) {
							$tid = $datas[$i]['item']['id'];
							$datas[$i]['item']['wordpress_theme_metadata']['id'] = esc_attr($tid);
							$datas[$i]['item']['wordpress_theme_metadata']['dlink'] = esc_url($dlinks['wordpress_theme']);
							$themes[] = $datas[$i]['item']['wordpress_theme_metadata'];
						} else if (isset($datas[$i]['item']['wordpress_plugin_metadata'])) {
							$tid = $datas[$i]['item']['id'];
							$datas[$i]['item']['wordpress_plugin_metadata']['id'] = esc_attr($tid);
							$datas[$i]['item']['wordpress_plugin_metadata']['dlink'] = esc_url($dlinks['wordpress_plugin']);
							$plugins[] = $datas[$i]['item']['wordpress_plugin_metadata'];

						} else {
							$datas[$i]['item']['dlink'] = esc_url($dlinks['download_url']);
							$others[] = $datas[$i]['item'];
						}
					} //end for-loop
					echo '<h1>主題</h1><br/><table style="text-align:center;"><tr><th>操作</th><th>名稱</th><th>版本</th></tr>';
					for ($i = 0; $i < count($themes); ++$i) {
						echo "<tr><td><button class='install_theme' data-dlink='{$themes[$i]['dlink']}' data-id='{$themes[$i]['id']}'>下載＆安裝</button><button style='display:none;' class='activate_theme' data-id='{$themes[$i]['id']}'>前往主題頁啟動</button></td><td>" . esc_html($themes[$i]['theme_name']) . "</td><td>" . esc_html($themes[$i]['version']) . "</td>";
					}
					echo '</table>';
					echo '<h1>外掛</h1><br/><table style="text-align:center;"><tr><th>操作</th><th>名稱</th><th>版本</th></tr>';
					for ($i = 0; $i < count($plugins); ++$i) {
						$pname = esc_attr($plugins[$i]['plugin_name']);
						echo "<tr><td><button class='install_plugin' data-name='{$pname}' data-dlink='{$plugins[$i]['dlink']}' data-id='{$plugins[$i]['id']}'>下載＆安裝</button><button style='display:none;' class='activate_plugin' data-name='{$pname}' data-dlink='{$plugins[$i]['dlink']}' data-id='{$plugins[$i]['id']}'>啟動</button></td><td>" . esc_html($plugins[$i]['plugin_name']) . "</td><td>" . esc_html($plugins[$i]['version']) . "</td>";
					}
					echo '</table>';
					echo '<h1>其他（未分類）</h1><br/><table style="text-align:center;"><tr><th>操作</th><th>名稱</th><th>版本</th></tr>';
					for ($i = 0; $i < count($others); ++$i) {
						$oname = esc_html($others[$i]['name']);
						echo "<tr><td><button class='install_other' data-dlink='{$others[$i]['dlink']}' data-id='{$others[$i]['id']}'>下載手動安裝</button></td><td>{$oname}</td><td>NONE</td>";
					}
					echo '</table>';
					wp_localize_script($this->plugin_slug . '-themeforest-list', 'Mxp_AJAX', array(
						'ajaxurl' => admin_url('admin-ajax.php'),
						'themesurl' => admin_url('themes.php'),
						'nonce' => wp_create_nonce('mxp-ajax-nonce-for-themeforest-list'),
					));
					wp_enqueue_script($this->plugin_slug . '-themeforest-list');
				} else {
					echo '<br/>若非無購買項目，請回報下列錯誤資訊至：im@mxp.tw<br/><br/><pre>' . print_r($response, true) . '</pre>';
					wp_die();
				}
			} else {
				echo '<br/>若非授權碼錯誤或無購買項目，請回報下列錯誤資訊至：im@mxp.tw<br/><br/><pre>' . print_r($response, true) . '</pre>';
				wp_die();
			}
		} else {
			echo '<p>請輸入授權碼！</p>';
		}
	}
	public function mxp_ajax_install_plugin() {
		$nonce = sanitize_text_field(isset($_POST['nonce']) == true ? $_POST['nonce'] : "");
		if (!wp_verify_nonce($nonce, 'mxp-ajax-nonce-for-plugin-list') && !wp_verify_nonce($nonce, 'mxp-ajax-nonce-for-themeforest-list')) {
			wp_send_json_error(array('status' => false, 'data' => array('msg' => '錯誤的請求來源')));
		}
		$activated = sanitize_text_field(isset($_POST['activated']) == true ? $_POST['activated'] : "");
		$file = sanitize_text_field(isset($_POST['file']) == true ? $_POST['file'] : "");
		$dlink = isset($_POST['dlink']) == true ? $_POST['dlink'] : "";
		$slug = sanitize_text_field(isset($_POST['slug']) == true ? $_POST['slug'] : "");
		$version = sanitize_text_field(isset($_POST['version']) == true ? $_POST['version'] : "");
		$name = sanitize_text_field(isset($_POST['name']) == true ? $_POST['name'] : "");
		if ($activated == "" || $dlink == "" || $slug == "" || $version == "" || $name == "") {
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
		$nonce = sanitize_text_field(isset($_POST['nonce']) == true ? $_POST['nonce'] : "");
		if (!wp_verify_nonce($nonce, 'mxp-ajax-nonce-for-plugin-list') && !wp_verify_nonce($nonce, 'mxp-ajax-nonce-for-themeforest-list')) {
			wp_send_json_error(array('activated' => false, 'data' => array('msg' => '錯誤的請求')));
		}
		$name = sanitize_text_field(isset($_POST['name']) == true ? $_POST['name'] : "");
		$file = isset($_POST['file']) ? sanitize_text_field(isset($_POST['file']) == true ? $_POST['file'] : "") : $this->get_plugin_file($name);
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
	public function mxp_ajax_install_theme() {
		$nonce = sanitize_text_field(isset($_POST['nonce']) == true ? $_POST['nonce'] : "");
		$dlink = isset($_POST['dlink']) == true ? $_POST['dlink'] : "";
		if (!wp_verify_nonce($nonce, 'mxp-ajax-nonce-for-themeforest-list') || $dlink == "") {
			wp_send_json_error(array('data' => array('msg' => '錯誤的請求')));
		}
		include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		//code reference from wp-admin/includes/ajax-actions.php
		$skin = new WP_Ajax_Upgrader_Skin();
		$upgrader = new Theme_Upgrader($skin);
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
		wp_send_json_success($status);
	}
	public function mxp_ajax_dismiss_notice() {
		$nonce = sanitize_text_field(isset($_POST['nonce']) == true ? $_POST['nonce'] : "");
		$key = sanitize_text_field(isset($_POST['key']) == true ? $_POST['key'] : "");
		if (!wp_verify_nonce($nonce, 'mxp-ajax-nonce-for-dashboard') || $key == "") {
			wp_send_json_error(array('data' => array('msg' => '錯誤的請求')));
		}
		setcookie($this->plugin_slug . '-dissmis-notice-' . $key, '1');
		wp_send_json_success('done');
	}
	private function get_plugin_file($plugin_name) {
		if (!function_exists('get_plugins')) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$plugins = get_plugins();
		foreach ($plugins as $plugin_file => $plugin_info) {
			if ($plugin_info['Name'] == $plugin_name) {
				return $plugin_file;
			}
		}
		return null;
	}

}

add_action('plugins_loaded', array('MxpDevTools', 'get_instance'));
