<?php
/**
 * OD_User_Search
 */
class OD_Map {

	/**
	 * Ajax actions nonce.
	 *
	 * @var string
	 */
	private $ajax_nonce = 'od-appointment-ajax';

	/**
	 * OD_Map constructor.
	 */
	public function __construct() {
		add_action( 'get_the_address', array( $this, 'get_the_address' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_map_assets') );
		add_action( 'wp_ajax_search_map', array( $this, 'search_map' ) );
		add_action( 'wp_ajax_nopriv_search_map', array( $this, 'search_map' ) );
		add_shortcode( 'od-map-search', array( $this, 'map_search_display' ) );
		add_shortcode( 'od-map-display', array( $this, 'add_map_canvas') );
	}

	/**
	 * Ajax callback function for sending search input to map functions.
	 *
	 * @return string
	 */
	public function search_map() {
		// Security check.
		check_ajax_referer( $this->ajax_nonce, 'nonce' );

		$search = isset( $_POST['search_val'] ) ? sanitize_text_field( wp_unslash( $_POST['search_val'] ) ) : '';

		if ( '' !== $search ) {
			$business_results = is_array( $this->get_current_addresses( $search ) ) ? $this->get_current_addresses( $search ) : '';

			if ( '' !== $business_results ) {
				$results = $this->define_global_var( $business_results );
			}

			echo $results;

			wp_die();
		}

		wp_send_json_error( 'no results' );
	}

	/**
	 * The shortcode callback function for placing the search input.
	 * Also enqueues the scripts and nonce needed.
	 *
	 * @return string
	 */
	public function map_search_display() {
		wp_enqueue_script( 'od-maps' );
		wp_enqueue_script( 'od-custom-maps' );

		wp_localize_script( 'od-maps', 'ODMap', array(
				'MapNonce' => wp_create_nonce( $this->ajax_nonce ),
			)
		);

		return '<input id="search_map_val" type="text" placeholder="type to search"/>';
	}

	/**
	 * The shortcode callback function for placing the map canvas.
	 * Also enqueues the map's style sheet.
	 *
	 * @return string
	 */
	public function add_map_canvas() {
		wp_enqueue_style( 'od-maps' );

		return '<div id="map_wrapper"><div id="close_map">X</div><div id="map-canvas"></div></div>';
	}

	/**
	 * An unused function for displaying the current
	 * user's location based on their IP address.
	 */
	public function current_user_location_map() {
		if ( $_SERVER['REMOTE_ADDR'] !== '127.0.0.1' ) {

			// Load the class.
			$ipLite = new ip2location_lite;
			$ipLite->setKey( '37a3692ac27db659e807dc8ca72c062979edb44b366ef001342b521a8bf6c50c' );

			// Get errors and locations.
			$locations = $ipLite->getCity( $_SERVER['REMOTE_ADDR'] );
			$errors    = $ipLite->getError();

			if ( $locations['statusCode'] === 'OK' ) {
				$address = $locations['cityName'] . ' ' . $locations['regionName'] . ', ' . $locations['countryCode'] . ' ' . $locations['zipCode'];
				self::google_map_enqueue();
				self::geocode_address( $address );
			}
		}
	}

	/**
	 * Used for registering scripts and styles to be enqueued.
	 */
	public function register_map_assets() {
		wp_register_script(
			'od-maps',
			'https://maps.googleapis.com/maps/api/js?key=AIzaSyD27PJsgKc4b4Jkm5swmUmeMOpbT8HcXtc&v=3.exp&libraries=places&signed_in=true',
			array(), null, true );

		wp_register_script(
			'od-custom-maps',
			'/wp-content/plugins/od-map/js/map.js',
			array( 'od-maps' ), null, true );

		wp_register_style(
			'od-maps',
			'/wp-content/plugins/od-map/css/custom-map.css'
		);
	}

	/**
	 * A helper function for reformatting an address using the google api.
	 * Returns an array of information for each business in the supplied array.
	 * This array includes the longitude, latitude, reformatted address and
	 * business user ID.
	 * Returns false if array is empty or not an array.
	 *
	 * @param $business_array
	 *
	 * @return array|bool
	 */
	private function geocode_address( $business_array ) {
		if ( is_array( $business_array ) && array() !== $business_array ) {
			foreach ( $business_array as $business ) {
				$geocode_info = json_decode( file_get_contents( 'https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyD27PJsgKc4b4Jkm5swmUmeMOpbT8HcXtc&address=' . urlencode( $business[0] ) ) );

				if ( isset( $geocode_info->results[0] ) ) {
					$coord[] = array(
						'lat'     => $geocode_info->results[0]->geometry->location->lat,
						'lng'     => $geocode_info->results[0]->geometry->location->lng,
						'address' => $geocode_info->results[0]->formatted_address,
						'info'    => $business[1],
					);
				}
			}

			return $coord;
		}

		return false;
	}

	/**
	 * This function gets all of the business that match the input value.
	 *
	 * @param string $key The input value from the search.
	 *
	 * @return array|bool|string
	 */
	private function get_current_addresses( $key ) {
		if( ! isset( $key ) || '' === $key ){
			return false;
		}

		$current_businesses = get_users( array( 'role' => 'business' ) );
		$business_array = '';
		$returned_business_array = array();

		if ( is_array( $current_businesses ) && count( $current_businesses ) > 0 ) {
			foreach ( $current_businesses as $business ) {
				if( false !== strripos( $business->display_name, $key ) ) {
					$business_address = '' !== get_usermeta( $business->ID, 'address', true ) ? get_usermeta( $business->ID, 'address', true ) : '';
					if ( '' !== $business_address ) {
						$returned_business_array[] = array( $business_address, $business->ID );
					}
				}
			}

			$business_array = $this->geocode_address( (array) $returned_business_array );
		} else {
			$business_array = $this->geocode_address( $key );
		}

		return $business_array;
	}

	/**
	 * Used for building the pop up card used on the google map pins.
	 *
	 * @param integer $id The business user id.
	 * @param string $address The business address for directions.
	 *
	 * @return string
	 */
	private function get_info_card( $id, $address = "" ) {
		$user_info = get_userdata( (int) $id );
		$user_avatar = intVal( get_user_meta( $user_info->ID, 'od_user_avatar', true ) );
		$open_close = ! empty( get_user_meta( $user_info->ID, 'decstatus', true ) ) ? get_user_meta( $user_info->ID, 'decstatus', true ) : "Closed";
		$pro_url   = get_the_guid( $user_avatar );

		$info_card = '<div class=\"map_name\">' . $user_info->display_name . '<\/div>';
		$info_card .= '<div class=\"map_avatar\"><img width=\"60px\" src=\"' . $pro_url . '\" ><\/div>';
		$info_card .= '<div class=\"map_open\">We Are ' . $open_close . '<\/div>';
		$info_card .= '<div class=\"map_address\"><a target=\"_blank\" href=\"https://www.google.com/maps/dir/' . $address . '\">' . $address . '<\/a><\/div>';
		$info_card .= '<div class=\"map_link\"><a href=\"/businesses/' . $user_info->user_login . '/\">view profile<\/a><\/div>';

		return $info_card;
	}

	/**
	 * This function builds the JSON string used in the google script to load
	 * the search results on the map.
	 *
	 * @param array $single_coor The google formatted information for searched businesses.
	 *
	 * @return string
	 */
	private function define_global_var( $single_coor ) {
		$script = '{ "search_val": { "lng" : "'.$single_coor[0]['lng'].'", "lat" : "'.$single_coor[0]['lat'].'"}, "data": { "markers_available": [';
		$comma = ',';
		$i = 1;
		foreach ( $single_coor as $business_info ) {
			if( $i === count( $single_coor ) ) {
				$comma = '';
			}
			$info   = $this->get_info_card( $business_info['info'], $business_info['address'] );
			$script .= '{ "lat":"' . $business_info['lat'] . '","long":"' . $business_info['lng'] . '", "info":"' . $info . '","business_type":"" }'.$comma;

			$i ++;
		}

		$script .= ' ]}}';

		return $script;
	}
}

$od_map = new OD_Map();