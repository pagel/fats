<?php
/**
 * Observable.php
 *
 * This class provides a base contract and implementation for classes that need to register events.
 *
 * PHP version 5
 *
 * @category   Web Application
 * @package    Faculty Advancement Tracking System
 * @author     Geoffrey Pagel <geoffrey.pagel@northwestern.edu>
 * @version    SVN: $Id$
 */
namespace FATS\Library;

abstract class Observable
{
	/**
	 * Collection of event listeners
	 *
	 * @var array
	 */
	protected $observers;

	/**
	 * Event message(s)
	 *
	 * @var mixed
	 */
	protected $state;

	/**
	 * Initializes a new instance of the derived class
	 */
	public function __construct()
	{
		$this->observers = array();
		$this->state = null;
	}

	/**
	 * Attaches the listener to a target
	 *
	 * @param Observer $observer
	 */
	public function attach(Observer $observer)
	{
		$i = array_search($observer, $this->observers);

		if ($i === false)
		{
			$this->observers[] = $observer;
		}
	}

	/**
	 * Detaches the listener from the target
	 *
	 * @param Observer $observer
	 */
	public function detach(Observer $observer)
	{
		if (isset($this->observers))
		{
			$i = array_search($observer, $this->observers);

			if ($i !== false)
			{
				unset($this->observers[$i]);
			}
		}
	}

	/**
	 * Gets the event message(s)
	 *
	 * @return null
	 */
	public function getState()
	{
		return $this->state;
	}

	/**
	 * Sets the event message(s)
	 *
	 * @param $state
	 */
	public function setState($state)
	{
		$this->state = $state;
		$this->notify();
	}

	/**
	 * Notifies event listeners that the target has a status update
	 */
	public function notify()
	{
		if (isset($this->observers))
		{
			foreach ($this->observers as $observer)
			{
				$observer->update($this);
			}
		}
	}

	/**
	 * Gets the event listener collection
	 *
	 * @return array
	 */
	public function getObservers()
	{
		return $this->observers;
	}
}

?>