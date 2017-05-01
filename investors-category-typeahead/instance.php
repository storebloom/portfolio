<?php
/**
 * Instantiates the Investors Category Typeahead plugin
 *
 * @package InvestorsCategoryTypeahead
 */

namespace InvestorsCategoryTypeahead;

global $investors_category_typeahead_plugin;

require_once __DIR__ . '/php/class-plugin-base.php';
require_once __DIR__ . '/php/class-plugin.php';

$investors_category_typeahead_plugin = new Plugin();

/**
 * Investors Category Typeahead Plugin Instance
 *
 * @return Plugin
 */
function get_plugin_instance() {
	global $investors_category_typeahead_plugin;
	return $investors_category_typeahead_plugin;
}
