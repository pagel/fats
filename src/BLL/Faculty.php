<?php
/**
 * Faculty.php
 *
 * This class provides methods for creating, reading, updating and deleting faculty members.
 *
 * PHP version 5
 *
 * @category   Web Application
 * @package    Faculty Advancement Tracking System
 * @author     Geoffrey Pagel <geoffrey.pagel@northwestern.edu>
 * @version    SVN: $Id$
 */
namespace FATS\BLL;

require_once 'Helpers/DirectoryHelper.php';
require_once 'DAL/DataAccessLayer.php';
require_once 'DAL/DataTransactionMonitor.php';
require_once 'Library/Observable.php';
require_once 'Library/Observer.php';

class Faculty extends \FATS\Library\Observable
{
	/**
	 * Faculty member ID
	 *
	 * @var int
	 */
	public $id;

	/**
	 * Faculty member NetID
	 *
	 * @var string
	 */
	public $netid;

	/**
	 * Faculty member full name
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Initializes a new instance of the Faculty class
	 *
	 * @param $id
	 * @param $netid
	 * @param $name
	 */
	function __construct($id, $netid, $name)
	{
		$this->id = $id;
		$this->netid = $netid;
		$this->name = $name;
	}

	/**
	 * Creates a new faculty member in the database
	 *
	 * @param $netid
	 * @return bool
	 */
	public static function createFaculty($netid)
	{
		/**
		 * Lookup NetID and get faculty member's name
		 */
		\FATS\Helpers\DirectoryHelper::getUserProfile($netid);

		$name = \FATS\Helpers\DirectoryHelper::$_name;

		$dal = new \FATS\DAL\DataAccessLayer();

		/**
		 * Attach the event handler
		 */
		new \FATS\DAL\DataTransactionMonitor($dal);

		return $dal->createFaculty($netid, $name);
	}

	/**
	 * Gets the specified faculty member from the database
	 *
	 * @param $id
	 * @return Faculty|null
	 */
	public static function getFaculty($id)
	{
		$dal = new \FATS\DAL\DataAccessLayer();

		/**
		 * Attach the event handler
		 */
		new \FATS\DAL\DataTransactionMonitor($dal);

		$faculty = $dal->getFaculty($id);

		return empty($faculty) ? null : $faculty;
	}

	/**
	 * Gets all faculty members from the database
	 *
	 * @return array|null
	 */
	public static function getAllFaculty()
	{
		$dal = new \FATS\DAL\DataAccessLayer();

		/**
		 * Attach the event handler
		 */
		new \FATS\DAL\DataTransactionMonitor($dal);

		$faculty = $dal->getAllFaculty();

		return empty($faculty) ? null : $faculty;
	}

	/**
	 * Updates the specified faculty member
	 *
	 * @param $id
	 * @param $netid
	 * @param $name
	 * @return bool
	 */
	public static function updateFaculty($id, $netid, $name)
	{
		$dal = new \FATS\DAL\DataAccessLayer();

		/**
		 * Attach the event handler
		 */
		new \FATS\DAL\DataTransactionMonitor($dal);

		return $dal->updateFaculty($id, $netid, $name);
	}

	/**
	 * Archives the specified faculty member
	 *
	 * @param $id
	 * @return bool
	 */
	public static function deleteFaculty($id)
	{
		$dal = new \FATS\DAL\DataAccessLayer();

		/**
		 * Attach the event handler
		 */
		new \FATS\DAL\DataTransactionMonitor($dal);

		return $dal->deleteFaculty($id);
	}
}

?>