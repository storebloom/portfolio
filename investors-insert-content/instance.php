<?php
/**
 * Instantiates the Investors Insert Content plugin
 *
 * @package InvestorsInsertContent
 */

namespace InvestorsInsertContent;

global $investors_insert_content_plugin;

require_once __DIR__ . '/php/class-plugin-base.php';
require_once __DIR__ . '/php/class-plugin.php';

$investors_insert_content_plugin = new Plugin();

/**
 * Investors Insert Content Plugin Instance
 *
 * @return Plugin
 */
function get_plugin_instance() {
	global $investors_insert_content_plugin;
	return $investors_insert_content_plugin;
}
