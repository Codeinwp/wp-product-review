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
	 * The loader class.
	 *
	 * @since    3.0.0
	 * @access   private
	 * @var      WPPR_Loader    $loader    The loader class of the plugin.
	 */
	private $loader;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    3.0.0
	 *
	 * @param      string $plugin_name The name of this plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, WPPR_Loader $loader ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->loader     = $loader;

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
				$this->plugin_name . '-admin-js', WPPR_URL . '/assets/js/admin.js',
				array(
					'jquery',
					'wp-color-picker',
				),
				$this->version
			);
		}
	}

	/**
	 * Add admin menu items.
	 *
	 * @since   3.0.0
	 * @access  public
	 */
	public function menu_pages() {
		add_menu_page(
			__( 'WP Product Review', 'wp-product-review' ), __( 'Product Review', 'wp-product-review' ), 'manage_options', 'wppr', array(
				$this,
				'page_settings',
			), 'dashicons-star-half', '99.87414'
		);
		if ( ! defined( 'WPPR_PRO_VERSION' ) ) {
			add_submenu_page(
				'wppr', __( 'More Features', 'wp-product-review' ), __( 'More Features ', 'wp-product-review' ) . '<span class="dashicons
		dashicons-star-filled" style="vertical-align:-5px; padding-left:2px; color:#FFCA54;"></span>', 'manage_options', 'wppr_pro_upsell',
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

	 * Initialize the hooks and filters for the tinymce button
	 *
	 * @access  public
	 */
	public function register_init() {
		if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
			if ( 'true' == get_user_option( 'rich_editing' ) ) {
				$this->loader->add_filter( 'mce_external_plugins', $this, 'tinymce_plugin', 10, 1 );
				$this->loader->add_filter( 'mce_buttons', $this, 'register_mce_button', 10, 1 );
				$this->loader->add_action( 'admin_enqueue_scripts', $this, 'enqueue_scripts', 10 );
				$this->loader->run();
			}
		}
	}

	/**
	 * Load custom js options - TinyMCE API
	 *
	 * @since   3.0.0
	 * @access  public
	 * @param   array $plugin_array  The tinymce plugin array.
	 * @return  array
	 */
	public function tinymce_plugin( $plugin_array ) {
		$plugin_array['wppr_mce_button'] = WPPR_URL . '/assets/js/tinymce.js';
		return $plugin_array;
	}

	/**
	 * Register new button in the editor
	 *
	 * @access  public
	 * @param   array $buttons  The tinymce buttons array.
	 * @return  array
	 */
	public function register_mce_button( $buttons ) {
		$buttons[] = 'wppr_mce_button';
		return $buttons;
	}

	/**
	 * Load plugin translation for - TinyMCE API
	 *
	 * @access  public
	 * @param   array $arr  The tinymce_lang array.
	 * @return  array
	 */
	public function add_tinymce_lang( $arr ) {
		$arr[] = apply_filters( 'wppr_ui_lang_filter', WPPR_PATH . '/includes/admin/models/class-wppr-tinymce-model.php' );
		return $arr;
	}

	/**
	 * Render the form template for tinyMCE popup.
	 * Called via ajax.
	 *
	 * @since   3.0.0
	 * @access  public
	 */
	public function get_tinymce_form() {
		check_ajax_referer( WPPR_SLUG, 'nonce' );

		$html_helper = new WPPR_Html_Fields();

		$type       = $_GET['type'];
		$elements   = array();
		switch ( $type ) {
			case 'review':
				$elements   = apply_filters(
					'wppr_shortcode_ui_' . $type, array(
						array(
							'id'      => 'post_id',
							'title' => __( 'Post', 'wp-product-review' ),
							'name'    => 'post_id',
							'description'    => __( 'The post.', 'wp-product-review' ),
							'type'    => 'select',
							'options' => $this->get_reviewable_posts( true ),
							'disabled' => ! defined( 'WPPR_PRO_SLUG' ),
						),
						array(
							'id'      => 'visual',
							'title' => __( 'Display type', 'wp-product-review' ),
							'name'    => 'visual',
							'description'    => __( 'Display type.', 'wp-product-review' ),
							'type'    => 'select',
							'options' => array(
								'full'  => __( 'Full', 'wp-product-review' ),
								'yes'   => __( 'Pie only', 'wp-product-review' ),
								'no'    => __( 'Basic', 'wp-product-review' ),
							),
							'disabled' => ! defined( 'WPPR_PRO_SLUG' ),
						),
					)
				);
				break;
			case 'listing':
			case 'comparison':
				$elements   = apply_filters(
					'wppr_shortcode_ui_' . $type, array(
						array(
							'id'      => 'cat',
							'title' => __( 'Category.', 'wp-product-review' ),
							'name'    => 'cat',
							'description'   => __( 'Category.', 'wp-product-review' ),
							'type'    => 'select',
							'options' => $this->get_categories_list( true ),
							'disabled' => ! defined( 'WPPR_PRO_SLUG' ),
						),
						array(
							'id'      => 'nr',
							'title' => __( 'Number of reviews to show.', 'wp-product-review' ),
							'name'    => 'nr',
							'description'    => __( 'Number of reviews to show.', 'wp-product-review' ),
							'type'    => 'number',
							'min'   => 0,
							'default' => 10,
							'disabled' => ! defined( 'WPPR_PRO_SLUG' ),
						),
						array(
							'id'      => 'img',
							'title' => __( 'Display image?', 'wp-product-review' ),
							'name'    => 'img',
							'description'    => __( 'Display image?', 'wp-product-review' ),
							'type'    => 'select',
							'options' => array(
								'no'    => __( 'No', 'wp-product-review' ),
								'yes'   => __( 'Yes', 'wp-product-review' ),
							),
							'disabled' => ! defined( 'WPPR_PRO_SLUG' ),
						),
						array(
							'id'      => 'orderby',
							'title' => __( 'Sort results by', 'wp-product-review' ),
							'name'    => 'orderby',
							'description'    => __( 'Sort results by', 'wp-product-review' ),
							'type'    => 'select',
							'options' => array(
								'rating'    => __( 'Rating', 'wp-product-review' ),
								'price' => __( 'Price', 'wp-product-review' ),
								'date'  => __( 'Date', 'wp-product-review' ),
							),
							'disabled' => ! defined( 'WPPR_PRO_SLUG' ),
						),
						array(
							'id'      => 'order',
							'title' => __( 'Sorting order', 'wp-product-review' ),
							'name'    => 'order',
							'description'    => __( 'Sorting order', 'wp-product-review' ),
							'type'    => 'select',
							'options' => array(
								'desc'  => __( 'Descending', 'wp-product-review' ),
								'asc'   => __( 'Ascending', 'wp-product-review' ),
							),
							'disabled' => ! defined( 'WPPR_PRO_SLUG' ),
						),
					)
				);
				break;
		}

		$render = new WPPR_Admin_Render_Controller( $this->plugin_name, $this->version );
		$render->retrive_template( 'tinymce', false, $elements );
		wp_die();
	}

	/**
	 * Method called from AJAX request to populate categories of specified post types .
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
	 * Get the categories for the shortcode.
	 *
	 * @access  private
	 */
	private function get_categories_list( $default = false ) {
		$cats   = array();
		if ( $default ) {
			$cats[] = __( 'Select', 'wp-product-review' );
		}

		$categories = get_categories(
			array(
				'orderby' => 'name',
				'order'   => 'ASC',
				'hide_empty'   => false,
			)
		);

		foreach ( $categories as $category ) {
			$cats[ $category->term_id ] = $category->name;
		}
		return $cats;
	}

	/**
	 * Get the posts for the shortcode.
	 *
	 * @access  private
	 */
	private function get_reviewable_posts( $default = false ) {
		$posts  = array();
		if ( $default ) {
			$posts[]    = __( 'Select', 'wp-product-review' );
		}

		$query  = new WP_Query(
			array(
				'post_type'     => 'any',
				'post_status'   => 'publish',
				'numberposts'   => 300,
				'meta_query'    => array(
					array(
						'key'   => 'cwp_meta_box_check',
						'value' => 'Yes',
					),
				),
			)
		);

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$posts[ get_the_ID() ] = get_the_title();
			}
		}

		return $posts;
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
				), 'objects'
			);
			if ( $taxonomies ) {
				foreach ( $taxonomies as $tax ) {
					$terms = get_terms(
						$tax->name, array(
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


}
