<?php

/**
 * Plugin Name: Cinemanager Core
 * Description:
 * Author: Roman Avery
 * Version: 1.0
 * Requires at least: 6.0
 * Requires PHP: 7.0
 * Network: true
 */
 
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cinemgr-activator.php
 */
function cinemgr_activate() { 
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-cinemgr-activator.php';
    Cinemgr_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-cinemgr-deactivator.php
 */
function cinemgr_deactivate() { 
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-cinemgr-deactivator.php';
    Cinemgr_Deactivator::deactivate();
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-cinemgr.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_cinemgr() {

	$plugin = new Cinemgr();
	$plugin->run();

}
run_cinemgr();