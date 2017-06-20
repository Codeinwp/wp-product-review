<?php
class WPPR_Comment_Controller {

	private $model;

	private $option_nr;

	public function __construct() {

		$this->model = new WPPR_Comment_Model();
		$this->add_hooks( $this->model->get_user_review() );
		$this->option_nr = $this->model->get_var( 'cwppos_option_nr' );
	}

	public function add_hooks( $user_review ) {
		if ( $user_review == 'yes' ) {
			add_action( 'comment_form_logged_in_after', array( $this, 'additional_fields' ) );
			add_action( 'comment_form_after_fields', array( $this, 'additional_fields' ) );
			add_action( 'comment_post', array( $this, 'add_comment_meta_values' ), 1 );

			add_filter( 'comment_text', array( $this, 'pac_comment_single' ) );
		} else {
			remove_action( 'comment_form_logged_in_after', array( $this, 'additional_fields' ) );
			remove_action( 'comment_form_after_fields', array( $this, 'additional_fields' ) );
			remove_action( 'comment_post', array( $this, 'add_comment_meta_values' ), 1 );

			remove_filter( 'comment_text', array( $this, 'pac_comment_single' ) );
		}
	}

	public function pac_comment_single( $text ) {
		global $post;
		global $comment;

		$html = '';

		for ( $i = 1; $i <= $this->option_nr; $i++ ) {
			$post_options[ $i ] = get_post_meta( $post->ID, "option_{$i}_content", true );
			$comment_meta_options[ "comment-meta-option-{$i}" ] = get_comment_meta( $comment->comment_ID, "meta_option_{$i}", true );
		}

		$filtered_post_options = array_filter( $comment_meta_options );

		if ( ! empty( $filtered_post_options ) ) {

			$html .= "<div class='user-comments-grades'>";

			$k = 1; // keep track

			foreach ( $comment_meta_options as $comment_meta_option => $comment_meta_value ) {

				if ( ! empty( $comment_meta_value ) ) {

					$comment_meta_score = $comment_meta_value * 10;

					$html .= "<div class='comment-meta-option'>
                            <p class='comment-meta-option-name'>{$post_options[$k]}</p>
                            <p class='comment-meta-option-grade'>$comment_meta_value</p>
                            <div class='cwpr_clearfix'></div>
                            <div class='comment-meta-grade-bar'>
                                <div class='comment-meta-grade' style='width: {$comment_meta_score}%'></div>
                            </div><!-- end .comment-meta-grade-bar -->
                        </div><!-- end .comment-meta-option -->
					";

				}

				$k++;

			}
			$html .= '</div><!-- end .user-comments-grades -->';

		}// End if().
		return  $html . $text . "<div class='cwpr_clearfix'></div>";
	}

	public function additional_fields() {
		global $post;
		$is_review = get_post_meta( $post->ID, 'cwp_meta_box_check', true );

		if ( $is_review == 'Yes' ) {
			wp_enqueue_style( 'jqueryui', WPPR_URL . '/css/jquery-ui.css',array(),WPPR_LITE_VERSION );
			wp_enqueue_script( 'jquery-ui-slider' );

			$meta_options = array();

			for ( $i = 1;$i <= cwppos( 'cwppos_option_nr' );$i++ ) {
				$meta_options[ 'meta_option_' . $i ] = get_post_meta( $post->ID, 'option_' . $i . '_content', true );
			}

			foreach ( $meta_options as $k => $value ) {
				if ( $meta_options[ $k ] == '' ) {
					unset( $meta_options[ $k ] );
				}
			}

			$sliders = array();
			foreach ( $meta_options as $k => $value ) {

				$sliders[] = "<div class='comment-form-meta-option'>
                                <label for='$k'>$meta_options[$k]</label>
                                <input type='text' id='$k' class='meta_option_input' value='' name='$k' readonly='readonly'>
                                <div class='comment_meta_slider'></div>
                                <div class='cwpr_clearfix'></div>
                              </div>";
			}

			echo "<div id='cwp-slider-comment'>" . implode( '',$sliders ) . "<div class='cwpr_clearfix'></div></div>";
		}// End if().
	}

	function add_comment_meta_values( $comment_id ) {
		for ( $i = 1; $i <= $this->option_nr; $i++ ) {
			if ( isset( $_POST[ 'meta_option_' . $i ] ) ) {
				${'meta_option_' . $i} = wp_filter_nohtml_kses( $_POST[ 'meta_option_' . $i ] );
				add_comment_meta( $comment_id, 'meta_option_' . $i, ${'meta_option_' . $i}, false );
			}
		}
	}
}
