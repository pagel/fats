<?php
/**
 * Observer.php
 *
 * This class provides a base contract and implementation for event listeners.
 *
 * PHP version 5
 *
 * @category   Web Application
 * @package    Faculty Advancement Tracking System
 * @author     Geoffrey Pagel <geoffrey.pagel@northwestern.edu>
 * @version    SVN: $Id$
 */
namespace FATS\Library;

abstract class Observer
{
	/**
	 * Initializes a new instance of the derived class
	 *
	 * @param null $observable
	 */
	public function __construct($observable = null)
	{
		if (is_object($observable) && $observable instanceof Observable)
		{
			$observable->attach($this);
		}
	}

	/**
	 * Handles event notifications to subscribers
	 *
	 * @param Observable $observable
	 */
	public function update(Observable $observable)
	{
		// Search for an observer method with the state name
		if (method_exists($this, $observable->getState()))
		{
			call_user_func_array(array($this, $observable->getState()), array($observable));
		}
	}
}

?>