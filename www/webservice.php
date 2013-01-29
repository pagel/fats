<?php
/**
 * webservice.php
 *
 * This script provides web services for the application using the Slim REST framework.
 *
 * PHP version 5
 *
 * @category   Web Application
 * @package    Faculty Advancement Tracking System
 * @author     Geoffrey Pagel <geoffrey.pagel@northwestern.edu>
 * @version    SVN: $Id$
 */

require_once 'Slim/Slim.php';
require_once 'BLL/User.php';
require_once 'BLL/Faculty.php';
require_once 'BLL/Application.php';
require_once 'BLL/Documents.php';
require_once 'Application/ObjectManager.php';

/**
 * Register the webservice autoloader
 */
\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

/**
 * Application option web services
 */
$app->get('/options', 'getApplicationOptions');
$app->put('/options', 'setApplicationOptions');

/**
 * User account web services
 */
$app->get('/users', 'getAllUsers');
$app->get('/roles', 'getRoles');
$app->get('/permissions', 'getPermissions');
$app->put('/users', 'updateUser');
$app->post('/users', 'createUser');
$app->delete('/users/:id', 'deleteUser');

/**
 * Faculty member web services
 */
$app->get('/faculty', 'getAllFaculty');
$app->get('/faculty/:id', 'getFaculty');
$app->post('/faculty', 'createFaculty');
$app->put('/faculty/:id', 'updateFaculty');
$app->delete('/faculty/:id', 'deleteFaculty');

/**
 * Document operations web services
 */
$app->get('/documents/:id', 'getDocuments');
$app->get('/download', 'downloadAllDocuments');
$app->get('/download/:ids', 'downloadDocuments');
$app->post('/documents', 'uploadDocument');
$app->delete('/documents/:ids', 'deleteDocuments');

$app->run();

/**
 * Gets application options
 */
function getApplicationOptions()
{
	$options = \FATS\BLL\Application::getApplicationOptions();
	echo json_encode(array('options' => $options));
}

/**
 * Sets application options
 */
function setApplicationOptions()
{
	$request = \Slim\Slim::getInstance()->request();

	parse_str($request->getBody(), $params);

	$response = \FATS\BLL\Application::setApplicationOptions($params);
	echo json_encode(array('result' => $response));
}

/**
 * Gets all user accounts
 */
function getAllUsers()
{
	$response = \FATS\BLL\User::getAllUsers();
	echo json_encode(array('result' => $response));
}

/**
 * Creates a new user account
 */
function createUser()
{
	$request = \Slim\Slim::getInstance()->request();

	parse_str($request->getBody(), $params);

	$response = \FATS\BLL\User::createUser($params['netid'], $params['role'], $params['permission']);
	echo json_encode(array('result' => $response));
}

/**
 * Deletes an existing user account
 *
 * @param $id
 */
function deleteUser($id)
{
	$response = \FATS\BLL\User::deleteUser($id);
	echo json_encode(array('result' => $response));
}

/**
 * Updates an existing user account
 */
function updateUser(){
	$request = \Slim\Slim::getInstance()->request();

	parse_str($request->getBody(), $params);

	$response = \FATS\BLL\User::updateUser($params['id'], $params['netid'], $params['name'], $params['email'], $params['role'], $params['permission']);
	echo json_encode(array('result' => $response));
}

/**
 * Gets all roles
 */
function getRoles()
{
	$response = \FATS\BLL\User::getRoles();
	echo json_encode(array('result' => $response));
}

/**
 * Gets all permissions
 */
function getPermissions()
{
	$response = \FATS\BLL\User::getPermissions();
	echo json_encode(array('result' => $response));
}

/**
 * Gets all faculty members
 */
function getAllFaculty()
{
	$response = \FATS\BLL\Faculty::getAllFaculty();
	echo json_encode(array('result' => $response));
}

/**
 * Gets a faculty member identified by $id
 *
 * @param $id
 */
function getFaculty($id)
{
	$response = \FATS\BLL\Faculty::getFaculty($id);
	echo json_encode(array('result' => $response));
}

/**
 * Creates a new faculty member
 */
function createFaculty()
{
	$request = \Slim\Slim::getInstance()->request();

	parse_str($request->getBody(), $params);

	$response = \FATS\BLL\Faculty::createFaculty($params['netid']);
	echo json_encode(array('result' => $response));
}

/**
 * Updates the faculty member information
 *
 * @param $id
 */
function updateFaculty($id)
{
	$request = \Slim\Slim::getInstance()->request();
	$data = json_decode($request->getBody());

	$faculty = \FATS\BLL\Faculty::updateFaculty($id, $data->netid, $data->name);

	echo json_encode(array('result' => $faculty));
}

/**
 * Deletes an existing faculty member
 *
 * @param $id
 */
function deleteFaculty($id)
{
	$response = \FATS\BLL\Faculty::deleteFaculty($id);
	echo json_encode(array('result' => $response));
}

/**
 * Retrieves a list of documents for a given category (folder)
 *
 * @param $id
 */
function getDocuments($id)
{
	$documents = \FATS\BLL\Documents::getDocuments($id);
	echo json_encode(array('result' => $documents));
}

/**
 * Prepares a document for download from the database
 *
 * @param $ids
 */
function downloadDocuments($ids)
{
	$url = \FATS\BLL\Documents::getDocumentsForDownload($ids);
	echo json_encode(array('result' => $url));
}

/**
 * Prepares all documents for the context faculty member for download from the database
 */
function downloadAllDocuments()
{
	$url = \FATS\BLL\Documents::getAllDocumentsForDownload();
	echo json_encode(array('result' => $url));
}

/**
 * Uploads a new document to the database
 */
function uploadDocument()
{
	$response = \FATS\BLL\Documents::uploadDocument();
	echo json_encode(array('result' => $response));
}

/**
 * Removes a document from the database
 *
 * @param $ids
 */
function deleteDocuments($ids)
{
	$response = \FATS\BLL\Documents::deleteDocument($ids);
	echo json_encode(array('result' => $response));
}

?>