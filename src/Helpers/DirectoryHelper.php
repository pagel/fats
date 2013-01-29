<?php
/**
 * DirectoryHelper.php
 *
 * This class performs LDAP searches using a given NetID and retrieves directory information.
 *
 * PHP version 5
 *
 * @category   Web Application
 * @package    Faculty Advancement Tracking System
 * @author     Geoffrey Pagel <geoffrey.pagel@northwestern.edu>
 * @version    SVN: $Id$
 */
namespace FATS\Helpers;

class DirectoryHelper
{
	/**
	 * LDAP service account distinguished name
	 */
	const LDAP_SERVICE_DN = 'cn=mccapp1,ou=service,dc=northwestern,dc=edu';

	/**
	 * LDAP service account password
	 */
	const LDAP_SERVICE_PASSWORD = 'GN7*2(yqbOhr';

	/**
	 * LDAP search base distinguished name
	 */
	const LDAP_SEARCH_BASE_DN = 'dc=northwestern,dc=edu';

	/**
	 * LDAP registry host URL (secure queries)
	 */
	const LDAP_REGISTRY_HOST = 'registry.northwestern.edu';

	/**
	 * LDAP directory host URL
	 */
	const LDAP_DIRECTORY_HOST = 'directory.northwestern.edu';

	/**
	 * User's distinguished name
	 *
	 * @var string
	 */
	public static $_distinguishedName;

	/**
	 * User's full name
	 *
	 * @var string
	 */
	public static $_name;

	/**
	 * User's e-mail address
	 *
	 * @var string
	 */
	public static $_email;

	/**
	 * Performs an LDAP search using the specified NetID
	 *
	 * @param $netid
	 * @throws \Exception
	 */
	public static function getUserProfile($netid)
	{
		try
		{
			$ldapConnection = @ldap_connect(self::LDAP_DIRECTORY_HOST);

			if (false === $ldapConnection)
			{
				throw new \Exception('Authentication services are currently unavailable');
			}

			ldap_set_option($ldapConnection, LDAP_OPT_PROTOCOL_VERSION, 3);
			ldap_set_option($ldapConnection, LDAP_OPT_REFERRALS, 0);

			/**
			 * Bind with our service account to retrieve the user's DN
			 */
			$ldap_bind = @ldap_bind($ldapConnection);

			if (false === $ldap_bind)
			{
				throw new \Exception('Authentication services are currently unavailable');
			}

			$filter = "uid={$netid}";
			$search = ldap_search($ldapConnection, self::LDAP_SEARCH_BASE_DN, $filter);
			$result = ldap_get_entries($ldapConnection, $search);

			self::$_distinguishedName = $result[0]['dn'];

			/**
			 * If there is no DN for the NetID provided, exit.
			 */
			if (empty(self::$_distinguishedName))
			{
				throw new \Exception('Invalid NetID');
			}

			self::$_name = $result[0]['displayname'][0];
			self::$_email = $result[0]['mail'][0];
		}
		catch (\Exception $x)
		{
			if ($ldapConnection){
				ldap_unbind($ldapConnection);
			}

			trigger_error($x->getMessage(), E_USER_ERROR);
		}
	}
}

?>