<?php

if ( cwppos( 'cwppos_show_userreview' ) == 'yes' ) :

	add_action( 'comment_form_logged_in_after', 'cwp_additional_fields' );

	add_action( 'comment_form_after_fields', 'cwp_additional_fields' );

	add_filter( 'comment_text', 'cwp_pac_comment_single' );

	add_action( 'comment_post', 'cwp_add_comment_meta_values', 1 );

else :



	remove_action( 'comment_form_logged_in_after', 'cwp_additional_fields' );

	remove_action( 'comment_form_after_fields', 'cwp_additional_fields' );

	remove_filter( 'comment_text', 'cwp_pac_comment_single' );

	remove_action( 'comment_post', 'cwp_add_comment_meta_values', 1 );

endif ;



function cwp_pac_comment_single( $text ) {

			global $post;

			global $comment;

			$return = '';

	for ( $i = 1; $i <= cwppos( 'cwppos_option_nr' ); $i++ ) {

		$post_options[ $i ] = get_post_meta( $post->ID, "option_{$i}_content", true );

		$comment_meta_options[ "comment-meta-option-{$i}" ] = get_comment_meta( $comment->comment_ID, "meta_option_{$i}", true );

	}

			$filtered_post_options = array_filter( $comment_meta_options );

	if ( ! empty( $filtered_post_options ) ) {

			$return .= "<div class='user-comments-grades'>";

		$k = 1; // keep track

		foreach ( $comment_meta_options as $comment_meta_option => $comment_meta_value ) {

			if ( ! empty( $comment_meta_value ) ) {

				$comment_meta_score = $comment_meta_value * 10;

				$return .= "<div class='comment-meta-option'>

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

		$return .= '</div><!-- end .user-comments-grades -->';

	}

			return  $return . $text . "<div class='cwpr_clearfix'></div>";

}

function cwp_additional_fields() {
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

			$sliders[] =

			"<div class='comment-form-meta-option'>

            <label for='$k'>$meta_options[$k]</label>

            <input type='text' id='$k' class='meta_option_input' value='' name='$k' readonly='readonly'>

            <div class='comment_meta_slider'></div>

            <div class='cwpr_clearfix'></div>

		</div>";

		}

		echo "<div id='cwp-slider-comment'>" . implode( '',$sliders ) . "<div class='cwpr_clearfix'></div></div>";
	}

}



function cwp_add_comment_meta_values( $comment_id ) {
	for ( $i = 1;$i <= cwppos( 'cwppos_option_nr' );$i++ ) {

		if ( isset( $_POST[ 'meta_option_' . $i ] ) ) {

	        ${'meta_option_' . $i} = wp_filter_nohtml_kses( $_POST[ 'meta_option_' . $i ] );

	        add_comment_meta( $comment_id, 'meta_option_' . $i, ${'meta_option_' . $i}, false );

	    }
	}

}


