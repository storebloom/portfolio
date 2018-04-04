<?php
/**
 * Instantiates the Prodigy Commerce plugin
 *
 * @package ProdigyCommerce
 */

namespace ProdigyCommerce;

global $prodigy_commerce_plugin;

require_once __DIR__ . '/php/class-plugin-base.php';
require_once __DIR__ . '/php/class-plugin.php';

$prodigy_commerce_plugin = new Plugin();

/**
 * Prodigy Commerce Plugin Instance
 *
 * @return Plugin
 */
function get_plugin_instance() {
	global $prodigy_commerce_plugin;
	return $prodigy_commerce_plugin;
}
