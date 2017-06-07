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
		add_action( 'admin_enqueue_scripts', array( $this, 'load_assets' ) );
	}

	/**
	 * Register the metabox editor.
	 */
	public function set_editor() {
		add_meta_box( 'wppr_editor_metabox', __( 'Product Review Extra Settings', 'wp-product-review' ), array(
			$this,
			'render_metabox'
		) );
	}

	/**
	 * Render the editor for WPPR.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_metabox( $post ) {
		$editor = $this->get_editor_name( $post );
		wp_nonce_field( 'wppr_editor_save.' . $post->ID, '_wppr_nonce' );
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
	 * Enqueue assets for WPPR.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function load_assets( $post ) {
		global $post;
		if ( is_a( $post, 'WP_Post' ) ) {
			$editor = $this->get_editor_name( $post );
			$editor->load_style();
		}
	}

	/**
	 * Save callback for WPPR editor.
	 *
	 * @param int $post_id The post id.
	 */
	public function editor_save( $post_id ) {
		$editor = $this->get_editor_name( get_post( $post_id ) );

		$is_autosave    = wp_is_post_autosave( $post_id );
		$is_revision    = wp_is_post_revision( $post_id );
		$nonce          = isset( $_REQUEST['_wppr_nonce'] ) ? $_REQUEST['_wppr_nonce'] : '';
		$is_valid_nonce = wp_verify_nonce( $nonce, 'wppr_editor_save.' . $post_id );

		if ( $is_autosave || $is_revision || ! $is_valid_nonce ) {
			return;
		}
		$editor->save();
	}
}