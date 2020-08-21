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
		if ( null === self::$instance ) {
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
		add_action( 'wp_loaded', array( $this, 'register_endpoints' ) );
		add_action( 'rest_api_init', array( $this, 'update_posts_endpoints' ) );
		add_filter( 'rest_post_query', array( $this, 'post_meta_request_params' ), 99, 2 );
		add_filter( 'rest_page_query', array( $this, 'post_meta_request_params' ), 99, 2 );
		add_filter( 'rest_wppr_review_query', array( $this, 'post_meta_request_params' ), 99, 2 );
	}

	/**
	 * Enqueue editor JavaScript and CSS
	 */
	public function enqueue_gutenberg_scripts() {
		if ( WPPR_CACHE_DISABLED ) {
			$version = filemtime( WPPR_PATH . '/includes/gutenberg/build/sidebar.js' );
		} else {
			$version = $this->version;
		}

		if ( defined( 'WPPR_PRO_VERSION' ) ) {
			$isPro = true;
		} else {
			$isPro = false;
		}

		$model = new WPPR_Query_Model();
		$length = $model->wppr_get_option( 'cwppos_option_nr' );

		// Enqueue the bundled block JS file
		wp_enqueue_script( 'wppr-gutenberg-block-js', WPPR_URL . '/includes/gutenberg/build/sidebar.js', array( 'wp-i18n', 'wp-edit-post', 'wp-element', 'wp-editor', 'wp-components', 'wp-compose', 'wp-data', 'wp-plugins', 'wp-edit-post', 'wp-api' ), $version );

		wp_localize_script(
			'wppr-gutenberg-block-js',
			'wpprguten',
			array(
				'isPro' => $isPro,
				'path'  => WPPR_URL,
				'length' => $length,
				'schema_types' => $this->get_schema_types(),
			)
		);

		// Enqueue editor block styles
		wp_enqueue_style( 'wppr-gutenberg-block-css', WPPR_URL . '/includes/gutenberg/build/sidebar.css', '', $version );
	}

	/**
	 * Get the schema types in a consumable format.
	 */
	private function get_schema_types() {
		$types = WPPR_Schema_Model::get_types();
		$array = array();
		if ( $types ) {
			foreach ( $types as $type ) {
				$array[] = array( 'label' => $type, 'value' => $type );
			}
		}
		return $array;
	}

	/**
	 * Hook server side rendering into render callback
	 */
	public function update_posts_endpoints() {
		register_rest_route(
			'wppr/v1',
			'/update-review',
			array(
				'methods'  => 'POST',
				'callback' => array( $this, 'update_review_callback' ),
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
				'args'     => array(
					'id' => array(
						'sanitize_callback' => 'absint',
					),
				),
			)
		);

		register_rest_route(
			'wppr/v1',
			'/schema-fields',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'get_schema_details' ),
				'args'     => array(
					'type' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);

	}

	/**
	 * Rest Callbackk Method to get schema details (fields and url).
	 */
	public function get_schema_details( $data ) {
		if ( empty( $data['type'] ) ) {
			return;
		}

		return array( 'fields' => WPPR_Schema_Model::get_fields_for_type( $data['type'] ), 'url' => WPPR_Schema_Model::get_schema_url( $data['type'] ) );
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
				$schema_type    = isset( $data['wppr_review_type'] ) ? sanitize_text_field( $data['wppr_review_type'] ) : 'Product';
				$schema_field_values  = isset( $data['wppr_review_custom_fields'] ) ? $data['wppr_review_custom_fields'] : array();
				$schema_fields  = isset( $data['schema_fields'] ) ? $data['schema_fields'] : array();

				$custom_fields  = array();
				if ( $schema_field_values ) {
					foreach ( $schema_field_values as $field => $value ) {
						// if the field is not part of the current schema, unset it
						// this can happen when the schema of a review is being changed.
						if ( ! in_array( $field, $schema_fields, true ) ) {
							unset( $custom_fields[ $field ] );
						} elseif ( ! empty( $value ) ) {
							$custom_fields[ $field ] = sanitize_text_field( $value );
						}
					}
				}

				foreach ( $affiliates as $key => $option ) {
					if ( $option === '' ) {
						unset( $affiliates[ $key ] );
					}
				}

				foreach ( $options as $key => $option ) {
					// phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
					if ( $option['name'] === '' && $option['value'] == 0 ) {
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
				$review->set_type( $schema_type );
				$review->set_custom_fields( $custom_fields );
			} else {
				$review->deactivate();
			}

			return new \WP_REST_Response( array( 'message' => __( 'Review updated.', 'wp-product-review' ) ), 200 );
		}
	}

	/**
	 * Register Rest Field
	 */
	public function register_endpoints() {
		$args = array(
			'public'   => true,
		);

		$output = 'names';
		$operator = 'and';

		$post_types = get_post_types( $args, $output, $operator );

		register_rest_field(
			$post_types,
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
			'wppr_review_type',
			'wppr_review_custom_fields',
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

	/**
	 * Allow querying posts by meta in REST API
	 */
	public function post_meta_request_params( $args, $request ) {
		$args += array(
			'meta_key'   => $request['meta_key'],
			'meta_value' => $request['meta_value'],
			'meta_query' => $request['meta_query'],
		);
		return $args;
	}

}
