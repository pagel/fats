<?php
/**
 * LogLevel.php
 *
 * This class enumerates log level types.
 *
 * PHP version 5
 *
 * @category   Web Application
 * @package    Faculty Advancement Tracking System
 * @author     Geoffrey Pagel <geoffrey.pagel@northwestern.edu>
 * @version    SVN: $Id$
 */
namespace FATS\Diagnostics;

class LogLevel
{
	/**
	 * Flag: Informational message
	 */
	const INFO = 1;

	/**
	 * Flag: Warning message
	 */
	const WARNING = 2;

	/**
	 * Flag: Error message
	 */
	const ERROR = 3;

	/**
	 * Flag: Fatal condition message
	 */
	const FATAL = 4;

	/**
	 * Flag: Debug message
	 */
	const DEBUG = 5;
}

?>