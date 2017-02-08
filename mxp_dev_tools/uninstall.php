<?php
/*
 * plugin should create a file named ‘uninstall.php’ in the base plugin folder. This file will be called, if it exists,
 * during the uninstall process bypassing the uninstall hook.
 * ref: https://developer.wordpress.org/reference/functions/register_uninstall_hook/
 */
if (!defined('WP_UNINSTALL_PLUGIN')) {
	die;
}
