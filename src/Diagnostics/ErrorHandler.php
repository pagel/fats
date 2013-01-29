<?php
/**
 * ErrorHandler.php
 *
 * This class manages application error reporting and logging.
 *
 * PHP version 5
 *
 * @category   Web Application
 * @package    Faculty Advancement Tracking System
 * @author     Geoffrey Pagel <geoffrey.pagel@northwestern.edu>
 * @version    SVN: $Id$
 */
namespace FATS\Diagnostics;

require_once 'Library/Observable.php';

class ErrorHandler extends \FATS\Library\Observable
{
	/**
	 * Composes user and system error messages and notifies event subscribers
	 *
	 * @param $errorNumber
	 * @param $errorString
	 * @param $errorFile
	 * @param $errorLine
	 */
	public function error($errorNumber , $errorString, $errorFile, $errorLine){
		/**
		 * Construct two different error strings: one for the system logs and one to
		 * display to the user.
		 *
		 * Error number ($errorNumber) constants:
		 * 256 = E_USER_ERROR
		 * 512 = E_USER_WARNING
		 * 1024 = E_USER_NOTICE
		 */
		$systemErrorMessage = sprintf('Error in %s on line %u: %s', $errorFile, $errorLine, $errorString);

		switch($errorNumber){
			case E_USER_ERROR:
				syslog(LOG_ERR, $systemErrorMessage);
				break;
			case E_USER_WARNING:
				syslog(LOG_WARNING, $systemErrorMessage);
				break;
			case E_USER_NOTICE:
				syslog(LOG_NOTICE, $systemErrorMessage);
				break;
		}

		$this->state = array('level' => $errorNumber, 'user' => $errorString, 'system' => $systemErrorMessage);
		$this->notify();
	}
}

?>