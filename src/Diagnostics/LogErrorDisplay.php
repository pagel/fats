<?php
/**
 * LogErrorDisplay.php
 *
 * This class prepares application error messages for display in the browser.
 *
 * PHP version 5
 *
 * @category   Web Application
 * @package    Faculty Advancement Tracking System
 * @author     Geoffrey Pagel <geoffrey.pagel@northwestern.edu>
 * @version    SVN: $Id$
 */
namespace FATS\Diagnostics;

require_once 'Application/ObjectManager.php';
require_once 'Helpers/ApplicationHelper.php';
require_once 'Library/Observer.php';
require_once 'ErrorManager.php';

class LogErrorDisplay extends \FATS\Library\Observer
{
	/**
	 * Initializes a new instance of the LogErrorDisplay class
	 *
	 * @param null $observable
	 * @throws \InvalidArgumentException
	 */
	public function __construct($observable = null)
	{
		if (!$observable instanceof ErrorHandler)
		{
			throw new \InvalidArgumentException;
		}

		parent::__construct($observable);
	}

	/**
	 * Composes a session variable that contains a user level error message
	 *
	 * @param \FATS\Diagnostics\ErrorHandler|\FATS\Library\Observable $observable
	 * @return void
	 */
	public function update(\FATS\Library\Observable $observable)
	{
		/**
		 * The getState method is expected to return an array that contains
		 * three keys: level, user, and system
		 */
		$state = $observable->getState();

		$contextUser = \FATS\Application\ObjectManager::getContextUser();

		$errorMessage = $state['user'];

		if (null !== $contextUser && intval($contextUser->role) === 1 && \FATS\Helpers\ApplicationHelper::isDebugMode())
		{
			$errorMessage = $state['system'];
		}

		ErrorManager::setError($errorMessage);
	}
}

?>