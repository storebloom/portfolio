<?php
/**
 * Plugin Name: Rpa Content Format
 * Description: Provides custom shortcodes for formatting titles and images in articles.
 * Version: 0.1.0
 * Author: Scott Adrian
 * Text Domain: rpa-content-format
 * Domain Path: /languages
 *
 * @package RpaContentFormat
 */

namespace RpaContentFormat;

require_once __DIR__ . '/php/class-plugin-base.php';
require_once __DIR__ . '/php/class-plugin.php';

$rpa_content_format_plugin = new Plugin();
