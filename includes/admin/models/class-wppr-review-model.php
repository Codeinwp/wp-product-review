<?php
/**
 * Model responsible for the reviews in WPPR.
 *
 * @package     WPPR
 * @subpackage  Models
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0.0
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
class WPPR_Review_Model extends WPPR_Model_Abstract {

	/**
	 * The review ID.
	 *
	 * @since   3.0.0
	 * @access  private
	 * @var int $ID The review id.
	 */
	private $ID = 0;

	/**
	 * The overall score of the review.
	 *
	 * @since   3.0.0
	 * @access  private
	 * @var float $score The overall score of the review.
	 */
	private $score = 0;

	/**
	 * If the review is active or not.
	 *
	 * @since   3.0.0
	 * @access  private
	 * @var bool $is_active If the review is active or not.
	 */
	private $is_active = false;

	/**
	 * Array containg the list of pros for the review.
	 *
	 * @since   3.0.0
	 * @access  private
	 * @var array $pros The list of pros.
	 */
	private $pros = array();

	/**
	 * The array containg the list of cons for the review.
	 *
	 * @since   3.0.0
	 * @access  private
	 * @var array $cons The list of cons.
	 */
	private $cons = array();

	/**
	 * The review title.
	 *
	 * @since   3.0.0
	 * @access  private
	 * @var string $name The review title.
	 */
	private $name = '';

	/**
	 * The url of the image used in the review.
	 *
	 * @since   3.0.0
	 * @access  private
	 * @var array $image The urls of the images used.
	 */
	private $image = '';

	/**
	 * The click behaviour.
	 *
	 * @since   3.0.0
	 * @access  private
	 * @var string $click The click behaviour.
	 */
	private $click = '';

	/**
	 * The list of links as url=>link_title
	 *
	 * @since   3.0.0
	 * @access  private
	 * @var array $links The list of links from the review
	 */
	private $links = array();

	/**
	 * The price of the product reviewed.
	 *
	 * @since   3.0.0
	 * @access  private
	 * @var string $price The price of the product reviewed.
	 */
	private $price = '0.00';
	/**
	 * The price raw of the product reviewed.
	 *
	 * @since   3.0.0
	 * @access  private
	 * @var string $price The price raw of the product reviewed containg currency and value.
	 */
	private $price_raw = '0.00';

	/**
	 * The price currency of the product reviewed.
	 *
	 * @since   3.0.0
	 * @access  private
	 * @var string $price The currency of the product reviewed.
	 */
	private $currency = '$';

	/**
	 * An array keeping the list of options for the product reviewed.
	 *
	 * @since   3.0.0
	 * @access  private
	 * @var array $options The options of the product reviewed.
	 */
	private $options = array();

	/**
	 * The review template.
	 *
	 * @access  private
	 * @var string $name The review template.
	 */
	private $template = 'default';

	/**
	 * WPPR_Review constructor.
	 *
	 * @since   3.0.0
	 * @access  public
	 *
	 * @param mixed $review_id The review id.
	 */
	public function __construct( $review_id = false ) {
		parent::__construct();

		if ( $review_id === false ) {
			$this->logger->error( 'No review id provided.' );

			return false;
		}
		if ( $this->check_post( $review_id ) ) {
			$this->ID = $review_id;
			$this->logger->notice( 'Checking review status for ID: ' . $review_id );
			$this->setup_status();
			if ( $this->is_active() ) {
				$this->logger->notice( 'Setting up review for ID: ' . $review_id );
				$this->setup_cpt();
				$this->setup_price();
				$this->setup_name();
				$this->setup_template();
				$this->setup_click();
				$this->setup_image();
				$this->setup_links();
				$this->setup_pros_cons();
				$this->setup_options();
				$this->count_rating();
				if ( ! is_admin() ) {
					$this->alter_options();
				}

				$this->backward_compatibility();

				return true;
			} else {
				$this->logger->warning( 'Review is not active for this ID: ' . $review_id );

				return false;
			}
		} else {
			$this->logger->error( 'No post id found to attach this review.' );
		}

		return false;
	}

	/**
	 * Setup hooks if this review is a CPT.
	 */
	private function setup_cpt() {
		if ( 'wppr_review' === get_post_type( $this->ID ) ) {
			add_filter( 'wppr_name', array( $this, 'get_name_for_cpt' ), 10, 2 );
		}
	}

	/**
	 * If this is a CPT, use the post title as the product name.
	 */
	public function get_name_for_cpt( $name, $id ) {
		return get_the_title( $id );
	}

	/**
	 * Check if post record exists with that id.
	 *
	 * @since   3.0.0
	 * @access  private
	 *
	 * @param string $review_id The review id to check.
	 *
	 * @return bool
	 */
	private function check_post( $review_id ) {
		return is_string( get_post_type( $review_id ) );
	}

	/**
	 * Setup the review status.
	 *
	 * @since   3.0.0
	 * @access  private
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
	 * @since   3.0.0
	 * @access  public
	 * @return bool
	 */
	public function is_active() {
		return apply_filters( 'wppr_is_review_active', $this->is_active, $this->ID, $this );
	}

	/**
	 * Setup the price of the review.
	 *
	 * @since   3.0.0
	 * @access  private
	 */
	private function setup_price() {
		$price           = get_post_meta( $this->ID, 'cwp_rev_price', true );
		$this->price_raw = $price;
		$currency        = $this->format_currency( $price );
		$price           = $this->format_price( $price );
		$this->price     = $price;
		$this->currency  = $currency;
	}

	/**
	 * Format a string to a currency format.
	 *
	 * @since   3.0.0
	 * @access  private
	 *
	 * @param string $string The currency for the price.
	 *
	 * @return string
	 */
	private function format_currency( $string ) {
		$currency = preg_replace( '/[0-9.,]/', '', $string );

		return $currency;
	}

	/**
	 * Format a string to a price format.
	 *
	 * @since   3.0.0
	 * @access  private
	 *
	 * @param string $string The string for the price.
	 *
	 * @return string
	 */
	private function format_price( $string ) {
		$price = preg_replace( '/[^0-9.,]/', '', $string );

		return floatval( $price );
	}

	/**
	 * Setup the name of the review.
	 *
	 * @since   3.0.0
	 * @access  private
	 */
	private function setup_name() {
		$name       = get_post_meta( $this->ID, 'cwp_rev_product_name', true );
		$this->name = $name;
	}

	/**
	 * Setup the template of the review.
	 *
	 * @access  private
	 */
	private function setup_template() {
		$template = get_post_meta( $this->ID, '_wppr_review_template', true );
		if ( empty( $template ) ) {
			$template = 'default';
		}
		$this->template = $template;
	}

	/**
	 * Setup the link behaviour
	 *
	 * @since   3.0.0
	 * @access  private
	 */
	private function setup_click() {
		$click = get_post_meta( $this->ID, 'cwp_image_link', true );
		if ( $click === 'image' || $click === 'link' ) {
			$this->click = $click;
		}
	}

	/**
	 * Setup the image url.
	 *
	 * @since   3.0.0
	 * @access  private
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
	 *
	 * @since   3.0.0
	 * @access  private
	 */
	private function setup_links() {
		$link_text                 = get_post_meta( $this->ID, 'cwp_product_affiliate_text', true );
		$link_url                  = get_post_meta( $this->ID, 'cwp_product_affiliate_link', true );
		$this->links[ $link_text ] = $link_url;
		$link_text                 = get_post_meta( $this->ID, 'cwp_product_affiliate_text2', true );
		$link_url                  = get_post_meta( $this->ID, 'cwp_product_affiliate_link2', true );
		$this->links[ $link_text ] = $link_url;
		$new_links                 = get_post_meta( $this->ID, 'wppr_links', true );
		if ( ! empty( $new_links ) ) {
			$this->links = $new_links;
		}
	}

	/**
	 * Setup the pros and cons array.
	 *
	 * @since   3.0.0
	 * @access  private
	 */
	private function setup_pros_cons() {
		$options_nr = $this->wppr_get_option( 'cwppos_option_nr' );
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
	 *
	 * @since   3.0.0
	 * @access  private
	 */
	private function setup_options() {
		$options    = array();
		$options_nr = $this->wppr_get_option( 'cwppos_option_nr' );
		for ( $i = 1; $i <= $options_nr; $i ++ ) {
			$tmp_name = get_post_meta( $this->ID, 'option_' . $i . '_content', true );
			if ( $tmp_name != '' ) {
				$tmp_score     = get_post_meta( $this->ID, 'option_' . $i . '_grade', true );
				$options[ $i ] = array(
					'name'  => $tmp_name,
					'value' => $tmp_score,
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
	 * Calculate the review rating.
	 *
	 * @since   3.0.0
	 * @access  public
	 */
	public function count_rating() {
		$values      = wp_list_pluck( $this->options, 'value' );
		$this->score = ( count( $this->options ) > 0 ) ? floatval( array_sum( $values ) / count( $this->options ) ) : 0;

		update_post_meta( $this->ID, 'wppr_rating', number_format( $this->score, 2 ) );
	}

	/**
	 * Alter options based on user influence.
	 *
	 * @access  private
	 */
	private function alter_options() {
		$comment_influence = intval( $this->wppr_get_option( 'cwppos_infl_userreview' ) );

		if ( 0 === $comment_influence ) {
			return;
		}

		$comments = $this->get_comments_options();
		if ( ! $comments ) {
			return;
		}

		$combined = array();
		foreach ( $comments as $comment ) {
			$array = wp_list_pluck( $comment['options'], 'value', 'name' );
			foreach ( $array as $k => $v ) {
				if ( ! isset( $combined[ $k ] ) ) {
					$combined[ $k ] = floatval( $v );
				} else {
					$combined[ $k ] += floatval( $v );
				}
			}
		}
		$new_options = array();
		foreach ( $this->options as $index => $option ) {
			$k             = $option['name'];
			$rating        = $option['value'];
			$v             = floatval( $combined [ $k ] ) / count( $comments );
			$weighted      = $v * 10 * ( $comment_influence / 100 ) + floatval( $rating ) * ( ( 100 - $comment_influence ) / 100 );
			$new_options[ $index ] = array(
				'name'  => $k,
				'value' => $weighted,
			);
		}

		$this->options = $new_options;
	}

	/**
	 * Get all comments associated with the review.
	 *
	 * @return array|int The list of comments..
	 */
	public function get_comments_options() {
		if ( $this->ID === 0 ) {
			$this->logger->error( 'Can not get comments rating, id is not set' );

			return array();
		}
		$comments_query = new WP_Comment_Query;
		$comments       = $comments_query->query(
			array(
				'fields'  => 'ids',
				'status'  => 'approve',
				'post_id' => $this->ID,
			)
		);
		$valid          = array();
		foreach ( $comments as $comment ) {
			$options = $this->get_comment_options( $comment );
			if ( ! empty( $options ) ) {
				$valid[ $comment ] = array(
					'options' => $options,
					'date'    => get_comment_date( '', $comment ),
					'author'  => get_comment_author( $comment ),
					'title'   => wp_strip_all_tags( get_comment_excerpt( $comment ) ),
					'content' => get_comment_text( $comment ),
				);
			}
		}

		return $valid;
	}

	/**
	 * Return the options values and names associated with the comment.
	 *
	 * @since   3.0.0
	 * @access  public
	 *
	 * @param   int $comment_id The comment id.
	 *
	 * @return array
	 */
	public function get_comment_options( $comment_id ) {
		$options = array();
		if ( $this->wppr_get_option( 'cwppos_show_userreview' ) === 'yes' ) {
			$options_names   = wp_list_pluck( $this->options, 'name' );
			$comment_options = array();
			$valid_comment   = false;
			foreach ( $options_names as $k => $name ) {
				$value = get_comment_meta( $comment_id, 'meta_option_' . $k, true );

				$comment_options[] = array(
					'name'  => $name,
					'value' => number_format( (float) $value, 2 ),
				);
				if ( is_numeric( $value ) ) {
					$valid_comment = true;
				}
			}
			if ( ! $valid_comment ) {
				return array();
			}

			$options = $comment_options;
		}

		return $options;

	}

	/**
	 * Add backward compatibility so that when a review is viewed, its meta data can be updated.
	 *
	 * @access  private
	 */
	private function backward_compatibility() {
		$comment_influence = intval( $this->wppr_get_option( 'cwppos_infl_userreview' ) );

		if ( 0 === $comment_influence ) {
			return;
		}
		$comment_ratings = get_post_meta( $this->ID, 'wppr_comment_rating', true );
		if ( empty( $comment_ratings ) ) {
			update_post_meta( $this->ID, 'wppr_comment_rating', $this->get_comments_rating() );
		}
	}

	/**
	 * Get comments rating.
	 *
	 * @since   3.0.0
	 * @access  public
	 * @return float|int
	 */
	public function get_comments_rating() {
		$comments = $this->get_comments_options();
		if ( $comments ) {
			$options = array();
			foreach ( $comments as $comment ) {
				$options = array_merge( $options, $comment['options'] );
			}

			if ( count( $options ) != 0 ) {
				return ( array_sum( wp_list_pluck( $options, 'value' ) ) / count( $options ) );
			} else {
				return 0;
			}
		} else {
			return 0;
		}

	}

	/**
	 * Update comments rating.
	 *
	 * @access public
	 */
	public function update_comments_rating() {
		$comment_influence = intval( $this->wppr_get_option( 'cwppos_infl_userreview' ) );

		if ( 0 === $comment_influence ) {
			return;
		}

		update_post_meta( $this->get_ID(), 'wppr_comment_rating', $this->get_comments_rating() );
	}

	/**
	 * Return the review id.
	 *
	 * @since   3.0.0
	 * @access  public
	 * @return int
	 */
	public function get_ID() {
		return $this->ID;
	}

	/**
	 * Deactivate the review.
	 *
	 * @since   3.0.0
	 * @access  public
	 */
	public function deactivate() {
		if ( $this->is_active === false ) {
			$this->logger->warning( 'Review is already inactive for ID: ' . $this->ID );
		}

		$this->is_active = apply_filters( 'wppr_review_change_status', false, $this->ID, $this );

		do_action( 'wppr_review_deactivate', $this->ID, $this );

		return update_post_meta( $this->ID, 'cwp_meta_box_check', 'No' );
	}

	/**
	 * Activate the review.
	 *
	 * @since   3.0.0
	 * @access  public
	 */
	public function activate() {
		if ( $this->is_active === true ) {
			$this->logger->warning( 'Review is already active for ID: ' . $this->ID );
		}

		$this->is_active = apply_filters( 'wppr_review_change_status', true, $this->ID, $this );
		do_action( 'wppr_review_activate', $this->ID, $this );

		return update_post_meta( $this->ID, 'cwp_meta_box_check', 'Yes' );
	}

	/**
	 * Method to retrieve the review model data as an array.
	 *
	 * @since   3.0.0
	 * @access  public
	 * @return array
	 */
	public function get_review_data() {
		$data = array(
			'id'             => $this->get_ID(),
			'name'           => $this->get_name(),
			'template'       => $this->get_template(),
			'price'          => $this->get_price(),
			'price_raw'      => $this->get_price_raw(),
			'currency'       => $this->get_currency(),
			'click'          => $this->get_click(),
			'image'          => array(
				'full'  => $this->get_image(),
				'thumb' => $this->get_small_thumbnail(),
			),
			'rating'         => $this->get_rating(),
			'comment_rating' => $this->get_comments_rating(),
			'pros'           => $this->get_pros(),
			'cons'           => $this->get_cons(),
			'options'        => $this->get_options(),
			'links'          => $this->get_links(),
		);

		return $data;
	}

	/**
	 * Return the review name.
	 *
	 * @since   3.0.0
	 * @access  public
	 * @return string
	 */
	public function get_name() {
		return apply_filters( 'wppr_name', $this->name, $this->ID, $this );
	}

	/**
	 * Filter to display review product name in comparison table
	 *
	 * @since   3.4.3
	 * @access  public
	 * @return bool
	 */
	public function hide_name() {
		return apply_filters( 'wppr_hide_product_name', $this->name, $this->ID, $this );
	}
	/**
	 * Return the review template.
	 *
	 * @access  public
	 * @return string
	 */
	public function get_template() {
		return apply_filters( 'wppr_template', $this->template, $this->ID, $this );
	}

	/**
	 * Setter method for saving the review name.
	 *
	 * @since   3.0.0
	 * @access  public
	 *
	 * @param   string $name The new review name.
	 *
	 * @return bool
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
	 * Setter method for saving the review template.
	 *
	 * @access  public
	 *
	 * @param   string $template The new review template.
	 *
	 * @return bool
	 */
	public function set_template( $template ) {
		$this->template = $template;

		return update_post_meta( $this->ID, '_wppr_review_template', $template );

	}

	/**
	 * Returns the review price.
	 *
	 * @since   3.0.0
	 * @access  public
	 * @return string
	 */
	public function get_price() {
		return apply_filters( 'wppr_price', $this->price, $this->ID, $this );
	}

	/**
	 * Setup the new price.
	 *
	 * @since   3.0.0
	 * @access  public
	 *
	 * @param   string $price The new price.
	 *
	 * @return bool
	 */
	public function set_price( $price ) {
		$price = apply_filters( 'wppr_price_raw', $price, $this->ID, $this );
		if ( $price !== $this->price_raw ) {
			$this->price_raw = $price;

			$update = update_post_meta( $this->ID, 'cwp_rev_price', $price );

			$this->setup_price();

			return $update;
		} else {
			$this->logger->warning( 'Review: ' . $this->ID . ' price is the same.' );
		}

		return false;
	}

	/**
	 * Returns the raw price.
	 *
	 * @since   3.0.0
	 * @access  public
	 * @return string
	 */
	public function get_price_raw() {
		return apply_filters( 'wppr_price_raw', $this->price_raw, $this->ID, $this );
	}

	/**
	 * Returns the currency price.
	 *
	 * @since   3.0.0
	 * @access  public
	 * @return string
	 */
	public function get_currency() {
		return apply_filters( 'wppr_currency_code', apply_filters( 'wppr_currency', empty( $this->currency ) ? '$' : $this->currency, $this->ID, $this ) );
	}

	/**
	 * Return the click behaviour.
	 *
	 * @since   3.0.0
	 * @access  public
	 * @return string
	 */
	public function get_click() {
		return apply_filters( 'wppr_click', $this->click, $this->ID, $this );
	}

	/**
	 * Setter for click behaviour.
	 *
	 * @since   3.0.0
	 * @access  public
	 *
	 * @param string $click The new click behaviour.
	 *
	 * @return bool
	 */
	public function set_click( $click ) {
		if ( $click === 'image' || $click === 'link' ) {
			if ( $this->click != $click ) {
				$this->click = $click;

				return update_post_meta( $this->ID, 'cwp_image_link', $this->click );
			} else {
				$this->logger->warning( 'Value for click already set in ID: ' . $this->ID );
			}
		} else {
			$this->logger->warning( 'Wrong value for click on ID : ' . $this->ID );
		}

		return false;
	}

	/**
	 * Get the list of images for the review.
	 *
	 * @since   3.0.0
	 * @access  public
	 * @return array
	 */
	public function get_image() {
		return apply_filters( 'wppr_images', $this->image, $this->ID, $this );
	}

	/**
	 * Set the new image url.
	 *
	 * @since   3.0.0
	 * @access  public
	 *
	 * @param   string $image The new image url.
	 *
	 * @return bool
	 */
	public function set_image( $image ) {
		$image = apply_filters( 'wppr_image_format', $image, $this->ID, $this );
		if ( $image !== $this->image ) {
			$this->image = $image;

			return update_post_meta( $this->ID, 'cwp_rev_product_image', $image );
		} else {
			$this->logger->warning( 'Image already used for ID: ' . $this->ID );
		}

		return false;
	}

	/**
	 * Return the review image ID.
	 *
	 * @since   3.4.3
	 * @access  public
	 * @return int
	 */
	public function get_image_id() {
		global $wpdb;
		$attachment  = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid=%s", $this->image ) );
		$image_id    = isset( $attachment[0] ) ? $attachment[0] : '';
		return $image_id;
	}

	/**
	 * Return the url of the thumbnail.
	 *
	 * @since   3.0.0
	 * @access  public
	 * @return string
	 */
	public function get_small_thumbnail() {
		// filter for image size;
		$size        = apply_filters( 'wppr_review_image_size', 'thumbnail', $this->ID, $this );
		$image_thumb = '';
		$image_id = $this->get_image_id();
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
	 * Return the review image's alt text.
	 *
	 * @since   3.4.3
	 * @access  public
	 * @return string
	 */
	public function get_image_alt() {
		$image_id = $this->get_image_id();
		if ( empty( $image_id ) ) {
			return $this->get_name();
		}
		$alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
		if ( empty( $alt ) ) {
			return $this->get_name();
		}
		return $alt;
	}

	/**
	 * Return the rating of the review.
	 *
	 * @since   3.0.0
	 * @access  public
	 * @return float
	 */
	public function get_rating() {
		$comment_influence = intval( $this->wppr_get_option( 'cwppos_infl_userreview' ) );

		$rating = $this->score;
		if ( $comment_influence > 0 ) {
			$comments_rating = $this->get_comments_rating();
			if ( $comments_rating > 0 ) {
				$rating = $comments_rating * 10 * ( $comment_influence / 100 ) + $rating * ( ( 100 - $comment_influence ) / 100 );
			}
		}

		do_action( 'themeisle_log_event', WPPR_SLUG, sprintf( 'rating %d becomes %d with user influence of %d', $this->score, $rating, $comment_influence ), 'debug', __FILE__, __LINE__ );

		return apply_filters( 'wppr_rating', $rating, $this->ID, $this );
	}

	/**
	 * Getter for the pros array.
	 *
	 * @since   3.0.0
	 * @access  public
	 * @return array
	 */
	public function get_pros() {
		return apply_filters( 'wppr_pros', $this->pros, $this->ID, $this );
	}

	/**
	 * Update the pros array.
	 *
	 * @since   3.0.0
	 * @access  public
	 *
	 * @param   array|string $pros The pros array or string to add.
	 *
	 * @return bool
	 */
	public function set_pros( $pros ) {
		$pros = apply_filters( 'wppr_pros_format', $pros, $this->ID, $this );
		if ( is_array( $pros ) ) {
			// We update the whole array.
			$this->pros = $pros;
			$this->logger->notice( 'Update pros array for ID . ' . $this->ID );

			return update_post_meta( $this->ID, 'wppr_pros', $this->pros );
		} else {
			// We add the text to the old array.
			$this->pros[] = $pros;
			$this->logger->notice( 'Adding pros option for ID . ' . $this->ID );

			return update_post_meta( $this->ID, 'wppr_pros', $this->pros );
		}
	}

	/**
	 * Getter for the cons array.
	 *
	 * @since   3.0.0
	 * @access  public
	 * @return array
	 */
	public function get_cons() {
		return apply_filters( 'wppr_cons', $this->cons, $this->ID, $this );
	}

	/**
	 * Update the cons array.
	 *
	 * @since   3.0.0
	 * @access  public
	 *
	 * @param   array|string $cons The cons array or string to add.
	 *
	 * @return bool
	 */
	public function set_cons( $cons ) {
		$cons = apply_filters( 'wppr_cons_format', $cons, $this->ID, $this );
		if ( is_array( $cons ) ) {
			// We update the whole array.
			$this->cons = $cons;
			$this->logger->notice( 'Update cons array for ID . ' . $this->ID );

			return update_post_meta( $this->ID, 'wppr_cons', $this->cons );
		} else {
			// We add the text to the old array.
			$this->pros[] = $cons;
			$this->logger->notice( 'Adding cons option for ID . ' . $this->ID );

			return update_post_meta( $this->ID, 'wppr_cons', $this->cons );
		}

	}

	/**
	 * Return the options array of the review.
	 *
	 * @since   3.0.0
	 * @access  public
	 * @return array
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
	 * @since   3.0.0
	 * @access  public
	 *
	 * @param   array $options The options array.
	 *
	 * @return bool
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
				$this->count_rating();

				return update_post_meta( $this->ID, 'wppr_options', $this->options );
			} else {
				/**
				 * Update the all list of options.
				 */
				$this->options = $options;
				$this->count_rating();

				return update_post_meta( $this->ID, 'wppr_options', $this->options );

			}
		} else {
			$this->logger->error( 'Invalid value for options in review: ' . $this->ID );
		}

		return false;
	}

	/**
	 * Return the list of links in url=>text format.
	 *
	 * @since   3.0.0
	 * @access  public
	 * @return array
	 */
	public function get_links() {
		return apply_filters( 'wppr_links', $this->links, $this->ID );

	}

	/**
	 * Save the links array ( url=>title ) to the postmeta.
	 *
	 * @since   3.0.0
	 * @access  public
	 *
	 * @param   array $links The new links array.
	 *
	 * @return bool Either was saved or not.
	 */
	public function set_links( $links ) {
		$links = apply_filters( 'wppr_links_format', $links, $this->ID, $this );
		if ( is_array( $links ) ) {
			$this->links = $links;

			return update_post_meta( $this->ID, 'wppr_links', $links );
		} else {
			$this->logger->error( 'Review: ' . $this->ID . ' Invalid array for links, it should be url=>text' );
		}

		return false;
	}

	/**
	 * Returns the JSON-LD array.
	 *
	 * @return array The JSON-LD array.
	 */
	public function get_json_ld() {
		$ld           = array(
			'@context'    => 'http://schema.org/',
			'@type'       => 'Product',
			'name'        => $this->get_name(),
			'image'       => $this->get_small_thumbnail(),
			'description' => $this->get_excerpt(),
		);
		$ld['offers'] = array(
			'@type'         => 'Offer',
			'price'         => number_format( $this->get_price(), 2, '.', '' ),
			'priceCurrency' => $this->get_currency(),
			'seller'        => array(
				'@type' => 'Person',
				'name'  => $this->get_author(),
			),
		);

		$review_default = array(
			'@type'         => 'Review',
			'reviewRating'  => array(
				'@type'       => 'Rating',
				'bestRating'  => '10',
				'worstRating' => '0',
				'ratingValue' => number_format( ( $this->get_rating() / 10 ), 2 ),
			),
			'name'          => $this->get_name(),
			'reviewBody'    => $this->get_content(),
			'author'        => array(
				'@type' => 'Person',
				'name'  => $this->get_author(),
			),
			'datePublished' => get_the_time( 'Y-m-d', $this->get_ID() ),
		);

		if ( $this->wppr_get_option( 'cwppos_show_userreview' ) != 'yes' ) {
			$ld['review'] = $review_default;

			return $ld;
		}
		$ld['review'][] = $review_default;

		$comments = $this->get_comments_options();
		foreach ( $comments as $comment ) {
			$ld['review'][] = array(
				'@type'         => 'Review',
				'reviewRating'  => array(
					'@type'       => 'Rating',
					'bestRating'  => '10',
					'worstRating' => '0',
					'ratingValue' => number_format( ( $this->rating_by_options( $comment['options'] ) ), 2 ),
				),
				'name'          => $comment['title'],
				'reviewBody'    => $comment['content'],
				'author'        => array(
					'@type' => 'Person',
					'name'  => $comment['author'],
				),
				'datePublished' => get_the_time( 'Y-m-d', $comment['date'] ),
			);
		}
		$ld['aggregateRating'] = array(
			'@type'       => 'AggregateRating',
			'bestRating'  => '10',
			'worstRating' => '0',
			'ratingValue' => number_format( ( $this->get_rating() / 10 ), 2 ),
			'reviewCount' => count( $ld['review'] ),
		);

		return apply_filters( 'wppr_schema', $ld, $this );
	}

	/**
	 * Returns the excerpt of the description
	 *
	 * @return string The excerpt of description.
	 */
	public function get_excerpt() {
		if ( ! $this->is_active() ) {
			return '';
		}
		$content = $this->get_content();
		$content = strip_shortcodes( $content );

		$excerpt_length = apply_filters( 'wppr_excerpt_length', 55 );

		return wp_trim_words( $content, $excerpt_length, '...' );
	}

	/**
	 * Get the review post content.
	 *
	 * @return string The review post content.
	 */
	public function get_content() {
		if ( ! $this->is_active() ) {
			return '';
		}
		$content = get_post_field( 'post_content', $this->get_ID() );
		if ( empty( $content ) ) {
			return '';
		}

		$content = wp_strip_all_tags( strip_shortcodes( $content ) );

		return apply_filters( 'wppr_content', $content, $this->ID, $this );
	}

	/**
	 * Get the review author.
	 */
	public function get_author() {
		if ( ! $this->is_active() ) {
			return '';
		}

		$author_id = get_post_field( 'post_author', $this->get_ID() );

		return get_the_author_meta( 'display_name', $author_id );
	}

	/**
	 * Calculate rating by options pair.
	 *
	 * @param array $options Options pair.
	 *
	 * @return float|int The rating by options pairs.
	 */
	public function rating_by_options( $options ) {
		if ( empty( $options ) ) {
			return 0;
		}

		return ( array_sum( wp_list_pluck( $options, 'value' ) ) / count( $options ) );

	}

	/**
	 * Return css class based on the rating.
	 *
	 * @return string CSS class for the rating.
	 */
	public function get_rating_class( $value = - 1 ) {
		$element = ( $value < 0 ) ? $this->get_rating() : $value;
		if ( $element >= 75 ) {
			return 'wppr-very-good';
		} elseif ( $element < 75 && $element >= 50 ) {
			return 'wppr-good';
		} elseif ( $element < 50 && $element >= 25 ) {
			return 'wppr-not-bad';
		} else {
			return 'wppr-weak';
		}
	}
}
