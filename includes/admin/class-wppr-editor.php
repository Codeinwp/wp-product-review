<?php
class WPPR_Editor {

	public function set_editor() {
		add_meta_box( 'wppr_editor_metabox', __( 'Product Review Extra Settings', 'wp-product-review' ), array(
			$this,
			'render_metabox',
		) );
	}

	public function render_metabox( $post ) {
		$editor = $this->get_editor_name( $post );
		wp_nonce_field( 'wppr_editor_save.' . $post->ID, '_wppr_nonce' );
		$editor->render();
	}

	private function get_editor_name( $post ) {
		$editor_name = 'WPPR_' . str_replace( '-', '_', ucfirst( $post->post_type ) . '_Editor' );
		if ( class_exists( $editor_name ) ) {
			$editor = new $editor_name ( $post );
		} else {
			$editor = new WPPR_Default_Editor( $post );
		}

		return $editor;
	}

	public function load_assets( $post ) {
		global $post;
		if ( is_a( $post, 'WP_Post' ) ) {
			$editor = $this->get_editor_name( $post );
			$editor->load_style();
		}
	}

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
