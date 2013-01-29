<?php
/**
 * MessageManager.php
 *
 * This class manages user level informational messages.
 *
 * PHP version 5
 *
 * @category   Web Application
 * @package    Faculty Advancement Tracking System
 * @author     Geoffrey Pagel <geoffrey.pagel@northwestern.edu>
 * @version    SVN: $Id$
 */
namespace FATS\Diagnostics;

class MessageManager
{
	/**
	 * Sets the informational message
	 *
	 * @param $message
	 */
	public static function setMessage($message)
	{
		$_SESSION['info']['message'] = $message;
	}

	/**
	 * Gets the current informational message
	 *
	 * @return mixed
	 */
	public static function getMessage()
	{
		return htmlentities($_SESSION['info']['message']);
	}

	/**
	 * Clears the last informational message
	 */
	public static function clearMessage()
	{
		unset($_SESSION['info']['message']);
	}

	/**
	 * Returns TRUE if the informational message is set, FALSE otherwise
	 *
	 * @return bool
	 */
	public static function isMessageSet()
	{
		return isset($_SESSION['info']['message']);
	}
}

?>