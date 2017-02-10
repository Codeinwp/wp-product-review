<?php
/**
 * Review
 *
 * Model responsible for the reviews in WPPR.
 *
 * @package WPPR
 * @subpackage Models
 * @copyright Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WPPR_Review
 *
 * @since 3.0
 */
class WPPR_Review {

	/**
	 * The review ID.
	 *
	 * @var int $ID The review id.
	 */
	private $ID = 0;
	/**
	 * The overall score of the review.
	 *
	 * @var float $score The overall score of the review.
	 */
	private $score = 0;
	/**
	 * If the review is active or not.
	 *
	 * @var bool $is_active If the review is active or not.
	 */
	private $is_active = false;
	/**
	 * Array containg the list of pros for the review.
	 *
	 * @var array $pros The list of pros.
	 */
	private $pros = array();
	/**
	 * The array containg the list of cons for the review.
	 *
	 * @var array $cons The list of cons.
	 */
	private $cons = array();
	/**
	 * The review title.
	 *
	 * @var string $name The review title.
	 */
	private $name = '';
	/**
	 * The url of the image used in the review.
	 *
	 * @var array $image The urls of the images used.
	 */
	private $image = '';
	/**
	 * The click behaviour.
	 *
	 * @var string $click The click behaviour.
	 */
	private $click = '';
	/**
	 * The list of links as url=>link_title
	 *
	 * @var array $links The list of links from the review
	 */
	private $links = array();
	/**
	 * The price of the product reviewed.
	 *
	 * @var string $price The price of the product reviewed.
	 */
	private $price = '0.00';
	/**
	 * An array keeping the list of options for the product reviewed.
	 *
	 * @var array $options The options of the product reviewed.
	 */
	private $options = array();

	/**
	 * WPPR_Review constructor.
	 *
	 * @param mixed $review_id The review id.
	 */
	public function __construct( $review_id = false ) {
		if ( $review_id === false ) {
			wppr_error( 'No review id provided.' );

			return false;
		}
		if ( $this->check_post( $review_id ) ) {
			$this->ID = $review_id;
			wppr_notice( 'Checking review status for ID: ' . $review_id );
			$this->setup_status();
			if ( $this->is_active() ) {
				wppr_notice( 'Setting up review for ID: ' . $review_id );
				$this->setup_price();
				$this->setup_name();
				$this->setup_click();
				$this->setup_image();
				$this->setup_links();
				$this->setup_pros_cons();
				$this->setup_options();

				return true;
			} else {
				wppr_warning( 'Review is not active for this ID: ' . $review_id );

				return false;
			}
		} else {
			wppr_error( 'No post id found to attach this review.' );
		}

		return false;
	}

	/**
	 * Check if post record exists with that id.
	 *
	 * @param string $review_id The review id to check.
	 *
	 * @return bool Either post exists or not
	 */
	private function check_post( $review_id ) {
		return is_string( get_post_type( $review_id ) );
	}

	/**
	 *  Setup the review status.
	 */
	private function setup_status() {
		$status = get_post_meta( $this->ID, 'cwp_meta_box_check', true );
		if ( $status === 'Yes' ) {
			$this->is_active = true;
		} else {
			$this->is_active = false;
		}
	}

	/**
	 * Check if review is active or not.
	 *
	 * @return bool Return either the review is active or not.
	 */
	public function is_active() {
		return apply_filters( 'wppr_is_review_active', $this->is_active, $this->ID, $this );
	}

	/**
	 * Setup the price of the review.
	 */
	private function setup_price() {
		$price       = get_post_meta( $this->ID, 'cwp_rev_price', true );
		$price       = $this->format_price( $price );
		$this->price = $price;
	}

	/**
	 * Format a string to a price format.
	 *
	 * @param string $string The string for the price.
	 *
	 * @return string The formated price.
	 */
	private function format_price( $string ) {
		$price = preg_replace( '/[^0-9.,]/', '', $string );

		return floatval( $price );
	}

	/**
	 * Setup the name of the review.
	 */
	private function setup_name() {
		$name       = get_post_meta( $this->ID, 'cwp_rev_product_name', true );
		$this->name = $name;
	}

	/**
	 * Setup the link behaviour
	 */
	private function setup_click() {
		$click = get_post_meta( $this->ID, 'cwp_image_link', true );
		if ( $click === 'image' || $click === 'link' ) {
			$this->click = $click;
		}
	}

	/**
	 * Setup the image url.
	 */
	private function setup_image() {
		$image = get_post_meta( $this->ID, 'cwp_rev_product_image', true );
		if ( empty( $image ) ) {
			$image = wp_get_attachment_url( get_post_thumbnail_id( $this->ID ) );
		}
		$this->image = $image;
	}

	/**
	 * Setup the links array.
	 */
	private function setup_links() {
		$link_text                = get_post_meta( $this->ID, 'cwp_product_affiliate_text', true );
		$link_url                 = get_post_meta( $this->ID, 'cwp_product_affiliate_link', true );
		$this->links[ $link_url ] = $link_text;
		$link_text                = get_post_meta( $this->ID, 'cwp_product_affiliate_text2', true );
		$link_url                 = get_post_meta( $this->ID, 'cwp_product_affiliate_link2', true );
		$this->links[ $link_url ] = $link_text;
		$new_links                = get_post_meta( $this->ID, 'wpp_links', true );
		if ( ! empty( $new_links ) ) {
			$this->links = $new_links;
		}
	}

	/**
	 * Setup the pros and cons array.
	 */
	private function setup_pros_cons() {
		$options_nr = wppr_get_option( 'cwppos_option_nr' );
		$pros       = array();
		$cons       = array();
		for ( $i = 1; $i <= $options_nr; $i ++ ) {
			$tmp_pro = get_post_meta( $this->ID, 'cwp_option_' . $i . '_pro', true );
			$tmp_con = get_post_meta( $this->ID, 'cwp_option_' . $i . '_cons', true );
			if ( ! empty( $tmp_pro ) ) {
				$pros[] = $tmp_pro;
			}
			if ( ! empty( $tmp_con ) ) {
				$cons[] = $tmp_con;
			}
		}
		// New pros meta.
		$new_pros = get_post_meta( $this->ID, 'wppr_pros', true );
		if ( ! empty( $new_pros ) ) {
			$pros = $new_pros;
		}
		$this->pros = array_filter( $pros );
		// New cons meta.
		$new_cons = get_post_meta( $this->ID, 'wppr_cons', true );
		if ( ! empty( $new_cons ) ) {
			$cons = $new_cons;
		}
		$this->cons = array_filter( $cons );

	}

	/**
	 * Setup the options array.
	 */
	private function setup_options() {
		$options    = array();
		$options_nr = wppr_get_option( 'cwppos_option_nr' );
		for ( $i = 1; $i <= $options_nr; $i ++ ) {
			$tmp_name = get_post_meta( $this->ID, 'option_' . $i . '_content', true );
			if ( ! empty( $tmp ) ) {
				$tmp_score = get_post_meta( $this->ID, 'option_' . $i . '_grade', true );
				$options[] = array(
					'name'  => $tmp_name,
					'score' => $tmp_score,
				);
			}
		}
		$new_options = get_post_meta( $this->ID, 'wppr_options', true );
		if ( ! empty( $new_options ) ) {
			$options = $new_options;
		}
		$this->options = $options;
	}

	/**
	 * Return the options array of the review.
	 *
	 * @return array The options array.
	 */
	public function get_options() {
		return apply_filters( 'wppr_options', $this->options, $this->ID, $this );
	}

	/**
	 * Setter method for options.
	 *
	 * We update the options array if there is only a single component like :
	 *      array(
	 *          'name'=>'Review name',
	 *          'value'=>Option rating
	 *      )
	 * or the all options array if we get smth like:
	 *  array(
	 *      array(
	 *          'name'=>'Review name',
	 *          'value'=>Option rating
	 *      ),
	 *      array(
	 *          'name'=>'Review name',
	 *          'value'=>Option rating
	 *      )
	 *  )
	 *
	 * @param array $options The options array.
	 *
	 * @return bool If the options were updated or not.
	 */
	public function set_options( $options ) {
		if ( is_array( $options ) ) {
			$options = apply_filters( 'wppr_options_format', $options, $this->ID, $this );
			if ( isset( $options['name'] ) ) {
				/**
				 * Add options if the param is
				 * array(
				 *  'name'=>'Review name',
				 *  'value'=>Option rating
				 * )
				 */
				$this->options[] = $options;

				return update_post_meta( $this->ID, 'wppr_options', $this->options );
			} else {
				/**
				 * Update the all list of options.
				 */
				$this->options = $options;

				return update_post_meta( $this->ID, 'wppr_options', $this->options );

			}
		} else {
			wppr_error( 'Invalid value for options in review: ' . $this->ID );
		}

		return false;
	}

	/**
	 * Getter for the cons array.
	 *
	 * @return array The cons array of optons.
	 */
	public function get_cons() {
		return apply_filters( 'wppr_cons', $this->cons, $this->ID, $this );
	}

	/**
	 * Update the cons array.
	 *
	 * @param array|string $cons The cons array or string to add.
	 *
	 * @return bool Either the update was made or not.
	 */
	public function set_cons( $cons ) {
		$cons = apply_filters( 'wppr_cons_format', $cons, $this->ID, $this );
		if ( is_array( $cons ) ) {
			// We update the whole array.
			$this->cons = $cons;
			wppr_notice( 'Update cons array for ID . ' . $this->ID );

			return update_post_meta( $this->ID, 'wppr_cons', $this->cons );
		} else {
			// We add the text to the old array.
			$this->pros[] = $cons;
			wppr_notice( 'Adding cons option for ID . ' . $this->ID );

			return update_post_meta( $this->ID, 'wppr_cons', $this->cons );
		}

		return false;
	}

	/**
	 * Getter for the pros array.
	 *
	 * @return array The pros array of options.
	 */
	public function get_pros() {
		return apply_filters( 'wppr_pros', $this->pros, $this->ID, $this );
	}

	/**
	 * Update the pros array.
	 *
	 * @param array|string $pros The pros array or string to add.
	 *
	 * @return bool Either the update was made or not.
	 */
	public function set_pros( $pros ) {
		$pros = apply_filters( 'wppr_pros_format', $pros, $this->ID, $this );
		if ( is_array( $pros ) ) {
			// We update the whole array.
			$this->pros = $pros;
			wppr_notice( 'Update pros array for ID . ' . $this->ID );

			return update_post_meta( $this->ID, 'wppr_pros', $this->pros );
		} else {
			// We add the text to the old array.
			$this->pros[] = $pros;
			wppr_notice( 'Adding pros option for ID . ' . $this->ID );

			return update_post_meta( $this->ID, 'wppr_pros', $this->pros );
		}

		return false;
	}

	/**
	 * Return the list of links in url=>text format.
	 *
	 * @return array The list of links
	 */
	public function get_links() {
		return apply_filters( 'wppr_links', $this->links, $this->ID );

	}

	/**
	 * Save the links array ( url=>title ) to the postmeta.
	 *
	 * @param array $links The new links array.
	 *
	 * @return bool Either was saved or not.
	 */
	public function set_links( $links ) {
		$links = apply_filters( 'wppr_links_format', $links, $this->ID, $this );
		if ( is_array( $links ) ) {
			$this->links = $links;

			return update_post_meta( $this->ID, 'wppr_links', $links );
		} else {
			wppr_error( 'Review: ' . $this->ID . ' Invalid array for links, it should be url=>text' );
		}

		return false;
	}

	/**
	 * Return the url of the thumbnail.
	 *
	 * @return string The url of the small image.
	 */
	public function get_small_thumbnail() {
		global $wpdb;
		// filter for image size;
		$size        = apply_filters( 'wppr_review_image_size', 'thumbnail', $this->ID, $this );
		$attachment  = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid='%s';", $this->image ) );
		$image_id    = isset( $attachment[0] ) ? $attachment[0] : '';
		$image_thumb = '';
		if ( ! empty( $image_id ) ) {
			$image_thumb = wp_get_attachment_image_src( $image_id, $size );
			if ( $size !== 'thumbnail' ) {
				if ( $image_thumb[0] === $this->image ) {
					$image_thumb = wp_get_attachment_image_src( $image_id, 'thumbnail' );
				}
			}
		}

		return apply_filters( 'wppr_thumb', isset( $image_thumb[0] ) ? $image_thumb[0] : $this->image, $this->ID, $this );
	}

	/**
	 * Get the list of images for the review.
	 *
	 * @return array The list of images.
	 */
	public function get_image() {
		return apply_filters( 'wppr_images', $this->image, $this->ID, $this );
	}

	/**
	 * Set the new image url.
	 *
	 * @param string $image The new image url.
	 *
	 * @return bool Either was updated or not.
	 */
	public function set_image( $image ) {
		$image = apply_filters( 'wppr_image_format', $image, $this->ID, $this );
		if ( $image !== $this->image ) {
			$this->image = $image;

			return update_post_meta( $this->ID, 'cwp_rev_product_image', $image );
		} else {
			wppr_warning( 'Image already used for ID: ' . $this->ID );
		}

		return false;
	}

	/**
	 * Return the click behaviour.
	 *
	 * @return string The click behaviour.
	 */
	public function get_click() {
		return apply_filters( 'wppr_click', $this->click, $this->ID, $this );
	}

	/**
	 * Setter for click behaviour.
	 *
	 * @param string $click The new click behaviour.
	 *
	 * @return bool Either was saved or not.
	 */
	public function set_click( $click ) {
		if ( $click === 'image' || $click === 'link' ) {
			if ( $this->click != $click ) {
				$this->click = $click;

				return update_post_meta( $this->ID, 'cwp_image_link', $this->click );
			} else {
				wppr_warning( 'Value for click already set in ID: ' . $this->ID );
			}
		} else {
			wppr_warning( 'Wrong value for click on ID : ' . $this->ID );
		}

		return false;
	}

	/**
	 * Return the review name.
	 *
	 * @return string The review name.
	 */
	public function get_name() {
		return apply_filters( 'wppr_name', $this->name, $this->ID, $this );
	}

	/**
	 * Setter method for saving the review name.
	 *
	 * @param string $name The new review name.
	 *
	 * @return bool Either the review was saved or not.
	 */
	public function set_name( $name ) {
		$name = apply_filters( 'wppr_name_format', $name, $this->ID, $this );
		if ( $name !== $this->name ) {
			$this->name = $name;

			return update_post_meta( $this->ID, 'cwp_rev_product_name', $name );
		}

		return false;
	}

	/**
	 * Returns the review price.
	 *
	 * @return string The review price.
	 */
	public function get_price() {
		return apply_filters( 'wppr_price', $this->price, $this->ID, $this );
	}

	/**
	 * Setup the new price.
	 *
	 * @param string $price The new price.
	 *
	 * @return bool Either the price was saved or not.
	 */
	public function set_price( $price ) {
		$price = $this->format_price( $price );
		$price = apply_filters( 'wppr_price_format', $price, $this->ID, $this );
		if ( $price !== $this->price ) {
			$this->price = $price;

			return update_post_meta( $this->ID, 'cwp_rev_price', $price );
		} else {
			wppr_warning( 'Review: ' . $this->ID . ' price is the same.' );
		}

		return false;
	}

	/**
	 * Deactivate the review.
	 */
	public function deactivate() {
		if ( $this->is_active === false ) {
			wppr_warning( 'Review is already inactive for ID: ' . $this->ID );
		}
		$this->is_active = false;

		return update_post_meta( $this->ID, 'cwp_meta_box_check', 'No' );
	}

}
