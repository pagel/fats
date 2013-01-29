<?php
/**
 * users.php
 *
 * This script provides methods for performing CRUD operations on user accounts.
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
require_once 'BLL/User.php';

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
 * Assign users variable
 */
$userList = \FATS\BLL\User::getAllUsers();
$smarty->assign('users', $userList);

/**
 * Assign roles variable
 */
$rolesList = \FATS\BLL\User::getRoles();
$smarty->assign('roles', $rolesList);

/**
 * Assign permissions variable
 */
$permissionsList = \FATS\BLL\User::getPermissions();
$smarty->assign('permissions', $permissionsList);

/**
 * Assign blade variables
 */
$smarty->assign('page_title', 'Manage Users');
$smarty->assign('breadcrumb_title', 'Manage Users');
$smarty->assign('context_user_netid', $contextUser->netid);
$smarty->assign('context_user_accesslevel', \FATS\BLL\User::getRoleFriendlyName($contextUser->role));

$smarty->display('users.tpl');

?>