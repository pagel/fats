<?php
/**
 * Roles.php
 *
 * This class enumerates user roles.
 *
 * PHP version 5
 *
 * @category   Web Application
 * @package    Faculty Advancement Tracking System
 * @author     Geoffrey Pagel <geoffrey.pagel@northwestern.edu>
 * @version    SVN: $Id$
 */
namespace FATS\Security;

class Roles
{
	/**
	 * Flag: Administrator
	 */
	const ADMINISTRATOR = 1;

	/**
	 * Flag: Blue level access
	 */
	const BLUE = 2;

	/**
	 * Flag: Red level access
	 */
	const RED = 3;

	/**
	 * Flag: Green level access
	 */
	const GREEN = 4;

	/**
	 * Flag: Yellow level access
	 */
	const YELLOW = 5;

	/**
	 * Flag: Orange level access
	 */
	const ORANGE = 6;
}

?>