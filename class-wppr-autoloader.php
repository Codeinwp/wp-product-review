<?php
/**
 * The file that defines autoload class
 *
 * A simple autoloader that loads class files recursively starting in the directory
 * where this class resides.  Additional options can be provided to control the naming
 * convention of the class files.
 *
 * @link        https://themeisle.com
 * @copyright   Copyright (c) 2017, Bogdan Preda
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 *
 * @since       1.0.0
 * @package     WPPR
 */

/**
 * The WPPR_Autoloader class.
 *
 * @since      3.0.0
 * @package    WPPR
 * @author     Themeisle <friends@themeisle.com>
 */
class WPPR_Autoloader {

	/**
	 * File extension as a string. Defaults to ".php".
	 *
	 * @since   3.0.0
	 * @access  protected
	 * @var     string $file_ext The file extension to look for.
	 */
	protected static $file_ext = '.php';

	/**
	 * The top level directory where recursion will begin. Defaults to the current
	 * directory.
	 *
	 * @since   3.0.0
	 * @access  protected
	 * @var     string $path_top The root directory.
	 */
	protected static $path_top = __DIR__;

	/**
	 * Holds an array of namespaces to filter in autoloading if set.
	 *
	 * @since   3.0.0
	 * @access  protected
	 * @var array $namespaces The namespace array, used if not empty on autoloading.
	 */
	protected static $namespaces = array();

	/**
	 * An array of files to exclude when looking to autoload.
	 *
	 * @since   3.0.0
	 * @access  protected
	 * @var     array $excluded_files The excluded files list.
	 */
	protected static $excluded_files = array();

	/**
	 * A placeholder to hold the file iterator so that directory traversal is only
	 * performed once.
	 *
	 * @since   3.0.0
	 * @access  protected
	 * @var     RecursiveIteratorIterator $file_iterator Holds an instance of the iterator class.
	 */
	protected static $file_iterator = null;

	/**
	 * Autoload function for registration with spl_autoload_register
	 *
	 * Looks recursively through project directory and loads class files based on
	 * filename match.
	 *
	 * @since   3.0.0
	 * @access  public
	 *
	 * @param   string $class_name The class name requested.
	 *
	 * @return mixed
	 */
	public static function loader( $class_name ) {

		if ( ! empty( static::$namespaces ) ) {
			$found = static::check_namespaces( $class_name );
			if ( ! $found ) {
				return $found;
			}
		}

		$directory = new RecursiveDirectoryIterator( static::$path_top . DIRECTORY_SEPARATOR . 'includes', RecursiveDirectoryIterator::SKIP_DOTS );

		if ( is_null( static::$file_iterator ) ) {
			$Iterator              = new RecursiveIteratorIterator( $directory );
			$Regex                 = new RegexIterator( $Iterator, '/^.+\.php$/i', RecursiveRegexIterator::MATCH );
			static::$file_iterator = iterator_to_array( $Regex, false );
		}

		$filename = 'class-' . str_replace( '_', '-', strtolower( $class_name ) ) . static::$file_ext;
		foreach ( static::$file_iterator as $file ) {
			if ( strtolower( $file->getFileName() ) === strtolower( $filename ) && is_readable( $file->getPathName() ) ) {
				require( $file->getPathName() );

				return true;
			}
		}
	}

	/**
	 * Method to check in allowed namespaces.
	 *
	 * @since   3.0.0
	 * @access  protected
	 *
	 * @param   string $class_name the class name to check with the namespaces.
	 *
	 * @return bool
	 */
	protected static function check_namespaces( $class_name ) {
		$found = false;
		foreach ( static::$namespaces as $namespace ) {
			if ( substr( $class_name, 0, strlen( $namespace ) ) == $namespace ) {
				$found = true;
			}
		}

		return $found;
	}

	/**
	 * Sets the $file_ext property
	 *
	 * @since   3.0.0
	 * @access  public
	 *
	 * @param   string $file_ext The file extension used for class files.  Default is "php".
	 */
	public static function set_file_ext( $file_ext ) {
		static::$file_ext = $file_ext;
	}

	/**
	 * Sets the $plugins_path property
	 *
	 * @since   3.0.0
	 * @access  public
	 *
	 * @param   string $path The path representing the top level where recursion should
	 *                       begin for plugins. Defaults to empty ( does not look in plugins ).
	 */
	public static function set_plugins_path( $path ) {
		static::$plugins_path = $path;
	}

	/**
	 * Sets the $path property
	 *
	 * @since   3.0.0
	 * @access  public
	 *
	 * @param   string $path The path representing the top level where recursion should
	 *                       begin. Defaults to the current directory.
	 */
	public static function set_path( $path ) {
		static::$path_top = $path;
	}

	/**
	 * Adds a new file to the exclusion list.
	 *
	 * @since   3.0.0
	 * @access  public
	 *
	 * @param   string $file_name The file name to exclude from autoload.
	 */
	public static function exclude_file( $file_name ) {
		static::$excluded_files[] = $file_name;
	}

	/**
	 * Sets the namespaces used in autoloading if any.
	 *
	 * @since   3.0.0
	 * @access  public
	 *
	 * @param   array $namespaces The namespaces to use.
	 */
	public static function define_namespaces( $namespaces = array() ) {
		static::$namespaces = $namespaces;
	}
}
