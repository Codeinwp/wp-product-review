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
abstract class WPPR_Editor_Abstract {
	/**
	 * The WP_Post object.
	 *
	 * @var WP_Post $post .
	 */
	public $post;

	/**
	 * WPPR_Default_Editor constructor.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function __construct( $post ) {
		if ( $post instanceof WP_Post ) {
			$this->post = $post;
		} else {
			wppr_error( 'No WP_Post provided = ' . var_export( $post, true ) );
		}
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

}
