<?php
/**
 * options.php
 *
 * This script provides methods for changing application options.
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
require_once 'BLL/Navigation.php';

/**
 * Get context items
 */
$contextUser = \FATS\Application\ObjectManager::getContextUser();

/**
 * Security check - users must be administrators
 */
if (intval($contextUser->role) !== \FATS\Security\Roles::ADMINISTRATOR)
{
	header('Location: main.php');
	exit;
}

$smarty = new \FATS\Application\Smarty();

$error = \FATS\Diagnostics\ErrorManager::isError() ? \FATS\Diagnostics\ErrorManager::getError() : '';
$smarty->assign('table_error', $error);
\FATS\Diagnostics\ErrorManager::clearError();

/**
 * Check user role and add administrative functions accordingly
 */
$smarty->assign('isAdmin', intval($contextUser->role) === 1);

/**
 * Assign blade variables
 */
$smarty->assign('page_title', 'Application Options');
$smarty->assign('breadcrumb_title', 'Application Options');
$smarty->assign('context_user_netid', $contextUser->netid);
$smarty->assign('context_user_accesslevel', \FATS\BLL\User::getRoleFriendlyName($contextUser->role));

/**
 * Assign user variable
 */
$userList = \FATS\BLL\User::getAllUsers();
$smarty->assign('users', $userList);

/**
 * Assign option variables
 */
$smarty->assign('maintenance_mode', \FATS\Helpers\ApplicationHelper::isMaintenanceMode());
$smarty->assign('debug_mode', \FATS\Helpers\ApplicationHelper::isDebugMode());
$smarty->assign('demo_mode', \FATS\Helpers\ApplicationHelper::isDemoMode());

$smarty->display('options.tpl');

?>