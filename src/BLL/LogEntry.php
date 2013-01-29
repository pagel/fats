<?php
/**
 * LogEntry.php
 *
 * This class provides methods for creating log entry objects.
 *
 * PHP version 5
 *
 * @category   Web Application
 * @package    Faculty Advancement Tracking System
 * @author     Geoffrey Pagel <geoffrey.pagel@northwestern.edu>
 * @version    SVN: $Id$
 */
namespace FATS\BLL;

require_once 'Library/Observable.php';
require_once 'Library/Observer.php';

class LogEntry extends \FATS\Library\Observable
{
	/**
	 * Log operation type
	 * @var int
	 */
	public $logOperation;

	/**
	 * Log level type
	 *
	 * @var int
	 */
	public $logLevel;

	/**
	 * Log message
	 *
	 * @var string
	 */
	public $logMessage;

	/**
	 * Faculty document ID
	 *
	 * @var int
	 */
	public $facultyDocumentsId;

	/**
	 * Initializes a new instance of the LogEntry class
	 *
	 * @param $logOperation
	 * @param $logLevel
	 * @param null $logMessage
	 * @param null $facultyDocumentsId
	 */
	function __construct($logOperation, $logLevel, $logMessage = null, $facultyDocumentsId = null)
	{
		$this->logOperation = $logOperation;
		$this->logLevel = $logLevel;
		$this->logMessage = $logMessage;
		$this->facultyDocumentsId = $facultyDocumentsId;
	}
}

?>