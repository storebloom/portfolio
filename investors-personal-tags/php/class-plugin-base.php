<?php
/**
 * Class Plugin_Base
 *
 * @package InvestorsPersonalTags
 */

namespace InvestorsPersonalTags;

/**
 * Class Plugin_Base
 *
 * @package InvestorsPersonalTags
 */
abstract class Plugin_Base {

	/**
	 * Plugin slug.
	 *
	 * @var string
	 */
	public $slug;

	/**
	 * Plugin directory path.
	 *
	 * @var string
	 */
	public $dir_path;

	/**
	 * Plugin directory URL.
	 *
	 * @var string
	 */
	public $dir_url;

	/**
	 * Plugin assets prefix.
	 *
	 * @var string Lowercased dashed prefix.
	 */
	public $assets_prefix;

	/**
	 * Plugin meta prefix.
	 *
	 * @var string Lowercased underscored prefix.
	 */
	public $meta_prefix;

	/**
	 * Directory in plugin containing autoloaded classes.
	 *
	 * @var string
	 */
	protected $autoload_class_dir = 'php';

	/**
	 * Autoload matches cache.
	 *
	 * @var array
	 */
	protected $autoload_matches_cache = array();

	/**
	 * Required instead of a static variable inside the add_doc_hooks method
	 * for the sake of unit testing.
	 *
	 * @var array
	 */
	protected $_called_doc_hooks = array();

	/**
	 * Plugin_Base constructor.
	 */
	public function __construct() {
		$location = $this->locate_plugin();
		$this->slug = $location['dir_basename'];
		$this->dir_path = $location['dir_path'];
		$this->dir_url = $location['dir_url'];
		$this->assets_prefix = strtolower( preg_replace( '/\B([A-Z])/', '-$1', __NAMESPACE__ ) );
		$this->meta_prefix = strtolower( preg_replace( '/\B([A-Z])/', '_$1', __NAMESPACE__ ) );

		spl_autoload_register( array( $this, 'autoload' ) );
		$this->add_doc_hooks();
	}

	/**
	 * Plugin_Base destructor.
	 */
	function __destruct() {
		$this->remove_doc_hooks();
	}

	/**
	 * Get reflection object for this class.
	 *
	 * @return \ReflectionObject
	 */
	public function get_object_reflection() {
		static $reflection;

		if ( empty( $reflection ) ) {
			$reflection = new \ReflectionObject( $this );
		}

		return $reflection;
	}

	/**
	 * Autoload for classes that are in the same namespace as $this.
	 *
	 * @param string $class Class name.
	 * @return void
	 */
	public function autoload( $class ) {
		if ( ! isset( $this->autoload_matches_cache[ $class ] ) ) {
			if ( ! preg_match( '/^(?P<namespace>.+)\\\\(?P<class>[^\\\\]+)$/', $class, $matches ) ) {
				$matches = false;
			}

			$this->autoload_matches_cache[ $class ] = $matches;
		} else {
			$matches = $this->autoload_matches_cache[ $class ];
		}

		if ( empty( $matches ) ) {
			return;
		}

		if ( $this->get_object_reflection()->getNamespaceName() !== $matches['namespace'] ) {
			return;
		}

		$class_name = $matches['class'];
		$class_path = \trailingslashit( $this->dir_path );

		if ( $this->autoload_class_dir ) {
			$class_path .= \trailingslashit( $this->autoload_class_dir );
		}

		$class_path .= sprintf( 'class-%s.php', strtolower( str_replace( '_', '-', $class_name ) ) );

		if ( is_readable( $class_path ) ) {
			require_once $class_path;
		}
	}

	/**
	 * Version of plugin_dir_url() which works for plugins installed in the plugins directory,
	 * and for plugins bundled with themes.
	 *
	 * @throws \Exception If the plugin is not located in the expected location.
	 * @return array
	 */
	public function locate_plugin() {
		$file_name = $this->get_object_reflection()->getFileName();

		if ( '/' !== \DIRECTORY_SEPARATOR ) {
			$file_name = str_replace( \DIRECTORY_SEPARATOR, '/', $file_name ); // Windows compat.
		}

		$plugin_dir = preg_replace( '#(.*plugins[^/]*/[^/]+)(/.*)?#', '$1', $file_name, 1, $count );

		if ( 0 === $count ) {
			throw new \Exception( "Class not located within a directory tree containing 'plugins': $file_name" );
		}

		// Make sure that we can reliably get the relative path inside of the content directory.
		$plugin_path = $this->relative_path( $plugin_dir, 'wp-content', \DIRECTORY_SEPARATOR );

		if ( '' === $plugin_path ) {
			throw new \Exception( 'Plugin dir is not inside of the `wp-content` directory' );
		}

		$dir_url = content_url( trailingslashit( $plugin_path ) );
		$dir_path = $plugin_dir;
		$dir_basename = basename( $plugin_dir );

		return compact( 'dir_url', 'dir_path', 'dir_basename' );
	}

	/**
	 * Relative Path
	 *
	 * Returns a relative path from a specified starting position of a full path
	 *
	 * @param string $path The full path to start with.
	 * @param string $start The directory after which to start creating the relative path.
	 * @param string $sep The directory seperator.
	 *
	 * @return string
	 */
	public function relative_path( $path, $start, $sep ) {
		$path = explode( $sep, untrailingslashit( $path ) );

		if ( count( $path ) > 0 ) {
			foreach ( $path as $p ) {
				array_shift( $path );

				if ( $p === $start ) {
					break;
				}
			}
		}

		return implode( $sep, $path );
	}

	/**
	 * Hooks a function on to a specific filter.
	 *
	 * @param string $name     The hook name.
	 * @param array  $callback The class object and method.
	 * @param array  $args     An array with priority and arg_count.
	 *
	 * @return mixed
	 */
	public function add_filter( $name, $callback, $args = array() ) {
		// Merge defaults.
		$args = array_merge( array(
			'priority' => 10,
			'arg_count' => PHP_INT_MAX,
		), $args );

		return $this->_add_hook( 'filter', $name, $callback, $args );
	}

	/**
	 * Hooks a function on to a specific action.
	 *
	 * @param string $name     The hook name.
	 * @param array  $callback The class object and method.
	 * @param array  $args     An array with priority and arg_count.
	 *
	 * @return mixed
	 */
	public function add_action( $name, $callback, $args = array() ) {
		// Merge defaults.
		$args = array_merge( array(
			'priority' => 10,
			'arg_count' => PHP_INT_MAX,
		), $args );

		return $this->_add_hook( 'action', $name, $callback, $args );
	}

	/**
	 * Hooks a function on to a specific action/filter.
	 *
	 * @param string $type     The hook type. Options are action/filter.
	 * @param string $name     The hook name.
	 * @param array  $callback The class object and method.
	 * @param array  $args     An array with priority and arg_count.
	 *
	 * @return mixed
	 */
	protected function _add_hook( $type, $name, $callback, $args = array() ) {
		$priority = isset( $args['priority'] ) ? $args['priority'] : 10;
		$arg_count = isset( $args['arg_count'] ) ? $args['arg_count'] : PHP_INT_MAX;
		$fn = sprintf( '\add_%s', $type );
		$retval = \call_user_func( $fn, $name, $callback, $priority, $arg_count );

		return $retval;
	}

	/**
	 * Add actions/filters from the methods of a class based on DocBlocks.
	 *
	 * @param object $object The class object.
	 */
	public function add_doc_hooks( $object = null ) {
		if ( is_null( $object ) ) {
			$object = $this;
		}

		$class_name = get_class( $object );

		if ( isset( $this->_called_doc_hooks[ $class_name ] ) ) {
			$notice = sprintf( 'The add_doc_hooks method was already called on %s. Note that the Plugin_Base constructor automatically calls this method.', $class_name );

			// @codingStandardsIgnoreStart
			trigger_error( esc_html( $notice ), \E_USER_NOTICE );
			// @codingStandardsIgnoreEnd

			return;
		}

		$this->_called_doc_hooks[ $class_name ] = true;

		$reflector = new \ReflectionObject( $object );

		foreach ( $reflector->getMethods() as $method ) {
			$doc = $method->getDocComment();
			$arg_count = $method->getNumberOfParameters();

			if ( preg_match_all( '#\* @(?P<type>filter|action)\s+(?P<name>[a-z0-9\-\._]+)(?:,\s+(?P<priority>\d+))?#', $doc, $matches, PREG_SET_ORDER ) ) {
				foreach ( $matches as $match ) {
					$type = $match['type'];
					$name = $match['name'];
					$priority = empty( $match['priority'] ) ? 10 : intval( $match['priority'] );
					$callback = array( $object, $method->getName() );
					call_user_func( array( $this, "add_{$type}" ), $name, $callback, compact( 'priority', 'arg_count' ) );
				}
			}
		}
	}

	/**
	 * Removes the added DocBlock hooks.
	 *
	 * @param object $object The class object.
	 */
	public function remove_doc_hooks( $object = null ) {
		if ( is_null( $object ) ) {
			$object = $this;
		}

		$class_name = get_class( $object );
		$reflector = new \ReflectionObject( $object );

		foreach ( $reflector->getMethods() as $method ) {
			$doc = $method->getDocComment();

			if ( preg_match_all( '#\* @(?P<type>filter|action)\s+(?P<name>[a-z0-9\-\._]+)(?:,\s+(?P<priority>\d+))?#', $doc, $matches, PREG_SET_ORDER ) ) {
				foreach ( $matches as $match ) {
					$type = $match['type'];
					$name = $match['name'];
					$priority = empty( $match['priority'] ) ? 10 : intval( $match['priority'] );
					$callback = array( $object, $method->getName() );
					call_user_func( "remove_{$type}", $name, $callback, $priority );
				}
			}
		}

		unset( $this->_called_doc_hooks[ $class_name ] );
	}
}
