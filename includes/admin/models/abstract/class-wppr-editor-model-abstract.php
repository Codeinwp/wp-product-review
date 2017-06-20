<?php
/**
 * The abstract class of the editor.
 *
 * @package     WPPR
 * @subpackage  WPPR_Editor
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since   3.0.0
 */

/**
 * Class WPPR_Editor_Abstract.
 */
abstract class WPPR_Editor_Model_Abstract extends WPPR_Model_Abstract {
	/**
	 * The WP_Post object.
	 *
	 * @var WP_Post $post .
	 */
	public $post;

	/**
	 * The WPPR_Review object.
	 *
	 * @var WPPR_Review $review .
	 */
	public $review;
	/**
	 * The last review id saved.
	 *
	 * @var int The previous review id.
	 */
	private $previous;

	/**
	 * WPPR_Default_Editor constructor.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function __construct( $post ) {
	    parent::__construct();
		if ( $post instanceof WP_Post ) {
			$this->post   = $post;
			$this->review = new WPPR_Review_Model( $this->post->ID );
		} else {
			$this->log_error( 'No WP_Post provided = ' . var_export( $post, true ) );
		}
		$previous = $this->get_var( 'last_review' );
		if ( ! empty( $previous ) ) {
			$this->previous = new WPPR_Review_Model( $previous );
		}
	}

	/**
	 * Retrive the smart values based on the last saved review.
	 *
	 * @param string $key The field name.
	 *
	 * @return string The default value.
	 */
	public function get_value( $key ) {
		switch ( true ) {
			case  ( $key === 'wppr-editor-button-text' ):
			case  ( $key === 'wppr-editor-button-link' ) :
				if ( $this->review->is_active() ) {
					$links = $this->review->get_links();
					if ( ! empty( $links ) ) {
						if ( $key == 'wppr-editor-button-text' ) {
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
							if ( $key == 'wppr-editor-button-text' ) {
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
				$parts = explode( '-', $key );
				$index = $parts[ count( $parts ) - 1 ];
				$index = intval( $index ) - 1;
				$type  = $parts[ count( $parts ) - 2 ];

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
	 * The save method.
	 */
	public abstract function save();

	/**
	 * Render metabox editor.
	 *
	 * @return mixed
	 */
	public abstract function render();

	/**
	 * Load editor style.
	 */
	public abstract function load_style();

}
