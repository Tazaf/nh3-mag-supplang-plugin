<?php

if ( ! class_exists( 'Supplang_Language_Taxonomy' ) ) {

	/**
	 * This class handles all the actions related to the Language custom taxonomy.
	 */
	class Supplang_Language_Taxonomy {

		public $tax_name;

		public function __construct() {
			$this->tax_name = defined( SL_LANG_TAX_ID ) ? SL_LANG_TAX_ID : 'supplang_lang';
		}

		/**
		 * Called when the plugin is activated.
		 * Registers the Language custom taxonomy and insert the default values.
		 * @author Mathias Oberson
		 */
		public function activate() {
			$this->register_taxonomy();
			$this->add_default_values();
		}

		/**
		 * Add four default languages in the Language custom taxonomy
		 * @author Mathias Oberson
		 */
		public function add_default_values() {
			$default_values = array(
				array(
					'name' => 'Français',
					'desc' => 'Apply this to french written articles',
					'slug' => 'fr',
				),
				array(
					'name' => 'Italiano',
					'desc' => 'Apply this to italian written articles',
					'slug' => 'it',
				),
				array(
					'name' => 'Rumansch',
					'desc' => 'Apply this to romansch written articles',
					'slug' => 'rm',
				),
			);

			foreach ( $default_values as $value ) {
				if ( ! term_exists( $value['name'], $this->tax_name ) ) {
					wp_insert_term(
						$value['name'],
						$this->tax_name,
						array(
							'slug'        => $value['slug'],
							'description' => $value['desc'],
						)
					);
				}
			}
		}

		/**
		 * Registers a custom taxonomy "Language" available for Posts
		 * @author Mathias Oberson
		 */
		public function register_taxonomy() {
			$labels = array(
				'name'              => _x( 'Languages', 'taxonomy general name', 'supplang' ),
				'singular_name'     => _x( 'Language', 'taxonomy singular name', 'supplang' ),
				'search_items'      => __( 'Search Languages', 'supplang' ),
				'all_items'         => __( 'All Languages', 'supplang' ),
				'parent_item'       => __( 'Parent Language', 'supplang' ),
				'parent_item_colon' => __( 'Parent Language:', 'supplang' ),
				'edit_item'         => __( 'Edit Language', 'supplang' ),
				'update_item'       => __( 'Update Language', 'supplang' ),
				'add_new_item'      => __( 'Add New Language', 'supplang' ),
				'new_item_name'     => __( 'New Language Name', 'supplang' ),
				'menu_name'         => __( 'Language', 'supplang' ),
			);

			$args = array(
				'hierarchical'      => true,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => 'lang' ),
			);

			register_taxonomy( $this->tax_name, array( 'post' ), $args );
		}

		/**
		 * Display a custom taxonomy dropdown in admin
		 * @author Mike Hemberger
		 * @link http://thestizmedia.com/custom-post-type-filter-admin-custom-taxonomy/
		 */
		public function add_admin_filter_dropdown() {
			global $typenow;
			$post_type = 'post';
			if ( $typenow == $post_type ) {
				$selected      = isset( $_GET[ $this->tax_name ] ) ? $_GET[ $this->tax_name ] : '';
				$info_taxonomy = get_taxonomy( $this->tax_name );
				wp_dropdown_categories(
					array(
						'show_option_all' => __( 'Show All Languages', 'supplang' ),
						'taxonomy'        => $this->tax_name,
						'name'            => $this->tax_name,
						'orderby'         => 'name',
						'selected'        => $selected,
						'show_count'      => true,
						'hide_empty'      => false,
					)
				);
			}
		}

		/**
		 * Filter posts by taxonomy in admin
		 * @author  Mike Hemberger
		 * @link http://thestizmedia.com/custom-post-type-filter-admin-custom-taxonomy/
		 */
		public function admin_filter_posts() {
			global $pagenow;
			$post_type      = 'post';
			$this->tax_name = SL_LANG_TAX_ID;
			$q_vars         = &$query->query_vars;
			if ( $pagenow == 'edit.php' && isset( $q_vars['post_type'] ) && $q_vars['post_type'] == $post_type && isset( $q_vars[ $this->tax_name ] ) && is_numeric( $q_vars[ $this->tax_name ] ) && $q_vars[ $this->tax_name ] != 0 ) {
				$term                      = get_term_by( 'id', $q_vars[ $this->tax_name ], $this->tax_name );
				$q_vars[ $this->tax_name ] = $term->slug;
			}
		}

	}

}