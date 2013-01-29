<?php
/**
 * DataTransactionMonitor.php
 *
 * This class monitors database transactions and reports results to the user.
 *
 * PHP version 5
 *
 * @category   Web Application
 * @package    Faculty Advancement Tracking System
 * @author     Geoffrey Pagel <geoffrey.pagel@northwestern.edu>
 * @version    SVN: $Id$
 */
namespace FATS\DAL;

require_once 'Library/Observer.php';
require_once 'Diagnostics/MessageManager.php';

class DataTransactionMonitor extends \FATS\Library\Observer
{
	/**
	 * @param null $observable
	 * @throws \InvalidArgumentException
	 */
	public function __construct($observable = null)
	{
		if (!$observable instanceof DataAccessLayer)
		{
			throw new \InvalidArgumentException;
		}

		parent::__construct($observable);
	}

	/**
	 * Takes action based on the result of a database transaction
	 *
	 * @param \FATS\DAL\DataAccessLayer|\FATS\Library\Observable $observable
	 * @return void
	 */
	public function update(\FATS\Library\Observable $observable)
	{
		$status = $observable->getState();

		// Attempt to log the transaction in the database
		if (!$status instanceof \FATS\BLL\LogEntry){
			\FATS\Diagnostics\MessageManager::setMessage($status);
			return;
		}

		$dal = new DataAccessLayer();
		$dal->createLogEntry($status);
	}
}

?>