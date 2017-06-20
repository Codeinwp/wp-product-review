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
			$editor = new WPPR_Default_Editor_Model( $post );
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

	public function save() {
		// Added by Ash/Upwork
		do_action( 'wppr-amazon-savefields', $post_id );
		// Added by Ash/Upwork
		// Moved from inside the if loop to here by Ash/Upwork
		if ( isset( $_POST['cwp_meta_box_check'] ) ) {
			update_post_meta( $post_id, 'cwp_meta_box_check', sanitize_text_field( $_POST['cwp_meta_box_check'] ) );
		}
		// Moved from inside the if loop to here by Ash/Upwork
		if ( isset( $_POST['cwp_meta_box_check'] ) && $_POST['cwp_meta_box_check'] == 'Yes' ) {
			if ( isset( $_POST['cwp_rev_product_name'] ) ) {
				update_post_meta( $post_id, 'cwp_rev_product_name', apply_filters( 'wppr_sanitize_product_title', $_POST['cwp_rev_product_name'] ) );
			}
			if ( isset( $_POST['cwp_rev_price'] ) ) {
				update_post_meta( $post_id, 'cwp_rev_price', apply_filters( 'wppr_sanitize_product_price', $_POST['cwp_rev_price'] ) );
			}
			if ( isset( $_POST['cwp_image_link'] ) ) {
				update_post_meta( $post_id, 'cwp_image_link', apply_filters( 'wppr_sanitize_product_image', $_POST['cwp_image_link'] ) );
			}
			if ( isset( $_POST['cwp_product_affiliate_text'] ) ) {
				update_post_meta( $post_id, 'cwp_product_affiliate_text', sanitize_text_field( $_POST['cwp_product_affiliate_text'] ) );
			}
			if ( isset( $_POST['cwp_product_affiliate_link'] ) ) {
				update_post_meta( $post_id, 'cwp_product_affiliate_link', apply_filters( 'wppr_sanitize_link1', $_POST['cwp_product_affiliate_link'] ) );
			}
			if ( isset( $_POST['cwp_product_affiliate_text2'] ) ) {
				update_post_meta( $post_id, 'cwp_product_affiliate_text2', sanitize_text_field( $_POST['cwp_product_affiliate_text2'] ) );
			}
			if ( isset( $_POST['cwp_product_affiliate_link2'] ) ) {
				update_post_meta( $post_id, 'cwp_product_affiliate_link2', apply_filters( 'wppr_sanitize_link2', $_POST['cwp_product_affiliate_link2'] ) );
			}
			if ( ! empty( $_POST['cwp_bar_icon'] ) ) {
				update_post_meta( $post_id, 'cwp_bar_icon', esc_url( $_POST['cwp_bar_icon'] ) );
			} else {
				update_post_meta( $post_id, 'cwp_bar_icon', '' );
			}
			for ( $i = 1; $i <= cwppos( 'cwppos_option_nr' ); $i ++ ) {
				if ( isset( $_POST[ 'option_' . $i . '_content' ] ) ) {
					update_post_meta( $post_id, 'option_' . $i . '_content', sanitize_text_field( $_POST[ 'option_' . $i . '_content' ] ) );
				}
				if ( isset( $_POST[ 'option_' . $i . '_grade' ] ) ) {
					update_post_meta( $post_id, 'option_' . $i . '_grade', sanitize_text_field( $_POST[ 'option_' . $i . '_grade' ] ) );
				}
			}
			if ( isset( $_POST['cwp_rev_product_image'] ) ) {
				update_post_meta( $post_id, 'cwp_rev_product_image', sanitize_text_field( $_POST['cwp_rev_product_image'] ) );
			} elseif ( cwppos( 'cwppos_show_poweredby' ) == 'yes' || class_exists( 'CWP_PR_PRO_Core' ) ) {
				$image = '';
				if ( strlen( $img = get_the_post_thumbnail( $post_id, array( 150, 150 ) ) ) ) :
					$image_array = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'optional-size' );
					$image       = $image_array[0];
				else :
					$post  = get_post( $post_id );
					$image = '';
					ob_start();
					ob_end_clean();
					$output = preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches );
					// $image = $matches[1][0];
				endif;
				update_post_meta( $post_id, 'cwp_rev_product_image', $image );
			}
			for ( $i = 1; $i <= cwppos( 'cwppos_option_nr' ); $i ++ ) {
				if ( isset( $_POST[ 'cwp_option_' . $i . '_pro' ] ) ) {
					update_post_meta( $post_id, 'cwp_option_' . $i . '_pro', sanitize_text_field( $_POST[ 'cwp_option_' . $i . '_pro' ] ) );
				}
				if ( isset( $_POST[ 'cwp_option_' . $i . '_cons' ] ) ) {
					update_post_meta( $post_id, 'cwp_option_' . $i . '_cons', sanitize_text_field( $_POST[ 'cwp_option_' . $i . '_cons' ] ) );
				}
			}
			$overall_score = '';
			$iter          = 0;
			for ( $i = 1; $i <= cwppos( 'cwppos_option_nr' ); $i ++ ) {
				${'option' . $i . '_grade'} = get_post_meta( $post_id, 'option_' . $i . '_grade', true );
				if ( ! empty( ${'option' . $i . '_grade'} ) || ${'option' . $i . '_grade'} === '0' ) {
					$overall_score += ${'option' . $i . '_grade'};
					$iter ++;
				}
			}
			if ( $iter == 0 ) {
				$overall_score = 0;
			} else {
				$overall_score = $overall_score / $iter;
			}
			update_post_meta( $post_id, 'option_overall_score', $overall_score / 10 );
		}// End if().
	}
}
