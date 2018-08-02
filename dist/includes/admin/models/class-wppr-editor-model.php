<?php
/**
 * Short desc
 *
 * Long desc
 *
 * @package     WPPR
 * @subpackage  WPPR_Editor
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0.0
 */

/**
 * The default editor class.
 *
 * Class WPPR_Default_Editor.
 */
class WPPR_Editor_Model extends WPPR_Model_Abstract {

	/**
	 * The WP_Post object.
	 *
	 * @since   3.0.0
	 * @access  public
	 * @var WP_Post $post The WordPress post object.
	 */
	public $post;

	/**
	 * The WPPR_Review object.
	 *
	 * @since   3.0.0
	 * @access  public
	 * @var WPPR_Review $review The Review Model class.
	 */
	public $review;

	/**
	 * The last review id saved.
	 *
	 * @since   3.0.0
	 * @access  private
	 * @var int $previous The previous review id.
	 */
	private $previous;

	/**
	 * The template to use for this editor.
	 *
	 * @since   3.0.0
	 * @access  private
	 * @var string $template_to_use The template name or path for this editor.
	 */
	private $template_to_use = 'editor-default';

	/**
	 * WPPR_Default_Editor constructor.
	 *
	 * @since   3.0.0
	 * @access  public
	 *
	 * @param   WP_Post $post The post object.
	 */
	public function __construct( $post ) {
		parent::__construct();

		if ( $post instanceof WP_Post ) {
			$this->post   = $post;
			$this->review = new WPPR_Review_Model( $this->post->ID );
		} else {
			$this->logger->error( 'No WP_Post provided = ' . var_export( $post, true ) );
		}
		$previous = $this->wppr_get_option( 'last_review' );
		if ( ! empty( $previous ) ) {
			$this->previous = new WPPR_Review_Model( $previous );
		}
	}

	/**
	 * Method to get the template needed for this editor.
	 *
	 * @since   3.0.0
	 * @access  public
	 * @return string
	 */
	public function get_template() {
		return $this->template_to_use;
	}

	/**
	 * Retrive the smart values based on the last saved review.
	 *
	 * @since   3.0.0
	 * @access  public
	 *
	 * @param   string $key The field name.
	 *
	 * @return string
	 */
	public function get_value( $key ) {
		switch ( true ) {
			case ( $key === 'wppr-editor-button-text' ):
			case ( $key === 'wppr-editor-button-link' ):
				if ( $this->review->is_active() ) {
					$links = $this->review->get_links();
					if ( ! empty( $links ) ) {
						if ( $key == 'wppr-editor-button-link' ) {
							$values = array_values( $links );
						} else {
							$values = array_keys( $links );
						}

						return isset( $values[0] ) ? $values[0] : '';
					}
				} else {
					if ( ! empty( $this->previous ) ) {
						$links = $this->previous->get_links();
						if ( ! empty( $links ) ) {
							if ( $key == 'wppr-editor-button-link' ) {
								$values = array_values( $links );
							} else {
								$values = array_keys( $links );
							}

							return isset( $values[0] ) ? $values[0] : '';
						}
					}
				}

				return '';
				break;
			case ( strpos( $key, 'wppr-option-name-' ) !== false ):
			case ( strpos( $key, 'wppr-option-value-' ) !== false ):
				$options = array();
				if ( $this->review->is_active() ) {
					$options = $this->review->get_options();
				} else {
					if ( ! empty( $this->previous ) ) {
						$options = $this->previous->get_options();
					}
				}
				$first_key = key( $options );
				$parts     = explode( '-', $key );
				$index     = $parts[ count( $parts ) - 1 ];
				$index     = intval( $index ) - ( $first_key === 0 ? 1 : 0 );
				$type      = $parts[ count( $parts ) - 2 ];

				return isset( $options[ $index ] ) ? $options[ $index ][ $type ] : '';
				break;
			case ( $key === 'wppr-editor-link' ):
				if ( $this->review->is_active() ) {
					return $this->review->get_click();
				} else {
					if ( ! empty( $this->previous ) ) {
						return $this->previous->get_click();
					}
				}

				return 'image';
				break;
			default:
				return '';
		}// End switch().
	}

	/**
	 * Save the editor data.
	 *
	 * @since   3.0.0
	 * @access  public
	 */
	public function save() {
		$data = $_POST;

		do_action( 'wppr_before_save', $this->post, $data );
		$status = isset( $data['wppr-review-status'] ) ? strval( $data['wppr-review-status'] ) : 'no';

		$review = $this->review;
		if ( $status === 'yes' ) {

			$review->activate();
			$name  = isset( $data['wppr-editor-product-name'] ) ? sanitize_text_field( $data['wppr-editor-product-name'] ) : '';
			$image = isset( $data['wppr-editor-image'] ) ? esc_url( $data['wppr-editor-image'] ) : '';
			$click = isset( $data['wppr-editor-link'] ) ? strval( sanitize_text_field( $data['wppr-editor-link'] ) ) : 'image';
			$template = isset( $data['wppr-review-template'] ) ? strval( sanitize_text_field( $data['wppr-review-template'] ) ) : 'default';

			// TODO Setup links as array.
			$link           = isset( $data['wppr-editor-button-text'] ) ? strval( sanitize_text_field( $data['wppr-editor-button-text'] ) ) : '';
			$text           = isset( $data['wppr-editor-button-link'] ) ? strval( esc_url( $data['wppr-editor-button-link'] ) ) : '';
			$link2          = isset( $data['wppr-editor-button-text-2'] ) ? strval( sanitize_text_field( $data['wppr-editor-button-text-2'] ) ) : '';
			$text2          = isset( $data['wppr-editor-button-link-2'] ) ? strval( esc_url( $data['wppr-editor-button-link-2'] ) ) : '';
			$price          = isset( $data['wppr-editor-price'] ) ? sanitize_text_field( $data['wppr-editor-price'] ) : 0;
			$options_names  = isset( $data['wppr-editor-options-name'] ) ? $data['wppr-editor-options-name'] : array();
			$options_values = isset( $data['wppr-editor-options-value'] ) ? $data['wppr-editor-options-value'] : array();
			$pros           = isset( $data['wppr-editor-pros'] ) ? $data['wppr-editor-pros'] : array();
			$cons           = isset( $data['wppr-editor-cons'] ) ? $data['wppr-editor-cons'] : array();
			$options        = array();
			foreach ( $options_names as $k => $op_name ) {
				if ( ! empty( $op_name ) ) {
					$options[ $k ] = array(
						'name'  => sanitize_text_field( $op_name ),
						'value' => sanitize_text_field( isset( $options_values[ $k ] ) ? ( empty( $options_values[ $k ] ) ? 0 : $options_values[ $k ] ) : 0 ),
					);

				}
			}
			if ( is_array( $pros ) ) {
				$pros = array_map( 'sanitize_text_field', $pros );
			} else {
				$pros = array();
			}
			if ( is_array( $cons ) ) {
				$cons = array_map( 'sanitize_text_field', $cons );
			} else {
				$cons = array();
			}
			$review->set_name( $name );
			$review->set_template( $template );
			$review->set_image( $image );
			if ( $click === 'image' || $click === 'link' ) {
				$review->set_click( $click );

			}
			$links = array();
			if ( ! empty( $link ) && ! empty( $text ) ) {
				$links[ $link ] = $text;
			}
			if ( ! empty( $link2 ) && ! empty( $text2 ) ) {
				$links[ $link2 ] = $text2;
			}
			$review->set_links( $links );
			$review->set_price( $price );
			$review->set_pros( $pros );
			$review->set_cons( $cons );
			$review->set_options( $options );
			$this->wppr_set_option( 'last_review', $review->get_ID() );
		} else {
			$review->deactivate();
		}// End if().
		do_action( 'wppr_after_save', $this->post, $data );
	}

	/**
	 * Method to retrieve editor model assets.
	 *
	 * @since   3.0.0
	 * @access  public
	 * @return array
	 */
	public function get_assets() {
		$assets = array(
			'css' => array(
				'dashboard-styles' => array(
					'path'     => WPPR_URL . '/assets/css/dashboard_styles.css',
					'required' => array(),
				),
				'default-editor'   => array(
					'path'     => WPPR_URL . '/assets/css/editor.css',
					'required' => array(),
				),
			),
			'js'  => array(
				'editor' => array(
					'path'     => WPPR_URL . '/assets/js/admin-review.js',
					'required' => array( 'jquery' ),
					'vars'     => array(
						'image_title'  => __( 'Add a product image to the review', 'wp-product-review' ),
						'image_button' => __( 'Attach the image', 'wp-product-review' ),
					),
				),
			),
		);

		return $assets;
	}
}
