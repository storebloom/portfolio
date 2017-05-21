<?php
/**
 * Instantiates the Auto Quote plugin
 *
 * @package AutoQuote
 */

namespace AutoQuote;

global $auto_quote_plugin;

require_once __DIR__ . '/php/class-plugin-base.php';
require_once __DIR__ . '/php/class-plugin.php';

$auto_quote_plugin = new Plugin();

/**
 * Auto Quote Plugin Instance
 *
 * @return Plugin
 */
function get_plugin_instance() {
	global $auto_quote_plugin;
	return $auto_quote_plugin;
}
