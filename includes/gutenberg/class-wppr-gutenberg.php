<?php
/**
 * Class for functionalities related to Gutenberg.
 *
 * Defines the functions that need to be used for Gutenberg,
 * and REST router.
 *
 * @package    wp-product-review
 * @subpackage wp-product-review/includes/guteneberg
 * @author     Themeisle <friends@themeisle.com>
 */
class WPPR_Gutenberg {

	/**
	 * A reference to an instance of this class.
	 *
	 * @var WPPR_Gutenberg The one WPPR_Gutenberg instance.
	 */
	private static $instance;

	/**
	 * WP Product Review version.
	 *
	 * @var string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Returns an instance of this class.
	 */
	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new WPPR_Gutenberg();
		}
		return self::$instance;
	}

	/**
	 * Initializes the plugin by setting filters and administration functions.
	 */
	private function __construct() {
		$plugin        = new WPPR();
		$this->version = $plugin->get_version();
		// Add a filter to load functions when all plugins have been loaded
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_gutenberg_scripts' ) );
		add_action( 'init', array( $this, 'register_endpoints' ) );
		add_action( 'rest_api_init', array( $this, 'update_posts_endpoints' ) );
	}

	/**
	 * Enqueue editor JavaScript and CSS
	 */
	public function enqueue_gutenberg_scripts() {
		if ( WPPR_CACHE_DISABLED ) {
			$version = filemtime( WPPR_URL . '/includes/gutenberg/dist/block.js' );
		} else {
			$version = $this->version;
		}

		// Enqueue the bundled block JS file
		wp_enqueue_script( 'wppr-gutenberg-block-js', WPPR_URL . '/includes/gutenberg/dist/block.js', array( 'wp-i18n', 'wp-edit-post', 'wp-element', 'wp-editor', 'wp-components', 'wp-data', 'wp-plugins', 'wp-edit-post', 'wp-api' ), $version );

		// Enqueue editor block styles
		wp_enqueue_style( 'wppr-gutenberg-block-css', WPPR_URL . '/includes/gutenberg/dist/block.css', '', $version );
	}

	/**
	 * Hook server side rendering into render callback
	 */
	public function update_posts_endpoints() {
		register_rest_route(
			'wp-product-review',
			'/update-review',
			array(
				'methods'  => 'POST',
				'callback' => array( $this, 'update_review_callback' ),
				'args'     => array(
					'id' => array(
						'sanitize_callback' => 'absint',
					),
				),
			)
		);
	}

	/**
	 * Rest Callbackk Method
	 */
	public function update_review_callback( $data ) {
		if ( ! empty( $data['id'] ) ) {
			$review = new WPPR_Review_Model( $data['id'] );
			if ( $data['cwp_meta_box_check'] === 'Yes' ) {
				$review->activate();

				if ( $data['postType'] === 'wppr_review' ) {
					$name = get_the_title( $data['id'] );
				} else {
					$name = isset( $data['cwp_rev_product_name'] ) ? sanitize_text_field( $data['cwp_rev_product_name'] ) : '';
				}
				$image      = isset( $data['cwp_rev_product_image'] ) ? esc_url( $data['cwp_rev_product_image'] ) : '';
				$click      = isset( $data['cwp_image_link'] ) ? strval( sanitize_text_field( $data['cwp_image_link'] ) ) : 'image';
				$template   = isset( $data['_wppr_review_template'] ) ? strval( sanitize_text_field( $data['_wppr_review_template'] ) ) : 'default';
				$affiliates = isset( $data['wppr_links'] ) ? $data['wppr_links'] : array( '' => '' );
				$price      = isset( $data['cwp_rev_price'] ) ? sanitize_text_field( $data['cwp_rev_price'] ) : 0;
				$options    = isset( $data['wppr_options'] ) ? $data['wppr_options'] : array();
				$pros       = isset( $data['wppr_pros'] ) ? $data['wppr_pros'] : array();
				$cons       = isset( $data['wppr_cons'] ) ? $data['wppr_cons'] : array();

				foreach ( $options as $key => $option ) {
					if ( $option['name'] == '' && $option['value'] == 0 ) {
						unset( $options[ $key ] );
					}
				}

				$review->set_name( $name );
				$review->set_image( $image );
				$review->set_click( $click );
				$review->set_template( $template );
				$review->set_links( $affiliates );
				$review->set_price( $price );
				$review->set_options( $options );
				$review->set_pros( $pros );
				$review->set_cons( $cons );
			} else {
				$review->deactivate();
			}
		}
	}

	/**
	 * Register Rest Field
	 */
	public function register_endpoints() {
		register_rest_field(
			array( 'post', 'wppr_review' ),
			'wppr_data',
			array(
				'get_callback'    => array( $this, 'get_post_meta' ),
				'schema'          => null,
			)
		);
	}

	/**
	 * Get Post Meta Fields
	 */
	public function get_post_meta( $post ) {
		$data = array();
		$post_id = $post['id'];
		$post_type = $post['type'];
		$options = array(
			'cwp_meta_box_check',
			'cwp_rev_product_name',
			'_wppr_review_template',
			'cwp_rev_product_image',
			'cwp_image_link',
			'wppr_links',
			'cwp_rev_price',
			'wppr_pros',
			'wppr_cons',
			'wppr_rating',
			'wppr_options',
		);
		foreach ( $options as $option ) {
			if ( get_post_meta( $post_id, $option ) ) {
				$object = get_post_meta( $post_id, $option );
				$object = $object[0];
				$data[ $option ] = $object;
			}
		}

		return $data;
	}

}
