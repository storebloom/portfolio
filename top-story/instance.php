<?php
/**
 * Instantiates the Top Story plugin
 *
 * @package TopStory
 */

namespace TopStory;

global $top_story_plugin;

require_once __DIR__ . '/php/class-plugin-base.php';
require_once __DIR__ . '/php/class-plugin.php';

$top_story_plugin = new Plugin();

/**
 * Top Story Plugin Instance
 *
 * @return Plugin
 */
function get_plugin_instance() {
	global $top_story_plugin;
	return $top_story_plugin;
}
