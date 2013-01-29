<?php
/**
 * Bootstrap.php
 *
 * This adds required files used throughout the application.
 *
 * PHP version 5
 *
 * @category   Web Application
 * @package    Faculty Advancement Tracking System
 * @author     Geoffrey Pagel <geoffrey.pagel@northwestern.edu>
 * @version    SVN: $Id$
 */
namespace FATS\Application;

require_once 'Smarty.php';
require_once 'Helpers/ApplicationHelper.php';
require_once 'Diagnostics/ErrorHandler.php';
require_once 'Diagnostics/LogErrorDisplay.php';
require_once 'Diagnostics/ErrorManager.php';
require_once 'Application/ObjectManager.php';
require_once 'Security/Roles.php';
require_once 'Security/Permissions.php';

/**
 * Configure error handling and reporting
 */
$errorHandler = new \FATS\Diagnostics\ErrorHandler();
new \FATS\Diagnostics\LogErrorDisplay($errorHandler);
set_error_handler(array($errorHandler, 'error'));

/**
 * Security check - if this isn't the login page, there should be a context user object
 */
if (strtolower(basename($_SERVER['PHP_SELF'])) !== 'index.php')
{
	$contextUser = ObjectManager::getContextUser();

	if (!isset($contextUser))
	{
		header('Location: /', true, 'Status: 401 Unauthorized');
		exit;
	}
}
?>