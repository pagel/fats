<?php
/**
 * documents.php
 *
 * This script provides methods for performing CRUD operations and downloads for documents.
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
require_once 'BLL/Folders.php';

$smarty = new \FATS\Application\Smarty();

/**
 * Get context items
 */
$contextFaculty = \FATS\Application\ObjectManager::getContextFaculty();
$contextUser = \FATS\Application\ObjectManager::getContextUser();
$contextUserCanWrite = intval($contextUser->permissions) === \FATS\Security\Permissions::READWRITE || intval($contextUser->permissions) === \FATS\Security\Permissions::READWRITEDELETE;
$contextUserCanDelete = intval($contextUser->permissions) === \FATS\Security\Permissions::READWRITEDELETE;

/**
 * Navigation elements
 */
$smarty->assign('navigation_error', '');

if (1 !== $contextFaculty)
{
	$smarty->assign('navigation', \FATS\BLL\Navigation::displayTree());
}

/**
 * Assign blade variables
 */
$smarty->assign('page_title', $contextFaculty->name);
$smarty->assign('breadcrumb_title', 'Folders');
$smarty->assign('context_user_netid', $contextUser->netid);
$smarty->assign('context_user_accesslevel', \FATS\BLL\User::getRoleFriendlyName($contextUser->role));

$folders = \FATS\BLL\Folders::getFolders();
$smarty->assign('folders', $folders);

/**
 * Check permissions
 */
$smarty->assign('write', $contextUserCanWrite);
$smarty->assign('delete', $contextUserCanDelete);

$smarty->display('documents.tpl');

?>