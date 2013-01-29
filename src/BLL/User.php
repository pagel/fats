<?php
/**
 * User.php
 *
 * This class contains methods to perform CRUD operations for application users.
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
require_once 'Security/Roles.php';
require_once 'DAL/DataAccessLayer.php';
require_once 'DAL/DataTransactionMonitor.php';
require_once 'Library/Observable.php';
require_once 'Library/Observer.php';

class User extends \FATS\Library\Observable
{
	/**
	 * Unique ID
	 *
	 * @var int
	 */
	public $id;

	/**
	 * Northwestern NetID
	 *
	 * @var string
	 */
	public $netid;

	/**
	 * Full name
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Role ID
	 *
	 * @var int
	 */
	public $role;

	/**
	 * Permisssions ID
	 *
	 * @var int
	 */
	public $permissions;

	/**
	 * E-mail address
	 *
	 * @var string
	 */
	public $email;

	/**
	 * Initializes a new instance of the User class
	 *
	 * @param $id
	 * @param $netid
	 * @param $name
	 * @param $email
	 * @param $role
	 * @param $permisssions
	 */
	function __construct($id, $netid, $name, $email, $role, $permisssions)
	{
		$this->id = $id;
		$this->netid = $netid;
		$this->name = $name;
		$this->email = $email;
		$this->role = $role;
		$this->permissions = $permisssions;
	}

	/**
	 * Creates a new user account in the database
	 *
	 * @param $netid
	 * @param $role
	 * @param $permission
	 * @return bool
	 */
	public static function createUser($netid, $role, $permission)
	{
		/**
		 * Lookup NetID and get user's name and e-mail address
		 */
		\FATS\Helpers\DirectoryHelper::getUserProfile($netid);

		$name = \FATS\Helpers\DirectoryHelper::$_name;
		$email = \FATS\Helpers\DirectoryHelper::$_email;

		$dal = new \FATS\DAL\DataAccessLayer();

		/**
		 * Attach the event handler
		 */
		new \FATS\DAL\DataTransactionMonitor($dal);

		return $dal->createUser($netid, $name, $email, $role, $permission);
	}

	/**
	 * Gets the specified user account from the database
	 *
	 * @param $netid
	 * @return \FATS\BLL\User|null
	 */
	public static function getUser($netid)
	{
		$dal = new \FATS\DAL\DataAccessLayer();

		/**
		 * Attach the event handler
		 */
		new \FATS\DAL\DataTransactionMonitor($dal);

		$user = $dal->getUser($netid);

		return empty($user) ? null : $user;
	}

	/**
	 * Gets all user accounts from the database
	 *
	 * @return null|void
	 */
	public static function getAllUsers()
	{
		$dal = new \FATS\DAL\DataAccessLayer();

		/**
		 * Attach the event handler
		 */
		new \FATS\DAL\DataTransactionMonitor($dal);

		$users = $dal->getAllUsers();

		return empty($users) ? null : $users;
	}

	/**
	 * Gets all roles from the database
	 *
	 * @return null
	 */
	public static function getRoles()
	{
		$dal = new \FATS\DAL\DataAccessLayer();

		/**
		 * Attach the event handler
		 */
		new \FATS\DAL\DataTransactionMonitor($dal);

		$roles = $dal->getRoles();

		return empty($roles) ? null : $roles;
	}

	public static function getRoleFriendlyName($id){
		$dal = new \FATS\DAL\DataAccessLayer();

		/**
		 * Attach the event handler
		 */
		new \FATS\DAL\DataTransactionMonitor($dal);

		$roleFriendlyName = $dal->getRoleFriendlyName($id);

		return $roleFriendlyName;
	}

	/**
	 * Gets all permissions from the database
	 *
	 * @return null
	 */
	public static function getPermissions()
	{
		$dal = new \FATS\DAL\DataAccessLayer();

		/**
		 * Attach the event handler
		 */
		new \FATS\DAL\DataTransactionMonitor($dal);

		$permissions = $dal->getPermissions();

		return empty($permissions) ? null : $permissions;
	}

	/**
	 * Updates the specified user account
	 *
	 * @internal This method does not operate on the context (logged in) user
	 * @param $id
	 * @param $netid
	 * @param $name
	 * @param $email
	 * @param $role
	 * @param $permission
	 * @return bool
	 */
	public static function updateUser($id, $netid, $name, $email, $role, $permission)
	{
		$dal = new \FATS\DAL\DataAccessLayer();

		/**
		 * Attach the event handler
		 */
		new \FATS\DAL\DataTransactionMonitor($dal);

		return $dal->updateUser($id, $netid, $name, $email, $role, $permission);
	}

	/**
	 * Archives the specified user account
	 *
	 * @internal This method does not operate on the context (logged in) user
	 * @param $id
	 * @return bool
	 */
	public static function deleteUser($id)
	{
		$dal = new \FATS\DAL\DataAccessLayer();

		/**
		 * Attach the event handler
		 */
		new \FATS\DAL\DataTransactionMonitor($dal);

		return $dal->deleteUser($id);
	}
}

?>