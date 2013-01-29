<?php
/**
 * index.php
 *
 * This script provides methods for logging in users.
 *
 * PHP version 5
 *
 * @category   Web Application
 * @package    Faculty Advancement Tracking System
 * @author     Geoffrey Pagel <geoffrey.pagel@northwestern.edu>
 * @version    SVN: $Id$
 */
namespace FATS;

require_once 'Application/Bootstrap.php';
require_once 'Security/Login.php';
require_once 'Security/LoginMonitor.php';

$smarty = new \FATS\Application\Smarty();

/**
 * If this is a postback, log the user in as long as we're not in maintenance mode.  If we are,
 * and the user is an administrator, proceed with the login.  If we're in demo mode, skip the
 * authentication piece altogether.
 */
if (isset($_POST['netid']) && isset($_POST['password']))
{
	if (!\FATS\Helpers\ApplicationHelper::isMaintenanceMode() || \FATS\Helpers\ApplicationHelper::isAdministrator($_POST['netid']))
	{
		$login = new \FATS\Security\Login();
		new \FATS\Security\LoginMonitor($login);
		$isSuccess = $login->handleLogin();

		if ($isSuccess)
		{
			\FATS\Diagnostics\ErrorManager::clearError();
			exit;
		}
	}
}

$errorMessage = \FATS\Diagnostics\ErrorManager::isError() ? \FATS\Diagnostics\ErrorManager::getError() : '';

$smarty->assign('page_title', 'Login');
$smarty->assign('error', $errorMessage);
$smarty->assign('isAdmin', false);
$smarty->assign('ignoreClass', true);
$smarty->display('index.tpl');

?>
