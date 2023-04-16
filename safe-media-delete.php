<?php

/**
 *
 * @link              https://https://github.com/davesuy
 * @since             1.0.0
 * @package           Safe_Media_Delete
 *
 * @wordpress-plugin
 * Plugin Name:       Safe Media Delete
 * Plugin URI:        https://https://github.com/davesuy
 * Description:       Safe Media Delete
 * Version:           1.0.0
 * Author:            Dave Ramirez
 * Author URI:        https://https://github.com/davesuy
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       safe-media-delete
 * Domain Path:       /languages
 */


if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'SAFE_MEDIA_DELETE_VERSION', '1.0.0' );

function activate_safe_media_delete() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-safe-media-delete-activator.php';
	Safe_Media_Delete_Activator::activate();
}


function deactivate_safe_media_delete() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-safe-media-delete-deactivator.php';
	Safe_Media_Delete_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_safe_media_delete' );
register_deactivation_hook( __FILE__, 'deactivate_safe_media_delete' );


require plugin_dir_path( __FILE__ ) . 'includes/class-safe-media-delete.php';


function run_safe_media_delete() {

	$plugin = new Safe_Media_Delete();
	$plugin->run();

}
run_safe_media_delete();
