<?php
/**
 * ApplicationHelper.php
 *
 * This class manages application options.
 *
 * PHP version 5
 *
 * @category   Web Application
 * @package    Faculty Advancement Tracking System
 * @author     Geoffrey Pagel <geoffrey.pagel@northwestern.edu>
 * @version    SVN: $Id$
 */
namespace FATS\Helpers;

class ApplicationHelper
{
	private static $_maintenanceMode;
	private static $_demoMode;
	private static $_debugMode;
	private static $_dbUser;
	private static $_dbPassword;
	private static $_dbDSN;
	private static $_admins;

	/**
	 * Sets the database DSN
	 *
	 * @param $dbDSN
	 */
	public static function setDbDSN($dbDSN)
	{
		self::$_dbDSN = $dbDSN;
	}

	/**
	 * Gets the database DSN
	 *
	 * @return string
	 */
	public static function getDbDSN()
	{
		return self::$_dbDSN;
	}

	/**
	 * Sets the database password
	 *
	 * @param $dbPassword
	 */
	public static function setDbPassword($dbPassword)
	{
		self::$_dbPassword = $dbPassword;
	}

	/**
	 * Gets the database password
	 *
	 * @return string
	 */
	public static function getDbPassword()
	{
		return self::$_dbPassword;
	}

	/**
	 * Sets the database user
	 *
	 * @param $dbUser
	 */
	public static function setDbUser($dbUser)
	{
		self::$_dbUser = $dbUser;
	}

	/**
	 * Gets the database user
	 *
	 * @return string
	 */
	public static function getDbUser()
	{
		return self::$_dbUser;
	}

	/**
	 * Initializes the application options
	 */
	public static function init()
	{
		$filename = '/var/www/sites/fats/src/application.xml';

		if (!file_exists($filename))
		{
			trigger_error("Application options file ({$filename}) not found or is unreadable.", E_USER_ERROR);
			header('Location: /', true);
			exit;
		}

		$xml = simplexml_load_file($filename);

		if (!empty($xml))
		{
			self::$_maintenanceMode = strval($xml->options->enablemaintenancemode);
			self::$_demoMode = strval($xml->options->enabledemomode);
			self::$_debugMode = strval($xml->options->enabledebugging);
			self::$_dbUser = strval($xml->database->user);
			self::$_dbPassword = strval($xml->database->password);
			self::$_dbDSN = strval($xml->database->dsn);
			self::$_admins = $xml->admins;
		}
	}

	/**
	 * Returns TRUE if maintenance mode is enabled, FALSE otherwise.
	 *
	 * @return mixed
	 */
	public static function isMaintenanceMode()
	{
		self::init();

		if (self::$_maintenanceMode)
		{
			trigger_error('The system is currently offline for maintenance.', E_USER_NOTICE);
		}

		return self::$_maintenanceMode;
	}

	/**
	 * @return mixed
	 */
	public static function isDemoMode()
	{
		self::init();

		return self::$_demoMode;
	}

	/**
	 * Returns TRUE if debug mode is enabled, FALSE otherwise.
	 *
	 * @return mixed
	 */
	public static function isDebugMode()
	{
		self::init();

		return self::$_debugMode;
	}

	/**
	 * Returns TRUE if the supplied NetID has administrative access to the system, FALSE otherwise
	 *
	 * @param $netid
	 * @return bool
	 */
	public static function isAdministrator($netid)
	{
		self::init();

		$adminAccounts = array();

		foreach (self::$_admins->children() as $account)
		{
			array_push($adminAccounts, strval($account));
		}

		return in_array($netid, $adminAccounts);
	}
}

?>