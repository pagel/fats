<?php
/**
 * AuthenticationManager.php
 *
 * This class manages LDAP authentication for the application.
 *
 * PHP version 5
 *
 * @category   Web Application
 * @package    Faculty Advancement Tracking System
 * @author     Geoffrey Pagel <geoffrey.pagel@northwestern.edu>
 * @version    SVN: $Id$
 */
namespace FATS\Security;

require_once 'Helpers/DirectoryHelper.php';

class AuthenticationManager
{
	private static $_isAuthenticated;

	/**
	 * Sets the user's authentication status
	 *
	 * @param $isAuthenticated
	 */
	private static function setIsAuthenticated($isAuthenticated)
	{
		self::$_isAuthenticated = $isAuthenticated;
	}

	/**
	 * Gets the user's authentication status
	 *
	 * @return mixed
	 */
	public static function getIsAuthenticated()
	{
		return self::$_isAuthenticated;
	}

	/**
	 * Authenticates the user with the supplied NetID and password
	 *
	 * @param $netid
	 * @param $password
	 * @throws \Exception
	 */
	public static function authenticateUser($netid, $password)
	{
		if (\FATS\Helpers\ApplicationHelper::isDemoMode())
		{
			self::setIsAuthenticated(true);

			return;
		}

		\FATS\Helpers\DirectoryHelper::getUserProfile($netid);
		$distinguishedName = \FATS\Helpers\DirectoryHelper::$_distinguishedName;

		/**
		 * If the supplied NetID doesn't return a DN from LDAP, terminate this script
		 */
		if (empty($distinguishedName))
		{
			return;
		}

		try
		{
			$ldapConnection = @ldap_connect(\FATS\Helpers\DirectoryHelper::LDAP_REGISTRY_HOST, 636);

			if (false === $ldapConnection)
			{
				ldap_unbind($ldapConnection);
				throw new \Exception('Authentication services are currently unavailable');
			}

			ldap_set_option($ldapConnection, LDAP_OPT_PROTOCOL_VERSION, 3);
			ldap_set_option($ldapConnection, LDAP_OPT_REFERRALS, 0);

			/**
			 * Bind with the user's NetID and password
			 */
			$ldap_bind = @ldap_bind($ldapConnection, $distinguishedName, $password);

			if ($ldap_bind)
			{
				ldap_unbind($ldapConnection);
				self::setIsAuthenticated(true);
			}
			else
			{
				ldap_unbind($ldapConnection);
				throw new \Exception('Invalid password');
			}
		}
		catch (\Exception $x)
		{
			trigger_error($x->getMessage(), E_USER_ERROR);
		}
	}
}

?>