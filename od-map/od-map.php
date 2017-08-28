<?php
/**
 * Plugin Name: OD Map
 * Description: This adds the map for searching current businesses.
 * Version: 0.1.0
 * Author: Scott Adrian
 *
 * @copyright 2016
 * @author Scott Adrian
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'includes/class-od-map.php');