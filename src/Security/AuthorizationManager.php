<?php
/**
 * AuthorizationManager.php
 *
 * This class manages database user account authorization for the application.
 *
 * PHP version 5
 *
 * @category   Web Application
 * @package    Faculty Advancement Tracking System
 * @author     Geoffrey Pagel <geoffrey.pagel@northwestern.edu>
 * @version    SVN: $Id$
 */
namespace FATS\Security;

require_once 'BLL/User.php';

class AuthorizationManager
{
	/**
	 * Ensures that the logging in user has a valid account in the database
	 *
	 * @param $netid
	 * @return \FATS\BLL\User|null
	 * @throws \Exception
	 */
	public static function validateUser($netid)
	{
		try
		{
			$user = \FATS\BLL\User::getUser($netid);

			if (!isset($user))
			{
				throw new \Exception('The NetID you supplied does not have access to this system.');
			}
		}
		catch (\Exception $x)
		{
			trigger_error($x->getMessage(), E_USER_ERROR);
			return null;
		}

		return $user;
	}
}

?>