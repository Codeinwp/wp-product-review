<?php

/**
 * The main editor logic.
 *
 * @package     WPPR
 * @subpackage  WPPR_Editor
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since 3.0.0
 */
class WPPR_Editor {
	/**
	 * WPPR_Editor constructor.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'set_editor' ) );
		add_action( 'save_post', array( $this, 'editor_save' ) );
	}

	/**
	 * Register the metabox editor.
	 */
	public function set_editor() {
		add_meta_box( 'wppr_editor_metabox', 'Product Review Extra Settings', array( $this, 'render_metabox' ) );
	}

	/**
	 * Render the editor for WPPR.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_metabox( $post ) {
		$editor = $this->get_editor_name( $post );
		$editor->render();
	}

	/**
	 * Get editor class name.
	 *
	 * @param WP_Post $post The post .
	 *
	 * @return object The editor object.
	 */
	private function get_editor_name( $post ) {
		$editor_name = 'WPPR_' . str_replace( '-', '_', ucfirst( $post->post_type ) . '_Editor' );
		if ( class_exists( $editor_name ) ) {
			$editor = new $editor_name ( $post );
		} else {
			$editor = new WPPR_Default_Editor( $post );
		}

		return $editor;
	}

	/**
	 * Save callback for WPPR editor.
	 *
	 * @param int $post_id The post id.
	 */
	public function editor_save( $post_id ) {
		$editor = $this->get_editor_name( get_post( $post_id ) );
		$editor->save();
	}
}