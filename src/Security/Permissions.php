<?php
/**
 * Permissions.php
 *
 * This class enumerates user permissions.
 *
 * PHP version 5
 *
 * @category   Web Application
 * @package    Faculty Advancement Tracking System
 * @author     Geoffrey Pagel <geoffrey.pagel@northwestern.edu>
 * @version    SVN: $Id$
 */
namespace FATS\Security;

class Permissions
{
	/**
	 * Flag: Read Only
	 */
	const READ = 1;

	/**
	 * Flag: Read and Write
	 */
	const READWRITE = 2;

	/**
	 * Flag: Read, Write and Delete
	 */
	const READWRITEDELETE = 3;
}

?>