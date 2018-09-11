<?php
/**
 *  Up-sell layout in the admin dashboard.
 *
 * @package     WPPR
 * @subpackage  Admin
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0.0
 */

/**
 * Class WPPR_Editor
 */
class WPPR_Editor {

	/**
	 * The ID of this plugin.
	 *
	 * @since    3.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    3.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    3.0.0
	 *
	 * @param      string $plugin_name The name of this plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Method to add editor meta box.
	 *
	 * @since   3.0.0
	 * @access  public
	 */
	public function set_editor() {
		add_meta_box(
			'wppr_editor_metabox',
			__( 'Product Review Extra Settings', 'wp-product-review' ),
			array(
				$this,
				'render_metabox',
			)
		);
	}

	/**
	 * Method to render editor.
	 *
	 * @since   3.0.0
	 * @access  public
	 *
	 * @param   WP_Post $post The post object.
	 */
	public function render_metabox( $post ) {
		$editor = $this->get_editor_name( $post );
		wp_nonce_field( 'wppr_editor_save.' . $post->ID, '_wppr_nonce' );
		$render_controller = new WPPR_Admin_Render_Controller( $this->plugin_name, $this->version );
		$render_controller->render_editor_metabox( $editor->get_template(), $editor );
	}

	/**
	 * Method to return editor object.
	 *
	 * @since   3.0.0
	 * @access  public
	 *
	 * @param   WP_Post $post The post object.
	 *
	 * @return WPPR_Editor_Abstract
	 */
	private function get_editor_name( $post ) {
		$editor_name = 'WPPR_' . str_replace( '-', '_', ucfirst( $post->post_type ) . '_Editor' );
		if ( class_exists( $editor_name ) ) {
			$editor = new $editor_name( $post );
		} else {
			$editor = new WPPR_Editor_Model( $post );
		}

		return $editor;
	}

	/**
	 * Method to load required assets.
	 *
	 * @since   3.0.0
	 * @access  public
	 *
	 * @param   WP_Post $post The post object.
	 */
	public function load_assets( $post ) {
		global $post;
		if ( is_a( $post, 'WP_Post' ) ) {
			$editor = $this->get_editor_name( $post );
			$assets = $editor->get_assets();
			if ( ! empty( $assets ) ) {
				if ( isset( $assets['js'] ) ) {
					foreach ( $assets['js'] as $handle => $data ) {
						if ( isset( $data['path'] ) ) {
							wp_enqueue_script( 'wppr-' . $handle . '-js', $data['path'], $data['required'], $this->version, true );
						}
						if ( isset( $data['vars'] ) ) {
							wp_localize_script( 'wppr-' . $handle . '-js', $handle . '_vars', $data['vars'] );
						}
					}
				}

				if ( isset( $assets['css'] ) ) {
					foreach ( $assets['css'] as $handle => $data ) {
						if ( isset( $data['path'] ) ) {
							wp_enqueue_style( 'wppr-' . $handle . '-css', $data['path'], $data['required'], $this->version );
						}
					}
				}
			}
		}
	}

	/**
	 * Method to save options.
	 *
	 * @since   3.0.0
	 * @access  public
	 *
	 * @param   int $post_id The post ID.
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

		// check if this is a review post type. If it is, then make comment_status as 'open' (but override with a filter) so that
		// comments can be addeded to this. If this is not done, then review comment feature will not show the ability to add rating in the comment section.
		if ( 'wppr_review' === get_post_type( $post_id ) ) {
			remove_action( 'save_post', array( $this, 'editor_save' ) );
			wp_update_post( array( 'ID' => $post_id, 'comment_status' => apply_filters( 'wppr_cpt_comment_status', 'open' ) ) );
			add_action( 'save_post', array( $this, 'editor_save' ) );
		}

		$editor->save();
	}
}
