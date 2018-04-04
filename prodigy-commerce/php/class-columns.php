<?php
/**
 * This class holds the logic for the pc_product admin columns.
 *
 * @package ProdigyCommerce
 */

namespace ProdigyCommerce;

/**
 * Columns Class
 *
 * @package ProdigyCommerce
 */
class Columns {

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
	 * Add custom columns to all products page.
	 *
	 * @param $columns
	 * @filter manage_edit-pc_product_columns
	 */
	public function custom_columns( $columns ) {
		$columns = array(
			'cb'         => '<input type="checkbox" />',
			'image'      => esc_html__( 'Image', 'prodigy-commerce' ),
			'title'      => esc_html__( 'Title', 'prodigy-commerce' ),
			'price'      => esc_html__( 'Price', 'prodigy-commerce' ),
			//'type'       => esc_html__( 'Type', 'prodigy-commerce' ), Will add back when extension is made for variable products.
			'prod_categories' => esc_html__( 'Categories', 'prodigy-commerce' ),
			'prod_tags'       => esc_html__( 'Tags', 'prodigy-commerce' ),
			'date'       => esc_html__( 'Publish Date', 'prodigy-commerce' ),
		);

		return $columns;
	}

	/**
	 * Set the custom price column as sortable.
	 *
	 * @param $columns
	 *
	 * @filter manage_edit-pc_product_sortable_columns
	 * @return mixed
	 */
	public function my_pc_product_sortable_columns( $columns ) {
		$columns['price'] = 'price';

		return $columns;
	}

	/**
	 * Only run our customization on the 'edit.php' page in the admin.
	 *
	 * @action load-edit.php
	 */
	public function my_edit_pc_product_load() {
		add_filter( 'request', array( $this, 'my_sort_pc_products' ) );
	}

	/**
	 * Sorts the movies.
	 */
	public function my_sort_pc_products( $vars ) {
		// Check if we're viewing the 'pc_product' post type.
		if ( isset( $vars['post_type'] ) && 'pc_product' === $vars['post_type'] ) {
			// Check if 'orderby' is set to 'price'.
			if ( isset( $vars['orderby'] ) && 'price' === $vars['orderby'] ) {
				// Merge the query vars with our custom variables.
				$vars = array_merge(
					$vars,
					array(
						'meta_key' => '_pc_regular_price',
						'orderby' => 'meta_value_num',
					)
				);
			}
		}

		return $vars;
	}

	/**
	 * Add content to the custom columns.
	 *
	 * @param $column
	 * @param $post_id
	 *
	 * @action manage_pc_product_posts_custom_column
	 */
	public function my_manage_pc_product_columns( $column, $post_id ) {
		global $post;

		$product_gen = get_post_meta( $post->ID, '_pc_product_general', true );
		$product_gen = '' !== $product_gen && null !== $product_gen && false !== $product_gen ? $product_gen : '';
		$regular_price = get_post_meta( $post->ID, '_pc_regular_price', true );
		$regular_price = '' !== $regular_price && null !== $regular_price && false !== $regular_price ? $regular_price : '';

		switch ( $column ) {
			case 'image' :
				// Get the product image.
				$image = get_the_post_thumbnail( $post->ID, array( 100, 100 ) );

				echo wp_kses_post( $image );
				break;
			case 'title' :
				echo esc_html( $post->post_title );
				break;
			case 'price' :
				echo esc_html( $regular_price );
				break;
			case 'prod_categories' :
				// Get product categories.
				$cat_list = wp_get_post_terms($post->ID, 'pc_product_cat', array(
					'fields' => 'names',
				) );

				echo esc_html( implode( ', ', $cat_list ) );
				break;
			case 'prod_tags' :
				// Get tags categories.
				$tag_list = wp_get_post_terms($post->ID, 'pc_product_tag', array(
					'fields' => 'names',
				) );

				echo esc_html( implode( ', ', $tag_list ) );
				break;
			default :
				break;
		}
	}

	/**
	 * Filter the products by category.
	 *
	 * @action restrict_manage_posts
	 */
	public function filter_by_taxonomy() {
		global $post_type;

		$taxonomies = array( 'pc_product_cat', 'pc_product_tag' );

		foreach ( $taxonomies as $taxonomy ) {
			if ( 'pc_product' === $post_type ) {
				$selected = isset( $_GET[ $taxonomy ] ) ? $_GET[ $taxonomy ] : ''; // WPCS: CSRF ok.
				$info_taxonomy = get_taxonomy( $taxonomy );

				wp_dropdown_categories( array(
					'show_option_all' => esc_html__( 'All ', 'prodigy-commerce' ) . $info_taxonomy->label,
					'taxonomy'        => $taxonomy,
					'name'            => $taxonomy,
					'orderby'         => 'name',
					'selected'        => $selected,
					'show_count'      => true,
					'hide_empty'      => true,
				) );
			}
		}
	}

	/**
	 * Filter posts by taxonomy in admin
	 *
	 * @param object $query The post query.
	 * @filter parse_query
	 */
	public function id_to_term_query( $query ) {
		global $pagenow;

		$post_type = 'pc_product';
		$taxonomies = array( 'pc_product_cat', 'pc_product_tag' );
		$q_vars = &$query->query_vars;

		foreach ( $taxonomies as $taxonomy ) {
			if ( 'edit.php' === $pagenow && isset( $q_vars['post_type'] ) && $q_vars['post_type'] === $post_type && isset( $q_vars[ $taxonomy ] ) && is_numeric( $q_vars[ $taxonomy ] ) && 0 !== $q_vars[ $taxonomy ] ) {
				$term = get_term_by( 'id', $q_vars[ $taxonomy ], $taxonomy );
				$q_vars[ $taxonomy ] = $term->slug;
			}
		}
	}
}
