<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://themeisle.com/
 * @since      3.0.0
 *
 * @package    WPPR
 * @subpackage WPPR/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WPPR
 * @subpackage WPPR/admin
 * @author     ThemeIsle <friends@themeisle.com>
 */
class WPPR_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    3.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    3.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    3.0.0
	 *
	 * @param      string $plugin_name The name of this plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since   3.0.0
	 * @access  public
	 *
	 * @param   string $hook The hook used filter loaded styles.
	 */
	public function enqueue_styles( $hook ) {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WPPR_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WPPR_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if ( $hook == 'toplevel_page_wppr' ) {
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( $this->plugin_name . '-dashboard-css', WPPR_URL . '/assets/css/dashboard_styles.css', array(), $this->version );
			wp_enqueue_style( $this->plugin_name . '-admin-css', WPPR_URL . '/assets/css/admin.css', array(), $this->version );
		}
		if ( $hook == 'product-review_page_wppr_pro_upsell' || $hook == 'toplevel_page_wppr' ) {
			wp_enqueue_style( $this->plugin_name . '-upsell-css', WPPR_URL . '/assets/css/upsell.css', array(), $this->version );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since   3.0.0
	 * @access  public
	 *
	 * @param   string $hook The hook used filter loaded scripts.
	 */
	public function enqueue_scripts( $hook ) {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WPPR_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WPPR_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if ( $hook == 'toplevel_page_wppr' ) {
			wp_enqueue_script(
				$this->plugin_name . '-admin-js',
				WPPR_URL . '/assets/js/admin.js',
				array(
					'jquery',
					'wp-color-picker',
				),
				$this->version
			);
		}

		$this->load_review_cpt();
	}

	/**
	 * Add admin menu items.
	 *
	 * @since   3.0.0
	 * @access  public
	 */
	public function menu_pages() {
		add_menu_page(
			__( 'WP Product Review', 'wp-product-review' ),
			__( 'Product Review', 'wp-product-review' ),
			'manage_options',
			'wppr',
			array(
				$this,
				'page_settings',
			),
			'dashicons-star-half',
			'99.87414'
		);
		if ( ! defined( 'WPPR_PRO_VERSION' ) ) {
			add_submenu_page(
				'wppr',
				__( 'More Features', 'wp-product-review' ),
				__( 'More Features ', 'wp-product-review' ) . '<span class="dashicons
		dashicons-star-filled" style="vertical-align:-5px; padding-left:2px; color:#FFCA54;"></span>',
				'manage_options',
				'wppr_pro_upsell',
				array(
					$this,
					'page_upsell',
				)
			);
		}
	}

	/**
	 * Method to render settings page.
	 *
	 * @since   3.0.0
	 * @access  public
	 */
	public function page_settings() {
		$model  = new WPPR_Options_Model();
		$render = new WPPR_Admin_Render_Controller( $this->plugin_name, $this->version );
		$render->retrive_template( 'settings', $model );
	}

	/**
	 * Method to render up-sell page.
	 *
	 * @since   3.0.0
	 * @access  public
	 */
	public function page_upsell() {
		$render = new WPPR_Admin_Render_Controller( $this->plugin_name, $this->version );
		$render->retrive_template( 'upsell' );
	}

	/**
	 * Method called from AJAX request to update options.
	 *
	 * @since   3.0.0
	 * @access  public
	 */
	public function update_options() {
		$model = new WPPR_Options_Model();
		$data  = $_POST['cwppos_options'];

		$nonce = $data[ count( $data ) - 1 ];
		if ( ! isset( $nonce['name'] ) ) {
			die( 'invalid nonce field' );
		}
		if ( $nonce['name'] != 'wppr_nonce_settings' ) {
			die( 'invalid nonce name' );
		}
		if ( wp_verify_nonce( $nonce['value'], 'wppr_save_global_settings' ) != 1 ) {
			die( 'invalid nonce value' );
		}

		foreach ( $data as $option ) {
			$model->wppr_set_option( $option['name'], $option['value'] );
		}
		die();
	}

	/**
	 * Method called from AJAX request to populate categories of specified post types.
	 *
	 * @since   3.0.0
	 * @access  public
	 */
	public function get_categories() {
		check_ajax_referer( WPPR_SLUG, 'nonce' );

		if ( isset( $_POST['type'] ) ) {
			echo wp_send_json_success( array( 'categories' => self::get_category_for_post_type( $_POST['type'] ) ) );
		}
		wp_die();
	}

	/**
	 * Method that returns the categories of specified post types.
	 *
	 * @since   3.0.0
	 * @access  public
	 */
	public static function get_category_for_post_type( $post_type ) {
		$categories = array();
		if ( $post_type ) {
			$taxonomies = get_taxonomies(
				array( 'object_type' => array( $post_type ),
												 'hierarchical' => true,
				),
				'objects'
			);
			if ( $taxonomies ) {
				foreach ( $taxonomies as $tax ) {
					$terms = get_terms(
						$tax->name,
						array(
							'hide_empty' => false,
						)
					);
					if ( empty( $terms ) ) {
						continue;
					}
					foreach ( $terms as $term ) {
						$categories[ $term->slug ] = $term->name;
					}
				}
			}
		}

		return $categories;
	}

	/**
	 * Adds the additional fields (columns, filters etc.) to the post listing screen.
	 *
	 * @since   3.0.0
	 * @access  public
	 */
	public function get_additional_fields() {
		// add filter to post listing.
		add_action( 'restrict_manage_posts', array( $this, 'restrict_manage_posts' ), 10, 2 );
		add_filter( 'parse_query', array( $this, 'show_only_review_posts' ), 10 );

		// add columns to post listing.
		$post_types     = apply_filters( 'wppr_post_types_custom_columns', array() );
		if ( $post_types ) {
			foreach ( $post_types as $post_type ) {
				$type   = in_array( $post_type, array( 'post', 'page' ) ) ? "{$post_type}s" : "{$post_type}_posts";
				add_filter( "manage_{$type}_columns", array( $this, 'manage_posts_columns' ), 10, 1 );
				add_action( "manage_{$type}_custom_column", array( $this, 'manage_posts_custom_column' ), 10, 2 );
			}
		}

		$this->get_additional_fields_for_cpt();
	}

	/**
	 * Show the filter.
	 *
	 * @access  public
	 */
	public function restrict_manage_posts( $post_type, $which ) {
		$post_types     = apply_filters( 'wppr_post_types_custom_filter', array( 'post', 'page' ) );
		if ( ! $post_types || ! in_array( $post_type, $post_types ) ) {
			return;
		}

		echo "<select name='wppr_filter' id='wppr_filter' class='postform'>";
		echo "<option value=''>" . __( 'Show All', 'wp-product-review' ) . '</option>';
		$selected   = isset( $_REQUEST['wppr_filter'] ) && 'only-wppr' === $_REQUEST['wppr_filter'] ? 'selected' : '';
		echo "<option value='only-wppr' $selected>" . __( 'Show only Reviews', 'wp-product-review' ) . '</option>';
		echo '</select>';
	}

	/**
	 * Filter only reviews.
	 *
	 * @access  public
	 */
	public function show_only_review_posts( $query ) {
		if ( ! ( is_admin() && $query->is_main_query() ) ) {
			return $query;
		}

		if ( ! isset( $_REQUEST['wppr_filter'] ) || 'only-wppr' !== $_REQUEST['wppr_filter'] ) {
			return $query;
		}

		$post_types     = apply_filters( 'wppr_post_types_custom_filter', array( 'post', 'page' ) );
		if ( ! in_array( $query->query['post_type'], $post_types ) ) {
			return $query;
		}

		$query->query_vars['meta_query'] = array(
			array(
				'field'     => 'cwp_meta_box_check',
				'value'     => 'Yes',
				'compare'   => '=',
				'type'      => 'CHAR',
			),
		);

		return $query;
	}

	/**
	 * Define the additional columns.
	 *
	 * @access  public
	 */
	public function manage_posts_columns( $columns ) {
		$columns['wppr_review']    = __( 'Review', 'wp-product-review' );
		return $columns;
	}

	/**
	 * Manage the additional column.s
	 *
	 * @access  public
	 */
	public function manage_posts_custom_column( $column, $id ) {
		switch ( $column ) {
			case 'wppr_review':
				$model = new WPPR_Review_Model( $id );
				echo $model->get_rating();
				break;
		}
	}

	/**
	 * Loads the assets for the CPT.
	 */
	public function load_review_cpt() {
		$current_screen = get_current_screen();

		if ( ! isset( $current_screen->id ) ) {
			return;
		}
		if ( $current_screen->id != 'wppr_review' ) {
			return;
		}

		wp_enqueue_script(
			$this->plugin_name . '-cpt-js',
			WPPR_URL . '/assets/js/cpt.js',
			array(
				'jquery',
			),
			$this->version
		);

		wp_localize_script(
			$this->plugin_name . '-cpt-js',
			'wppr',
			array(
				'i10n' => array(
					'title_placeholder' => __( 'Enter Review Title', 'wp-product-review' ),
				),
			)
		);
	}

	/**
	 * Loads the additional fields for the CPT.
	 */
	private function get_additional_fields_for_cpt() {
		$model = new WPPR_Query_Model();
		if ( 'yes' !== $model->wppr_get_option( 'wppr_cpt' ) ) {
			return;
		}

		add_filter( 'manage_wppr_review_posts_columns', array( $this, 'manage_cpt_columns' ), 10, 1 );
		add_action( 'manage_wppr_review_posts_custom_column', array( $this, 'manage_cpt_custom_column' ), 10, 2 );
	}

	/**
	 * Define the additional columns for the CPT.
	 *
	 * @access  public
	 */
	public function manage_cpt_columns( $columns ) {
		$custom     = array(
			'wppr_price' => __( 'Product Price', 'wp-product-review' ),
			'wppr_rating' => __( 'Rating', 'wp-product-review' ),
		);

		// add before the date column.
		return array_slice( $columns, 0, -1, true ) + $custom + array_slice( $columns, -1, null, true );
	}

	/**
	 * Manage the additional columns for the CPT.
	 *
	 * @access  public
	 */
	public function manage_cpt_custom_column( $column, $id ) {
		switch ( $column ) {
			case 'wppr_price':
				$model = new WPPR_Review_Model( $id );
				echo $model->get_price();
				break;
			case 'wppr_rating':
				$model = new WPPR_Review_Model( $id );
				echo $model->get_rating();
				break;
		}
	}

	/**
	 * Add an upsell bar when the tab starts.
	 *
	 * @param string $section Name of the section.
	 */
	public function settings_section_upsell( $section ) {
		if ( 'general' === $section ) {
			echo '<label class="wppr-upsell-label"> You can display the review using the <b>[P_REVIEW]</b> shortcode. You can read more about it <a href="https://docs.themeisle.com/article/449-wp-product-review-shortcode-documentation" target="_blank">here</a></label>.';
		}
	}

	/**
	 * Add a custom image size for widgets.
	 */
	public function add_image_size() {
		add_image_size( 'wppr-widget', 50, 50 );
	}

}
