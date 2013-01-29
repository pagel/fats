<?php
/**
 * LogOperation.php
 *
 * This class enumerates log operation types.
 *
 * PHP version 5
 *
 * @category   Web Application
 * @package    Faculty Advancement Tracking System
 * @author     Geoffrey Pagel <geoffrey.pagel@northwestern.edu>
 * @version    SVN: $Id$
 */
namespace FATS\Diagnostics;

class LogOperation
{
	/**
	 * Flag: Create
	 */
	const CREATE = 1;

	/**
	 * Flag: Read
	 */
	const READ = 2;

	/**
	 * Flag: Update
	 */
	const UPDATE = 3;

	/**
	 * Flag: Delete
	 */
	const DELETE = 4;

	/**
	 * Flag: Login
	 */
	const LOGIN = 5;

	/**
	 * Flag: Logout
	 */
	const LOGOUT = 6;

	/**
	 * Flag: Download
	 */
	const DOWNLOAD = 7;

	/**
	 * Flag: Upload
	 */
	const UPLOAD = 8;

	/**
	 * Flag: Audit
	 */
	const AUDIT = 9;
}

?>