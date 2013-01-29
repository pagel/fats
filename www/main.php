<?php
/**
 * main.php
 *
 * This script provides methods for the application's home page.
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
require_once 'BLL/Faculty.php';

$smarty = new \FATS\Application\Smarty();

$error = \FATS\Diagnostics\ErrorManager::isError() ? \FATS\Diagnostics\ErrorManager::getError() : '';
$smarty->assign('navigation_error', $error);
\FATS\Diagnostics\ErrorManager::clearError();

\FATS\Application\ObjectManager::clearContextFaculty();

/**
 * Get context items
 */
$contextUser = \FATS\Application\ObjectManager::getContextUser();

/**
 * Check user role and add administrative functions accordingly
 */
$smarty->assign('isAdmin', intval($contextUser->role) === 1);
$smarty->assign('ignoreClass', intval($contextUser->role) !== 1);

/**
 * Assign blade variables
 */
$smarty->assign('page_title', 'Home');
$smarty->assign('breadcrumb_title', '');
$smarty->assign('context_user_netid', $contextUser->netid);
$smarty->assign('context_user_accesslevel', \FATS\BLL\User::getRoleFriendlyName($contextUser->role));

/**
 * Display the list of faculty
 */
$facultyList = \FATS\BLL\Faculty::getAllFaculty();
$smarty->assign('faculty', $facultyList);

$smarty->display('main.tpl');

?>
