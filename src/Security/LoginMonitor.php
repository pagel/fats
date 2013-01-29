<?php
/**
 * LoginMonitor.php
 *
 * This class monitors login events and responds accordingly.
 *
 * PHP version 5
 *
 * @category   Web Application
 * @package    Faculty Advancement Tracking System
 * @author     Geoffrey Pagel <geoffrey.pagel@northwestern.edu>
 * @version    SVN: $Id$
 */
namespace FATS\Security;

require_once 'Library/Observer.php';

class LoginMonitor extends \FATS\Library\Observer
{
	public function __construct($observable = null)
	{
		if (!$observable instanceof Login)
		{
			throw new \InvalidArgumentException;
		}

		parent::__construct($observable);
	}

	/**
	 * Takes action based on the result of the login process.
	 *
	 * @param \FATS\Library\Observable|\FATS\Security\Login $observable
	 * @throws \InvalidArgumentException
	 * @return void
	 */
	public function update(\FATS\Library\Observable $observable)
	{
		/**
		 * The getState method is expected to return a Login constant
		 */
		$state = $observable->getState();

		switch ($state)
		{
			case Login::LOGIN_INVALID_USER:
			case Login::LOGIN_MISSING_CREDENTIALS:
			case Login::LOGIN_MAINTENANCE_MODE:
			case Login::LOGIN_ACCESS_DENIED:
				header('Location: /');
				break;
			default:
				/**
				 * Default case = access granted
				 * Clear any error message and redirect the user to the main menu
				 */
				\FATS\Diagnostics\ErrorManager::clearError();
				header('Location: /main.php');
				break;
		}
	}
}

?>