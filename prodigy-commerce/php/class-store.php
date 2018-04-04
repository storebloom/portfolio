<?php
/**
 * Store class to house all of the front end template logic.
 *
 * @package ProdigyCommerce
 */

namespace ProdigyCommerce;

/**
 * Store Class.
 *
 * @package ProdigyCommerce
 */
class Store {

	/**
	 * Plugin instance.
	 *
	 * @var object
	 */
	public $plugin;

	/**
	 * Class constructor.
	 *
	 * @param object $plugin Plugin class.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Enqueue the prodigy commerce assets.
	 *
	 * @action wp_enqueue_scripts
	 */
	public function enqueue_assets() {
		global $post;

		$storeid = key( get_option( 'prodigy-commerce_shop-page' ) );
		$custom_css = get_option( 'prodigy-commerce_custom-css' );
		$custom_css = null !== $custom_css && false !== $custom_css ? $custom_css : '';
		$straight_to_cart = get_option( 'prodigy-commerce_right-to-cart' );
		$straight_to_cart = null !== $straight_to_cart && false !== $straight_to_cart && 'on' === $straight_to_cart ? true : false;
		$cart_page = get_option( 'prodigy-commerce_cart-page' );
		$cart_page = null !== $cart_page && false !== $cart_page ? get_post_permalink( array_keys( $cart_page )[0] ) : '';

		// If you're on a product page or store page enqueue.
		if ( 'pc_product' === $post->post_type || $storeid === $post->ID ) {
			wp_enqueue_script( "{$this->plugin->assets_prefix}-store" );
			wp_add_inline_script( "{$this->plugin->assets_prefix}-store", sprintf( 'Store.boot( %s );',
				wp_json_encode( array(
					'nonce' => wp_create_nonce( $this->plugin->meta_prefix ),
				) )
			) );
			wp_enqueue_script( "{$this->plugin->assets_prefix}-cart" );
			wp_add_inline_script( "{$this->plugin->assets_prefix}-cart", sprintf( 'Cart.boot( %s );',
				wp_json_encode( array(
					'toCart'  => $straight_to_cart,
					'cartUrl' => $cart_page,
					'nonce' => wp_create_nonce( $this->plugin->meta_prefix ),
				) )
			) );

			wp_enqueue_style( "{$this->plugin->assets_prefix}-store" );
			wp_add_inline_style( "{$this->plugin->assets_prefix}-store", $custom_css );
		}
	}

	/**
	 * Apply store page template to page.
	 *
	 * @filter page_template
	 * @param string $page_template The normal page template that usually runs on the page.
	 */
	public function shop_template( $page_template ) {
		global $post;

		// Current selected store.
		$storeid = key( get_option( 'prodigy-commerce_shop-page' ) );
		$override = $this->template_override( 'store' );

		if ( $post->ID === $storeid && ! $override ) {
			$page_template = apply_filters( 'pc_store_template', '' );
		} elseif ( false !== $override ) {
			$page_template = $override;
		}

		return $page_template;
	}

	/**
	 * Apply store page template to archive / category / tag pages.
	 *
	 * @filter archive_template
	 * @param string $category_template The normal category template.
	 */
	public function cat_template( $category_template ) {
		$override = $this->template_override( 'category' );

		if ( is_tax( 'pc_product_cat' ) && ! $override ) {
			$category_template = apply_filters( 'pc_category_template', '' );
		} elseif ( false !== $override ) {
			$category_template = $override;
		}

		return $category_template;
	}

	/**
	 * Apply pdp page template to product type.
	 *
	 * @filter single_template
	 * @param string $single_template The normal single post template.
	 */
	public function product_template( $single_template ) {
		global $post;

		$override = $this->template_override( 'product' );

		// If post type is pc_product use our template instead.
		if ( 'pc_product' === $post->post_type && ! $override ) {
			$single_template = apply_filters( 'pc_product_template', '' );
		} elseif ( false !== $override ) {
			$single_template = $override;
		}

		return $single_template;
	}

	/**
	 * Apply cart page template to page.
	 *
	 * @filter page_template
	 * @param string $page_template The normal page template that usually runs on the page.
	 */
	public function cart_template( $page_template ) {
		global $post;

		$override = $this->template_override( 'cart' );

		// Current selected store.
		$storeid = key( get_option( 'prodigy-commerce_cart-page' ) );

		if ( $post->ID === $storeid && ! $override ) {
			$page_template = apply_filters( 'pc_cart_page_template', '' );
		} elseif ( false !== $override ) {
			$page_template = $override;
		}

		return $page_template;
	}

	/**
	 * Remove the slug from published post permalinks. Only affect our custom post type, though.
	 *
	 * @param string $post_link The current posts link url.
	 * @param object $post The post object.
	 * @param string $leavename The name.
	 *
	 * @filter post_type_link 10, 3
	 *
	 * @return mixed
	 */
	public function remove_cpt_from_url( $post_link, $post, $leavename ) {

		if ( 'pc_product' !== $post->post_type || 'publish' !== $post->post_status ) {
			return $post_link;
		}

		$post_link = str_replace( '/' . $post->post_type . '/', '/', $post_link );

		return $post_link;
	}

	/**
	 * Have WordPress match postname to any of our public post types (post, page, race).
	 * All of our public post types can have /post-name/ as the slug, so they need to be unique across all posts.
	 * By default, WordPress only accounts for posts and pages where the slug is /post-name/.
	 *
	 * @action pre_get_posts
	 * @param $query The current query.
	 */
	public function add_cpt_post_names_to_main_query( $query ) {
		// Bail if this is not the main query.
		if ( ! $query->is_main_query() ) {
			return;
		}

		// Bail if this query doesn't match our very specific rewrite rule.
		if ( ! isset( $query->query['page'] ) || 2 !== count( $query->query ) ) {
			return;
		}

		// Bail if we're not querying based on the post name.
		if ( empty( $query->query['name'] ) ) {
			return;
		}

		// Add CPT to the list of post types WP will include when it queries based on the post name.
		$query->set( 'post_type', array( 'post', 'page', 'pc_product' ) );
	}

	/**
	 * Change the term request.
	 *
	 * @param array $query The request query.
	 * @filter request
	 *
	 * @return mixed
	 */
	public function change_term_request( $query ) {
		$tax_name = 'pc_product_cat';

		// Request for child terms differs, we should make an additional check.
		if ( isset( $query['attachment'] ) && $query['attachment'] ) {
			$include_children = true;
			$name  = $query['attachment'];
		} else {
			$include_children = false;
			$name = isset( $query['name'] ) ? $query['name'] : '';
		}

		$term = get_term_by( 'slug', $name, $tax_name );

		if ( isset( $name ) && $term && ! is_wp_error( $term ) ) {
			if ( $include_children ) {
				unset( $query['attachment'] );

				$parent = $term->parent;

				while ( $parent ) {
					$parent_term = get_term( $parent, $tax_name );
					$name = $parent_term->slug . '/' . $name;
					$parent = $parent_term->parent;
				}
			} else {
				unset( $query['name'] );
			}

			switch ( $tax_name ) :
				case 'category': {
					$query['category_name'] = $name;
					break;
				}
				case 'post_tag': {
					$query['tag'] = $name;
					break;
				}
				default: {
					$query[ $tax_name ] = $name;
					break;
				}
			endswitch;
		}

		return $query;
	}

	/**
	 * Update term permalink.
	 *
	 * @param $url
	 * @param $term
	 * @param $taxonomy
	 * @filter term_link 1, 3
	 *
	 * @return mixed
	 */
	public function term_permalink( $url, $term, $taxonomy ) {
		$taxonomy_name = 'Product Category'; // your taxonomy name here
		$taxonomy_slug = 'pc_product_cat'; // the taxonomy slug can be different with the taxonomy name (like 'post_tag' and 'tag' )

		// exit the function if taxonomy slug is not in URL
		if ( false === strpos( $url, $taxonomy_slug ) || $taxonomy !== $taxonomy_name ) {
			return $url;
		}

		$url = str_replace( '/' . $taxonomy_slug, '', $url );

		return $url;
	}

	/**
	 * AJAX Callback function to return products based on parameters.
	 *
	 * @action wp_ajax_get_pc_products
	 */
	public function get_pc_products() {
		check_ajax_referer( $this->plugin->meta_prefix, 'nonce' );

		if ( ! isset( $_POST['count'] ) || '' === $_POST['count'] ) { // WPCS: input var ok.
			wp_send_json_error( 'Get products failed' );
		}

		// The shortcode attribute values.
		$count = isset( $_POST['count'] ) ? intval( wp_unslash( $_POST['count'] ) ) : ''; // WPCS: input var ok.
		$count_diff = (int) $count - 1;
		$product_count = get_option( 'prodigy-commerce_pagination' );
		$product_count = null !== $product_count && false !== $product_count ? $product_count : '';
		$offset = '' !== $product_count ? (int) $product_count * $count_diff : 0;
		$products = get_pc_products( $product_count, '', '', '', $offset );
		$product_html = '';

		// Get thumbnail width / height.
		$thumbs = get_option( 'prodigy-commerce_cat-thumb' );
		$thumbs = '' !== $thumbs && false !== $thumbs && null !== $thumbs ? $thumbs : '0';

		foreach ( $products as $product ) {
			// Get product_meta.
			$product_meta = get_post_meta( $product->ID, '_pc_product_general', true );

			// Get thumbnail url.
			$image_url = get_the_post_thumbnail_url( $product->ID, 'medium' );
			$product_html .= '<li style="max-width: ' . esc_attr( $thumbs['width'] ) . 'px;" class="pc-product-item">';
			$product_html .= '<a href="' . esc_url( get_post_permalink( $product->ID ) ) . '">';
			$product_html .= '<div style="overflow: hidden; max-width: ' . esc_attr( $thumbs['width'] ) . 'px; height: ' . esc_attr( $thumbs['height'] ) . 'px;" class="pc-product-store-image">';
			$product_html .= '<img src="' . esc_url( $image_url ) . '" class="pc-cat-thumb" style="max-width: ' . esc_attr( $thumbs['width'] ) . 'px; max-height: ' . esc_attr( $thumbs['height'] ) . 'px;"/>';
			$product_html .= '</div>';
			$product_html .= '<div class="pc-product-title">';
			$product_html .= esc_html( $product->post_title );
			$product_html .= '</div>';
			$product_html .= '</a>';
			$product_html .= '<hr>';

			if ( '' !== $product_meta['pc-short-description'] ) {
				$product_html .= '<div class="pc-product-description" style="max-width: ' . esc_attr( $thumbs['width'] ) . 'px;">';
				$product_html .= esc_html( $product_meta['pc-short-description'] );
				$product_html .= '</div>';
			}

			$product_html .= '<div class="pc-product-price">';
			$product_html .= get_pc_price( $product->ID, false );
			$product_html .= '</div>';
			$product_html .= get_pc_cart_button( $product->ID, false );
			$product_html .= '</li>';
		}

		wp_send_json_success( $product_html );
	}

	/**
	 * Determine if override template exists and return its page if so.
	 *
	 * @param string $type The template to look for.
	 */
	private function template_override( $type ) {
		$template_path = "templates/{$type}-template.php";
		$exists = locate_template( $template_path );

		if ( '' !== $exists ) {
			return $exists;
		}

		return false;
	}
}
