<?php
/**
 * SessionHelper.php
 *
 * This class manages session variables for the application.
 *
 * PHP version 5
 *
 * @category   Web Application
 * @package    Faculty Advancement Tracking System
 * @author     Geoffrey Pagel <geoffrey.pagel@northwestern.edu>
 * @version    SVN: $Id$
 */
namespace FATS\Helpers;

class SessionHelper
{
	private static $_instance;

	private function __construct()
	{
		if (!headers_sent())
		{
			session_start();
		}
	}

	/**
	 * Gets an instance of SessionHelper (starts the server session)
	 *
	 * @return SessionHelper
	 */
	public static function getInstance()
	{
		if (!isset(self::$_instance))
		{
			self::$_instance = new SessionHelper();
		}

		return self::$_instance;
	}

	/**
	 * Destroys the current session
	 */
	public static function destroySession()
	{
		$_SESSION = array();

		if (isset($_COOKIE[session_name()]))
		{
			setcookie(session_name(), '', time() - 42000, '/');
		}

		session_destroy();
		session_regenerate_id(true);
	}
}

?>