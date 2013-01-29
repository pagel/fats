<?php
/**
 * Navigation.php
 *
 * This class contains methods to compose and render navigation elements.
 *
 * PHP version 5
 *
 * @category   Web Application
 * @package    Faculty Advancement Tracking System
 * @author     Geoffrey Pagel <geoffrey.pagel@northwestern.edu>
 * @version    SVN: $Id$
 */
namespace FATS\BLL;

require_once 'DAL/DataAccessLayer.php';
require_once 'DAL/DataTransactionMonitor.php';
require_once 'Library/Observable.php';
require_once 'Application/ObjectManager.php';

class Navigation extends \FATS\Library\Observable
{
	/**
	 * Rebuilds the navigation tree using modified preorder tree traversal
	 *
	 * @return bool
	 */
	public static function rebuildTree()
	{
		try
		{
			$dal = new \FATS\DAL\DataAccessLayer();

			$dal->rebuildTree('Root', 1);

			return true;
		}
		catch (\Exception $x)
		{
			trigger_error($x->getMessage(), E_USER_ERROR);

			return false;
		}
	}

	/**
	 * Composes and renders the navigation tree
	 *
	 * @return bool|string
	 */
	public static function displayTree()
	{
		try
		{
			if (null === \FATS\Application\ObjectManager::getContextFaculty())
			{
				return false;
			}

			$dal = new \FATS\DAL\DataAccessLayer();

			/**
			 * Attach the event handler
			 */
			new \FATS\DAL\DataTransactionMonitor($dal);

			$tree = $dal->displayTree();

			return $tree;
		}
		catch (\Exception $x)
		{
			trigger_error($x->getMessage(), E_USER_ERROR);

			return false;
		}
	}
}

?>