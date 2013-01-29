<?php
/**
 * Folders.php
 *
 * This class provides methods for creating folder objects.
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

class Folders extends \FATS\Library\Observable
{
	/**
	 * Folder ID
	 *
	 * @var int
	 */
	public $id;

	/**
	 * Folder name
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Navigation position right
	 *
	 * @var int
	 */
	public $rgt;

	/**
	 * Navigation position left
	 *
	 * @var int
	 */
	public $lft;

	/**
	 * Initializes a new instance of the Folders class
	 *
	 * @param $id
	 * @param $name
	 * @param $rgt
	 * @param $lft
	 */
	function __construct($id, $name, $rgt, $lft)
	{
		$this->id = $id;
		$this->name = $name;
		$this->rgt = $rgt;
		$this->lft = $lft;
	}

	/**
	 * Gets the folders within the specified area of the navigation tree
	 *
	 * @param null $leftMin
	 * @param null $leftMax
	 * @return bool|null
	 */
	public static function getFolders($leftMin = null, $leftMax = null)
	{
		$dal = new \FATS\DAL\DataAccessLayer();

		/**
		 * Attach the event handler
		 */
		new \FATS\DAL\DataTransactionMonitor($dal);

		$folders = $dal->getFolders($leftMin, $leftMax);

		return empty($folders) ? null : $folders;
	}
}

?>