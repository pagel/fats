<?php
/**
 * Login.php
 *
 * This class handles access to the application.
 *
 * PHP version 5
 *
 * @category   Web Application
 * @package    Faculty Advancement Tracking System
 * @author     Geoffrey Pagel <geoffrey.pagel@northwestern.edu>
 * @version    SVN: $Id$
 */
namespace FATS\Security;

require_once 'Library/Observable.php';
require_once 'AuthorizationManager.php';
require_once 'AuthenticationManager.php';
require_once 'Helpers/ApplicationHelper.php';
require_once 'Application/ObjectManager.php';

class Login extends \FATS\Library\Observable
{
	private $_netid;
	private $_password;

	/**
	 * Flag: Access denied
	 */
	const LOGIN_ACCESS_DENIED = 1;

	/**
	 * Flag: Access granted
	 */
	const LOGIN_ACCESS_GRANTED = 2;

	/**
	 * Flag: Maintenance mode enabled
	 */
	const LOGIN_MAINTENANCE_MODE = 3;

	/**
	 * Flag: Username or password is blank
	 */
	const LOGIN_MISSING_CREDENTIALS = 4;

	/**
	 * Flag: User does not have an account in FATS
	 */
	const LOGIN_INVALID_USER = 5;

	/**
	 * Performs authorization and authentication and notifies event subscribers of results
	 *
	 * @return bool
	 */
	public function handleLogin()
	{
		$this->state = self::LOGIN_ACCESS_GRANTED;
		$login = true;

		$this->_netid = trim($_POST['netid']);
		$this->_password = $_POST['password'];

		/**
		 * If the NetID or password field is empty, reject the login attempt.
		 */
		if (empty($this->_netid) || empty($this->_password))
		{
			$login = false;
			$this->state = self::LOGIN_MISSING_CREDENTIALS;
			trigger_error('NetID and password cannot be blank.', E_USER_ERROR);
		}
		else
		{
			$user = AuthorizationManager::validateUser($this->_netid);

			/**
			 * If the NetID does not correspond to a user account in the FATS database, deny access.
			 */
			if (null === $user)
			{
				$this->state = self::LOGIN_INVALID_USER;
				$login = false;
			}
			else
			{
				/**
				 * Authenticate the user with LDAP.
				 */
				AuthenticationManager::authenticateUser($this->_netid, $this->_password);

				if (!AuthenticationManager::getIsAuthenticated())
				{
					$login = false;
					$this->state = self::LOGIN_ACCESS_DENIED;
				}
				else
				{
					\FATS\Application\ObjectManager::setContextUser($user);
				}
			}
		}

		/**
		 * Update event listeners
		 */
		$this->setState($this->state);
		$this->notify();

		return $login;
	}
}

?>