<?php
/**
 * ErrorManager.php
 *
 * This class manages user level error messages.
 *
 * PHP version 5
 *
 * @category   Web Application
 * @package    Faculty Advancement Tracking System
 * @author     Geoffrey Pagel <geoffrey.pagel@northwestern.edu>
 * @version    SVN: $Id$
 */
namespace FATS\Diagnostics;

class ErrorManager
{
	/**
	 * Sets the current error message
	 *
	 * @param $error
	 */
	public static function setError($error)
	{
		$_SESSION['error']['message'] = $error;
	}

	/**
	 * Gets the current error message
	 *
	 * @return mixed
	 */
	public static function getError()
	{
		return htmlentities($_SESSION['error']['message']);
	}

	/**
	 * Clears the last error message.
	 */
	public static function clearError()
	{
		unset($_SESSION['error']['message']);
	}

	/**
	 * Returns TRUE if the error message is set, FALSE otherwise
	 *
	 * @return bool
	 */
	public static function isError()
	{
		return isset($_SESSION['error']['message']);
	}
}

?>