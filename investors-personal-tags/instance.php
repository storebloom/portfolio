<?php
/**
 * Instantiates the Investors Personal Tags plugin
 *
 * @package InvestorsPersonalTags
 */

namespace InvestorsPersonalTags;

global $investors_personal_tags_plugin;

require_once __DIR__ . '/php/class-plugin-base.php';
require_once __DIR__ . '/php/class-plugin.php';

$investors_personal_tags_plugin = new Plugin();

/**
 * Investors Personal Tags Plugin Instance
 *
 * @return Plugin
 */
function get_plugin_instance() {
	global $investors_personal_tags_plugin;
	return $investors_personal_tags_plugin;
}
