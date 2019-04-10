<?php
/**
 * Used for filtering autoloader files. Compatible with PHP 5.3 and 5.2
 *
 * Author:          Andrei Baicus <andrei@themeisle.com>
 * Created on:      18/07/2018
 *
 * @soundtrack D.O.A. (Death of Auto-Tune) - JAY-Z
 * @package    Hestia
 */

/**
 * Class Wppr_Recursive_Filter
 */
class Wppr_Recursive_Filter extends RecursiveFilterIterator {

	/**
	 * Callback function.
	 *
	 * @var string
	 */
	public $callback;

	/**
	 * Hestia_Recursive_Filter constructor.
	 *
	 * @param RecursiveIterator $iterator file iterator.
	 * @param callable          $callback callback function.
	 */
	public function __construct( RecursiveIterator $iterator, $callback ) {
		$this->callback = $callback;
		parent::__construct( $iterator );
	}

	/**
	 * Run the filter.
	 *
	 * @return bool
	 */
	public function accept() {
		$callback = $this->callback;

		return call_user_func_array( $callback, array( parent::current(), parent::key(), parent::getInnerIterator() ) );
	}

	/**
	 * Get the children from iterator.
	 *
	 * @return Wppr_Recursive_Filter|RecursiveFilterIterator
	 */
	public function getChildren() {
		return new self( $this->getInnerIterator()->getChildren(), $this->callback );
	}
}

