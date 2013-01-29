<?php
/**
 * DatabaseHelper.php
 *
 * This class manages the database connection for the application.
 *
 * PHP version 5
 *
 * @category   Web Application
 * @package    Faculty Advancement Tracking System
 * @author     Geoffrey Pagel <geoffrey.pagel@northwestern.edu>
 * @version    SVN: $Id$
 */
namespace FATS\Helpers;

require_once 'ApplicationHelper.php';

class DatabaseHelper
{
	private static $_instance;
	private static $_connection;

	private function __construct()
	{
		ApplicationHelper::init();

		self::$_connection = new \PDO(ApplicationHelper::getDbDSN(), ApplicationHelper::getDbUser(), ApplicationHelper::getDbPassword(), array(\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC));
	}

	/**
	 * Gets an instance of DatabaseHelper (initiates the database connection)
	 *
	 * @return DatabaseHelper
	 */
	public static function getInstance()
	{
		if (!isset(self::$_instance))
		{
			try
			{
				self::$_instance = new DatabaseHelper();
			}
			catch (\PDOException $x)
			{
				trigger_error('Database connection error: ' . $x->getMessage());
			}
		}

		return self::$_connection;
	}
}

?>