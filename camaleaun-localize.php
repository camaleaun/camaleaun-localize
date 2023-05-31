<?php
/**
 * Plugin Name: Camaleaun Localize
 * Description: REST API endpoint to retrieve current server datetime with remote IP and GPS coordinates of request.
 * Author: Camaleaun
 * Author URI: https://camaleaun.cz/en/
 * Text Domain: camaleaun-localize
 * Domain Path: /languages
 * Version: 1.0.0
 * Tested up to: 6.1
 * Requires at least: 5.7
 * Requires PHP: 5.6
 *
 * @package CamaleaunLocalize
 */

defined('ABSPATH') || exit;

if (! file_exists($composer = __DIR__.'/vendor/autoload.php')) {
    wp_die(__('Error locating autoloader. Please run <code>composer install</code>.', 'camaleaun-localize'));
    exit;
}

require $composer;

/**
 * Returns the main instance of Camaleaun\Localize.
 *
 * @since  1.0
 * @return Camaleaun\Localize
 */
function CamaleaunLocalize()
{
    return Camaleaun\Localize::instance();
}

CamaleaunLocalize();
