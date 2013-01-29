<?php
/**
 * DataAccessLayer.php
 *
 * This class interacts directly with the application's MySQL database.
 *
 * PHP version 5
 *
 * @category   Web Application
 * @package    Faculty Advancement Tracking System
 * @author     Geoffrey Pagel <geoffrey.pagel@northwestern.edu>
 * @version    SVN: $Id$
 */
namespace FATS\DAL;

require_once 'Helpers/DatabaseHelper.php';
require_once 'Library/Observable.php';
require_once 'BLL/LogEntry.php';
require_once 'Diagnostics/LogOperation.php';
require_once 'Diagnostics/LogLevel.php';

class DataAccessLayer extends \FATS\Library\Observable
{
	private $_database;

	/**
	 * Initializes a new instance of the DataAccessLayer class
	 */
	function __construct()
	{
		$this->_database = \FATS\Helpers\DatabaseHelper::getInstance();
		parent::__construct();
	}

	/**
	 * @param \FATS\BLL\LogEntry $entry
	 */
	public function createLogEntry(\FATS\BLL\LogEntry $entry)
	{
		try
		{
			$contextUserObject = \FATS\Application\ObjectManager::getContextUser();
			$contextUserId = $contextUserObject->id;

			$sql = $this->_database->prepare('INSERT INTO log(users_id, log_level_id, log_operations_id, faculty_documents_id, description) VALUES (?, ?, ?, ?, ?)');
			$isSuccess = $sql->execute(array($contextUserId, $entry->logLevel, $entry->logOperation, $entry->facultyDocumentsId, $entry->logMessage));

			$logMessage = $entry->logMessage . '\nUser NetID: ' . $contextUserId . '\nLog Level: ' . $entry->logLevel . '\nLog Operation: ' . $entry->logOperation . '\nFaculty Document ID: ' . $entry->facultyDocumentsId;

			if (!$isSuccess)
			{
				// Attempt to add this entry to the system log
				syslog(LOG_ERR, $this->_database->errorInfo());
				syslog(LOG_NOTICE, $logMessage);
			}
		}
		catch (\Exception $x)
		{
			// Attempt to add this entry to the system log
			syslog(LOG_ERR, $x->getMessage());
			@syslog(LOG_NOTICE, $logMessage);
		}
	}

	/**
	 * Creates a new user account in the database
	 *
	 * @param $netid
	 * @param $name
	 * @param $email
	 * @param $role
	 * @param $permission
	 * @throws \Exception
	 * @return bool
	 */
	public function createUser($netid, $name, $email, $role, $permission)
	{
		try
		{
			// Verify that the NetID doesn't already exist in the database
			$sql = $this->_database->prepare('SELECT COUNT(*) FROM users WHERE netid = ? AND archive != 1');
			$isSuccess = $sql->execute(array($netid));

			if ($isSuccess)
			{
				if (intval($sql->fetchColumn()) !== 0)
				{
					throw new \Exception("User account already exists for $netid.");
				}
			}

			$sql = $this->_database->prepare('INSERT INTO users(netid, name, email, roles_id, permissions_id) VALUES (?, ?, ?, ?, ?)');
			$isSuccess = $sql->execute(array($netid, $name, $email, $role, $permission));

			if ($isSuccess)
			{
				$this->setState(new \FATS\BLL\LogEntry(\FATS\Diagnostics\LogOperation::CREATE, \FATS\Diagnostics\LogLevel::INFO, "Created user $netid"));

				return true;
			}

			throw new \Exception('The system is unable to add the user account. ' . implode(' : ', $this->_database->errorInfo()));
		}
		catch (\Exception $x)
		{
			$this->setState(new \FATS\BLL\LogEntry(\FATS\Diagnostics\LogOperation::CREATE, \FATS\Diagnostics\LogLevel::ERROR, "Failed to create user $netid"));
			trigger_error($x->getMessage(), E_USER_ERROR);

			return false;
		}
	}

	/**
	 * Gets the specified user account from the database
	 *
	 * @param $netid
	 * @throws \Exception
	 * @return \FATS\BLL\User|null
	 */
	public function getUser($netid)
	{
		try
		{
			$sql = $this->_database->prepare('SELECT * FROM users WHERE netid = ? AND archive != 1 LIMIT 1');
			$isSuccess = $sql->execute(array($netid));

			if ($isSuccess)
			{
				$user = $sql->fetchAll();
			}
			else
			{
				throw new \Exception('The system is unable to retrieve the user account. ' . implode(' : ', $this->_database->errorInfo()));
			}

			if (empty($user))
			{
				return null;
			}

			$this->setState('The system successfully retrieved the user account from the database.');

			return new \FATS\BLL\User($user[0]['id'], $user[0]['netid'], $user[0]['name'], $user[0]['email'], $user[0]['roles_id'], $user[0]['permissions_id']);
		}
		catch (\Exception $x)
		{
			$this->setState(new \FATS\BLL\LogEntry(\FATS\Diagnostics\LogOperation::READ, \FATS\Diagnostics\LogLevel::ERROR, "Failed to retrieve user $netid : {$x->getMessage()}"));
			trigger_error($x->getMessage(), E_USER_ERROR);

			return null;
		}
	}

	/**
	 * Gets all user accounts from the database
	 *
	 * @throws \Exception
	 * @return array|null
	 */
	public function getAllUsers()
	{
		try
		{
			$sql = $this->_database->prepare('SELECT * FROM users WHERE archive != 1');
			$isSuccess = $sql->execute();

			if ($isSuccess)
			{
				$users = $sql->fetchAll();
			}
			else
			{
				throw new \Exception('The system is unable to retrieve all user accounts. ' . implode(' : ', $this->_database->errorInfo()));
			}

			if (empty($users))
			{
				return null;
			}

			$this->setState('The system successfully retrieved all user accounts from the database.');

			$userAccounts = array();

			foreach ($users as $user)
			{
				array_push($userAccounts, new \FATS\BLL\User($user['id'], $user['netid'], $user['name'], $user['email'], $user['roles_id'], $user['permissions_id']));
			}

			return $userAccounts;
		}
		catch (\Exception $x)
		{
			$this->setState(new \FATS\BLL\LogEntry(\FATS\Diagnostics\LogOperation::READ, \FATS\Diagnostics\LogLevel::ERROR, "Failed to retrieve all user accounts : {$x->getMessage()}"));
			trigger_error($x->getMessage(), E_USER_ERROR);

			return null;
		}
	}

	/**
	 * Updates the specified user account
	 *
	 * @param $id
	 * @param $netid
	 * @param $name
	 * @param $email
	 * @param $role
	 * @param $permission
	 * @throws \Exception
	 * @return bool
	 */
	public function updateUser($id, $netid, $name, $email, $role, $permission)
	{
		/**
		 * Reject attempts by the current user to modify their own account
		 */
		$currentUser = \FATS\Application\ObjectManager::getContextUser();

		if ($id === $currentUser->id)
		{
			return 'The system cannot update a context (currently active) user account.';
		}

		try
		{
			$sql = $this->_database->prepare('UPDATE users SET netid = ?, name = ?, email = ?, roles_id = ?, permissions_id = ? WHERE id = ?');
			$isSuccess = $sql->execute(array($netid, $name, $email, $role, $permission, $id));

			if ($isSuccess)
			{
				$this->setState(new \FATS\BLL\LogEntry(\FATS\Diagnostics\LogOperation::UPDATE, \FATS\Diagnostics\LogLevel::INFO, "Updated user ID $id"));

				return true;
			}

			throw new \Exception('The system is unable to update the user account. ' . implode(' : ', $this->_database->errorInfo()));
		}
		catch (\Exception $x)
		{
			$this->setState(new \FATS\BLL\LogEntry(\FATS\Diagnostics\LogOperation::UPDATE, \FATS\Diagnostics\LogLevel::ERROR, "Failed to update user ID $id : {$x->getMessage()}"));
			trigger_error($x->getMessage(), E_USER_ERROR);

			return false;
		}
	}

	/**
	 * Archives the specified user account
	 *
	 * @param $id
	 * @throws \Exception
	 * @return bool|string
	 */
	public function deleteUser($id)
	{
		/**
		 * Reject attempts by the current user to delete their own account
		 */
		$currentUser = \FATS\Application\ObjectManager::getContextUser();

		if ($id === $currentUser->id)
		{
			return 'The system cannot delete a context (currently active) user account.';
		}

		try
		{
			$sql = $this->_database->prepare('UPDATE users SET archive = 1 WHERE id = ? LIMIT 1');
			$isSuccess = $sql->execute(array($id));

			if ($isSuccess)
			{
				$this->setState(new \FATS\BLL\LogEntry(\FATS\Diagnostics\LogOperation::DELETE, \FATS\Diagnostics\LogLevel::INFO, "Archived user ID $id"));

				return true;
			}
			else
			{
				throw new \Exception('The system is unable to delete the user account. ' . implode(' : ', $this->_database->errorInfo()));
			}
		}
		catch (\Exception $x)
		{
			$this->setState(new \FATS\BLL\LogEntry(\FATS\Diagnostics\LogOperation::DELETE, \FATS\Diagnostics\LogLevel::ERROR, "Failed to archive user ID $id : {$x->getMessage()}"));
			trigger_error($x->getMessage());

			return false;
		}
	}

	/**
	 * Gets the roles from the database
	 *
	 * @throws \Exception
	 * @return null
	 */
	public function getRoles()
	{
		try
		{
			$sql = $this->_database->prepare('SELECT * FROM roles');
			$isSuccess = $sql->execute();

			if ($isSuccess)
			{
				$roles = $sql->fetchAll();
			}
			else
			{
				throw new \Exception('The system is unable to retrieve roles. ' . implode(' : ', $this->_database->errorInfo()));
			}

			if (empty($roles))
			{
				return null;
			}

			$this->setState('The system successfully retrieved roles from the database.');

			return $roles;
		}
		catch (\Exception $x)
		{
			$this->setState(new \FATS\BLL\LogEntry(\FATS\Diagnostics\LogOperation::READ, \FATS\Diagnostics\LogLevel::ERROR, "Failed to retrieve all roles : {$x->getMessage()}"));
			trigger_error($x->getMessage(), E_USER_ERROR);

			return null;
		}
	}

	/**
	 * Gets the role friendly name for a given role id
	 * @param $id
	 * @return null
	 * @throws \Exception
	 */
	public function getRoleFriendlyName($id)
	{
		try
		{
			$sql = $this->_database->prepare('SELECT description FROM roles WHERE id = ?');
			$isSuccess = $sql->execute(array($id));

			if ($isSuccess)
			{
				$roleFriendlyName = $sql->fetchColumn();
			}
			else
			{
				throw new \Exception('The system is unable to retrieve the role friendly name. ' . implode(' : ', $this->_database->errorInfo()));
			}

			$this->setState('The system successfully retrieved the role friendly name from the database.');

			return $roleFriendlyName;
		}
		catch (\Exception $x)
		{
			$this->setState(new \FATS\BLL\LogEntry(\FATS\Diagnostics\LogOperation::READ, \FATS\Diagnostics\LogLevel::ERROR, "Failed to retrieve role friendly name : {$x->getMessage()}"));
			trigger_error($x->getMessage(), E_USER_ERROR);

			return null;
		}
	}

	/**
	 * Gets the permissions from the database
	 *
	 * @return null
	 * @throws \Exception
	 */
	public function getPermissions()
	{
		try
		{
			$sql = $this->_database->prepare('SELECT * FROM permissions');
			$isSuccess = $sql->execute();

			if ($isSuccess)
			{
				$permissions = $sql->fetchAll();
			}
			else
			{
				throw new \Exception('The system is unable to retrieve permissions. ' . implode(' : ', $this->_database->errorInfo()));
			}

			if (empty($permissions))
			{
				return null;
			}

			$this->setState('The system successfully retrieved permissions from the database.');

			return $permissions;
		}
		catch (\Exception $x)
		{
			$this->setState(new \FATS\BLL\LogEntry(\FATS\Diagnostics\LogOperation::READ, \FATS\Diagnostics\LogLevel::ERROR, "Failed to retrieve all permissions : {$x->getMessage()}"));
			trigger_error($x->getMessage(), E_USER_ERROR);

			return null;
		}
	}

	/**
	 * Creates a new faculty member in the database
	 *
	 * @param $netid
	 * @param $name
	 * @throws \Exception
	 * @return bool
	 */
	public function createFaculty($netid, $name)
	{
		try
		{
			// Verify that the NetID doesn't already exist in the database
			$sql = $this->_database->prepare('SELECT COUNT(*) FROM faculty WHERE netid = ? AND archive != 1');
			$isSuccess = $sql->execute(array($netid));

			if ($isSuccess)
			{
				if (intval($sql->fetchColumn()) !== 0)
				{
					throw new \Exception('Faculty member already exists in the database.');
				}
			}

			$sql = $this->_database->prepare('INSERT INTO faculty (netid, name) VALUES (?, ?)');
			$isSuccess = $sql->execute(array($netid, $name));

			if ($isSuccess)
			{
				$this->setState(new \FATS\BLL\LogEntry(\FATS\Diagnostics\LogOperation::CREATE, \FATS\Diagnostics\LogLevel::INFO, "Created faculty member $netid"));

				return true;
			}

			throw new \Exception('The system is unable to add the faculty member. ' . implode(' : ', $this->_database->errorInfo()));
		}
		catch (\Exception $x)
		{
			$this->setState(new \FATS\BLL\LogEntry(\FATS\Diagnostics\LogOperation::CREATE, \FATS\Diagnostics\LogLevel::ERROR, "Failed to create faculty member $netid"));
			trigger_error($x->getMessage(), E_USER_ERROR);

			return false;
		}
	}

	/**
	 * Gets the specified faculty member from the database
	 *
	 * @param $id
	 * @throws \Exception
	 * @return bool
	 */
	public function getFaculty($id)
	{
		try
		{
			$sql = $this->_database->prepare('SELECT * FROM faculty WHERE id = ? LIMIT 1');
			$isSuccess = $sql->execute(array($id));

			if ($isSuccess)
			{
				$faculty = $sql->fetchAll();
			}
			else
			{
				throw new \Exception('The system is unable to retrieve the faculty member. ' . implode(' : ', $this->_database->errorInfo()));
			}

			if (empty($faculty))
			{
				return false;
			}

			$this->setState('The system successfully retrieved the faculty member from the database.');

			\FATS\Application\ObjectManager::setContextFaculty(new \FATS\BLL\Faculty($faculty[0]['id'], $faculty[0]['netid'], $faculty[0]['name']));

			return true;
		}
		catch (\Exception $x)
		{
			$this->setState(new \FATS\BLL\LogEntry(\FATS\Diagnostics\LogOperation::READ, \FATS\Diagnostics\LogLevel::ERROR, "Failed to retrieve faculty member ID $id : {$x->getMessage()}"));
			trigger_error($x->getMessage(), E_USER_ERROR);

			return false;
		}
	}

	/**
	 * Gets all faculty members from the database
	 *
	 * @throws \Exception
	 * @return array|null
	 */
	public function getAllFaculty()
	{
		try
		{
			$sql = $this->_database->prepare('SELECT * FROM faculty WHERE archive != 1');
			$isSuccess = $sql->execute();

			if ($isSuccess)
			{
				$faculty = $sql->fetchAll();
			}
			else
			{
				throw new \Exception('The system is unable to retrieve all faculty members. ' . implode(' : ', $this->_database->errorInfo()));
			}

			if (empty($faculty))
			{
				return null;
			}

			$this->setState('The system successfully retrieved all faculty members from the database.');

			$facultyMembers = array();

			foreach ($faculty as $f)
			{
				array_push($facultyMembers, new \FATS\BLL\Faculty($f['id'], $f['netid'], $f['name']));
			}

			return $facultyMembers;
		}
		catch (\Exception $x)
		{
			$this->setState(new \FATS\BLL\LogEntry(\FATS\Diagnostics\LogOperation::READ, \FATS\Diagnostics\LogLevel::ERROR, "Failed to retrieve all faculty members : {$x->getMessage()}"));
			trigger_error($x->getMessage(), E_USER_ERROR);

			return null;
		}
	}

	/**
	 * Updates the specified faculty member
	 *
	 * @param $id
	 * @param $netid
	 * @param $name
	 * @throws \Exception
	 * @return bool
	 */
	public function updateFaculty($id, $netid, $name)
	{
		try
		{
			$sql = $this->_database->prepare('UPDATE faculty SET netid = ?, name = ? WHERE id = ?');
			$isSuccess = $sql->execute(array($netid, $name, $id));

			if ($isSuccess)
			{
				$this->setState(new \FATS\BLL\LogEntry(\FATS\Diagnostics\LogOperation::UPDATE, \FATS\Diagnostics\LogLevel::INFO, "Updated faculty member ID $id"));

				return true;
			}

			throw new \Exception('The system is unable to update the faculty member. ' . implode(' : ', $this->_database->errorInfo()));
		}
		catch (\Exception $x)
		{
			$this->setState(new \FATS\BLL\LogEntry(\FATS\Diagnostics\LogOperation::UPDATE, \FATS\Diagnostics\LogLevel::ERROR, "Failed to update faculty member ID $id : {$x->getMessage()}"));
			trigger_error($x->getMessage(), E_USER_ERROR);

			return false;
		}
	}

	/**
	 * Archives the specified faculty member
	 *
	 * @param $id
	 * @throws \Exception
	 * @return bool
	 */
	public function deleteFaculty($id)
	{
		try
		{
			$sql = $this->_database->prepare('UPDATE faculty SET archive = 1 WHERE id = ? LIMIT 1');
			$facultyMemberIsArchived = $sql->execute(array($id));

			$sql = $this->_database->prepare('UPDATE faculty_documents SET archive = 1 WHERE faculty_id = ?');
			$facultyDocumentsIsArchived = $sql->execute(array($id));

			$isSuccess = $facultyMemberIsArchived && $facultyDocumentsIsArchived;

			if ($isSuccess)
			{
				$this->setState(new \FATS\BLL\LogEntry(\FATS\Diagnostics\LogOperation::DELETE, \FATS\Diagnostics\LogLevel::INFO, "Archived faculty member ID $id"));

				return true;
			}
			else
			{
				throw new \Exception('The system is unable to delete the faculty member. ' . implode(' : ', $this->_database->errorInfo()));
			}
		}
		catch (\Exception $x)
		{
			$this->setState(new \FATS\BLL\LogEntry(\FATS\Diagnostics\LogOperation::DELETE, \FATS\Diagnostics\LogLevel::ERROR, "Failed to archive faculty member ID $id : {$x->getMessage()}"));
			trigger_error($x->getMessage(), E_USER_ERROR);

			return false;
		}
	}

	/**
	 * Creates a new document in the database
	 *
	 * @throws \Exception
	 * @return bool
	 */
	public function createDocument()
	{
		try
		{
			$uploaddir = '/var/www/sites/fats/uploads/';
			$uploadfile = $uploaddir . basename($_FILES['document']['name']);
			$acceptableFileTypes = array('application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

			$faculty = \FATS\Application\ObjectManager::getContextFaculty();
			$folder = \FATS\Application\ObjectManager::getContextFolder();

			if (move_uploaded_file($_FILES['document']['tmp_name'], $uploadfile))
			{
				$fileName = basename($uploadfile);
				$fileSize = filesize($uploadfile);

				$fileTypeArray = explode(';', exec('file -bi ' . escapeshellarg($uploadfile), $foo, $returnCode));
				$fileType = $fileTypeArray[0];

				if ($returnCode !== 0 || !in_array($fileType, $acceptableFileTypes))
				{
					throw new \Exception("Error: Invalid file type. Valid types include PDF, DOC, and DOCX. ($fileType)");
				}

				$handle = fopen($uploadfile, 'r');

				if ($handle === false)
				{
					throw new \Exception('Error: The system is unable to open the file for reading.', E_USER_ERROR);
				}

				$raw_data = fread($handle, $fileSize);

				if ($raw_data === false)
				{
					throw new \Exception('Error: The system is unable to read the file.', E_USER_ERROR);
				}

				/**
				 * First, execute a query to get the document type
				 */
				$sql = $this->_database->prepare('SELECT id FROM faculty_documents_type WHERE mimetype = ?');
				$isSuccess = $sql->execute(array($fileType));

				if (!$isSuccess)
				{
					throw new \Exception('Unable to upload the document to the database.');
				}

				$fileName = preg_replace('/[^a-zA-Z0-9\-_\.]/', '_', $fileName);
				$mimetype = $sql->fetchColumn();

				$sql = $this->_database->prepare('INSERT INTO faculty_documents (faculty_id, faculty_documents_category_id, faculty_documents_type_id, file_name, file_size, file_data) values (?, ?, ?, ?, ?, ?)');

				$sql->bindParam(1, $faculty->id);
				$sql->bindParam(2, $folder->id);
				$sql->bindParam(3, $mimetype);
				$sql->bindParam(4, $fileName);
				$sql->bindParam(5, $fileSize);
				$sql->bindParam(6, $raw_data, \PDO::PARAM_LOB);

				$this->_database->beginTransaction();
				$sql->execute();
				$this->_database->commit();

				$this->setState(new \FATS\BLL\LogEntry(\FATS\Diagnostics\LogOperation::UPLOAD, \FATS\Diagnostics\LogLevel::INFO, "Uploaded new document $uploadfile into folder ID {$folder->id} for faculty member ID {$faculty->id}", $folder->id));
				unlink($uploadfile);

				return true;
			}
			else
			{
				throw new \Exception("Failed to upload new document $uploadfile into folder ID {$folder->id} for faculty member ID {$faculty->id}");
			}
		}
		catch (\Exception $x)
		{
			$this->setState(new \FATS\BLL\LogEntry(\FATS\Diagnostics\LogOperation::DELETE, \FATS\Diagnostics\LogLevel::ERROR, $x->getMessage()));
			trigger_error($x->getMessage(), E_USER_ERROR);
			@unlink($uploadfile);

			return false;
		}
	}

	/**
	 * Gets the specified document(s) from the database
	 *
	 * @param $id
	 * @throws \Exception
	 * @return null
	 */
	public function getDocuments($id)
	{
		$faculty = \FATS\Application\ObjectManager::getContextFaculty();

		try
		{
			// Set the context folder
			$sql = $this->_database->prepare('SELECT * FROM faculty_documents_category WHERE id = ? LIMIT 1');
			$isSuccess = $sql->execute(array($id));

			if ($isSuccess)
			{
				$folder = $sql->fetch(\PDO::FETCH_ASSOC);

				if (empty($folder))
				{
					throw new \Exception('The system is unable to determine the folder from which to retrieve documents.');
				}

				\FATS\Application\ObjectManager::setContextFolder(new \FATS\BLL\Folders($folder['id'], $folder['title'], $folder['rgt'], $folder['lft']));
			}
			else
			{
				throw new \Exception('The system is unable to retrieve the documents. ' . implode(' : ', $this->_database->errorInfo()));
			}

			unset($sql, $isSuccess);

			$sql = $this->_database->prepare('SELECT a.id, a.file_name, a.file_size, b.mimetype FROM faculty_documents AS a LEFT JOIN faculty_documents_type AS b ON a.faculty_documents_type_id = b.id WHERE a.faculty_documents_category_id = ? AND a.faculty_id = ? AND a.archive != 1');
			$isSuccess = $sql->execute(array($id, $faculty->id));

			if ($isSuccess)
			{
				$documents = $sql->fetchAll();
			}
			else
			{
				throw new \Exception('The system is unable to retrieve the documents. ' . implode(' : ', $this->_database->errorInfo()));
			}

			if (empty($documents))
			{
				return null;
			}

			$this->setState('The system successfully retrieved the documents from the database.');

			return $documents;
		}
		catch (\Exception $x)
		{
			$this->setState(new \FATS\BLL\LogEntry(\FATS\Diagnostics\LogOperation::READ, \FATS\Diagnostics\LogLevel::ERROR, "Failed to retrieve all documents in folder ID $id for faculty member ID {$faculty->id} : {$x->getMessage()}"));
			trigger_error($x->getMessage(), E_USER_ERROR);

			return null;
		}
	}

	/**
	 * Gets the specified document(s) from the database and prepares them for user download
	 *
	 * @param $ids
	 * @throws \Exception
	 * @return null|string
	 */
	public function getDocumentsForDownload($ids)
	{
		$idList = explode('-', $ids);

		// Create a temporary directory for the downloads
		$downloadsDir = '/var/www/sites/fats/www/downloads/';
		$downloadsTempDir = time();

		$tempDir = $downloadsDir . $downloadsTempDir;

		if (!mkdir($tempDir))
		{
			throw new \Exception('Cannot create temporary folder for download.');
		}

		$files = array();

		try
		{
			foreach ($idList as $id)
			{
				$sql = $this->_database->prepare('SELECT file_name, b.mimetype, file_data FROM faculty_documents a LEFT JOIN faculty_documents_type b ON a.faculty_documents_type_id = b.id WHERE a.id = ? AND a.archive != 1');
				$isSuccess = $sql->execute(array($id));

				if (!$isSuccess)
				{
					throw new \Exception('Unable to retrieve the documents from the database.');
				}

				$sql->bindColumn(1, $name);
				$sql->bindColumn(2, $type);
				$sql->bindColumn(3, $data, \PDO::PARAM_LOB);

				if (!file_exists($tempDir))
				{
					throw new \Exception('Destination temporary folder does not exist.');
				}

				while ($sql->fetch())
				{
					$path = $tempDir . '/' . $name;
					$bytes = file_put_contents($path, $data);

					$info = array('path' => $path, 'type' => $type, 'size' => $bytes);

					array_push($files, $info);
				}
			}

			/**
			 * Create a zip file containing the documents
			 */
			$zip = new \ZipArchive();
			$faculty = \FATS\Application\ObjectManager::getContextFaculty();
			$filename = $downloadsDir . $faculty->netid . '.' . time() . '.zip';

			if ($zip->open($filename, \ZIPARCHIVE::CREATE) !== true)
			{
				throw new \Exception('Unable to create file for download.');
			}

			if (!empty($files))
			{
				foreach ($files as $file)
				{
					$zip->addFile($file['path'], basename($file['path']));
				}
			}

			$this->setState(new \FATS\BLL\LogEntry(\FATS\Diagnostics\LogOperation::DOWNLOAD, \FATS\Diagnostics\LogLevel::INFO, "Downloaded document IDs $ids"));

			$url = 'http://' . $_SERVER['HTTP_HOST'] . '/downloads/' . basename($filename);

			return $url;
		}
		catch (\Exception $x)
		{
			$this->setState(new \FATS\BLL\LogEntry(\FATS\Diagnostics\LogOperation::READ, \FATS\Diagnostics\LogLevel::ERROR, "Failed to prepare documents for download : {$x->getMessage()}"));
			trigger_error($x->getMessage(), E_USER_ERROR);

			return null;
		}
	}

	/**
	 * Gets all available documents for the context faculty member and prepares them for user download
	 *
	 * @return null|string
	 * @throws \Exception
	 */
	public function getAllDocumentsForDownload()
	{
		// If the context faculty member or user is undefined, we can't produce a download
		$faculty = \FATS\Application\ObjectManager::getContextFaculty();

		if ($faculty === null)
		{
			throw new \Exception('Cannot determine context (currently selected) faculty member.');
		}

		$user = \FATS\Application\ObjectManager::getContextUser();

		if ($user === null)
		{
			throw new \Exception('Cannot determine context (currently logged in) user');
		}

		// Create a temporary directory for the downloads
		$downloadsDir = '/var/www/sites/fats/www/downloads/';
		$downloadsTempDir = time();

		$tempDir = $downloadsDir . $downloadsTempDir;

		if (!mkdir($tempDir))
		{
			throw new \Exception('Cannot create temporary folder for download.');
		}

		$files = array();

		try
		{
			$sql = $this->_database->prepare('SELECT file_name, b.mimetype, file_data FROM faculty_documents a LEFT JOIN faculty_documents_type b ON a.faculty_documents_type_id = b.id LEFT JOIN faculty_documents_access c ON a.faculty_documents_category_id = c.faculty_documents_category_id WHERE a.faculty_id = ? AND c.roles_id = ? AND a.archive != 1');
			$isSuccess = $sql->execute(array($faculty->id, $user->role));

			if (!$isSuccess)
			{
				throw new \Exception('Unable to retrieve the documents from the database.');
			}

			$sql->bindColumn(1, $name);
			$sql->bindColumn(2, $type);
			$sql->bindColumn(3, $data, \PDO::PARAM_LOB);

			if (!file_exists($tempDir))
			{
				throw new \Exception('Destination temporary folder does not exist.');
			}

			while ($sql->fetch())
			{
				$path = $tempDir . '/' . $name;
				$bytes = file_put_contents($path, $data);

				$info = array('path' => $path, 'type' => $type, 'size' => $bytes);

				array_push($files, $info);
			}

			/**
			 * Create a zip file containing the documents
			 */
			$zip = new \ZipArchive();
			$filename = $downloadsDir . $faculty->netid . '.' . time() . '.zip';

			if ($zip->open($filename, \ZIPARCHIVE::CREATE) !== true)
			{
				throw new \Exception('Unable to create file for download.');
			}

			if (!empty($files))
			{
				foreach ($files as $file)
				{
					$zip->addFile($file['path'], basename($file['path']));
				}
			}

			$this->setState(new \FATS\BLL\LogEntry(\FATS\Diagnostics\LogOperation::DOWNLOAD, \FATS\Diagnostics\LogLevel::INFO, "Downloaded all documents for the context faculty member"));

			$url = 'http://' . $_SERVER['HTTP_HOST'] . '/downloads/' . basename($filename);

			return $url;
		}
		catch (\Exception $x)
		{
			$this->setState(new \FATS\BLL\LogEntry(\FATS\Diagnostics\LogOperation::READ, \FATS\Diagnostics\LogLevel::ERROR, "Failed to prepare documents for download : {$x->getMessage()}"));
			trigger_error($x->getMessage(), E_USER_ERROR);

			return null;
		}
	}

	/**
	 * Archives the specified document(s)
	 *
	 * @param $ids
	 * @throws \Exception
	 * @return bool
	 */
	public function deleteDocument($ids)
	{
		try
		{
			$idList = explode('-', $ids);
			$errors = 0;

			foreach ($idList as $id)
			{
				$sql = $this->_database->prepare('UPDATE faculty_documents SET archive = 1 WHERE id = ? LIMIT 1');
				$isSuccess = $sql->execute(array($id));

				if (!$isSuccess)
				{
					$errors++;
				}
			}

			if ($errors == 0)
			{
				$this->setState(new \FATS\BLL\LogEntry(\FATS\Diagnostics\LogOperation::DELETE, \FATS\Diagnostics\LogLevel::INFO, "Archived document ID(s) $ids"));

				return true;
			}
			else
			{
				throw new \Exception('The system is unable to delete the document(s). ' . implode(' : ', $this->_database->errorInfo()));
			}
		}
		catch (\Exception $x)
		{
			$this->setState(new \FATS\BLL\LogEntry(\FATS\Diagnostics\LogOperation::DELETE, \FATS\Diagnostics\LogLevel::ERROR, "Failed to archive one or more document ID(s) $ids : {$x->getMessage()}"));
			trigger_error($x->getMessage(), E_USER_ERROR);

			return false;
		}
	}

	/**
	 * Rebuilds the document navigation tree using modified preorder tree traversal
	 *
	 * @param $parent
	 * @param $left
	 * @return mixed
	 */
	public function rebuildTree($parent, $left)
	{
		$right = $left + 1;

		$sql = $this->_database->prepare('SELECT title FROM faculty_documents_category WHERE parent = ?');
		$isSuccess = $sql->execute(array($parent));

		if ($isSuccess)
		{
			$rows = $sql->fetchAll(\PDO::FETCH_OBJ);

			foreach ($rows as $row)
			{
				$title = $row->title;
				$right = $this->rebuildTree($title, $right);
			}

			$query = $this->_database->prepare('UPDATE faculty_documents_category SET lft = ?, rgt = ? WHERE title = ?');
			$isQuerySuccess = $query->execute(array($left, $right, $parent));

			if ($isQuerySuccess)
			{
				return $right + 1;
			}
		}
	}

	/**
	 * Composes and renders the navigation tree
	 *
	 * @throws \Exception
	 * @return null|string
	 */
	function displayTree()
	{
		try
		{
			$roleId = \FATS\Application\ObjectManager::getContextUser()->role;

			if (!isset($roleId))
			{
				throw new \Exception('Cannot display navigation tree for empty or missing role ID.');
			}

			$sql = $this->_database->prepare('SELECT node.*, (COUNT(parent.title) - 1) AS depth FROM faculty_documents_category AS node CROSS JOIN faculty_documents_category AS parent LEFT JOIN faculty_documents_access AS access ON node.id = access.faculty_documents_category_id WHERE node.lft BETWEEN parent.lft AND parent.rgt AND access.roles_id = ? GROUP BY node.id ORDER BY node.lft');
			$sql->execute(array($roleId));

			$result = $sql->fetchAll();

			$tree = array();

			foreach ($result as $row)
			{
				$tree[] = $row;
			}

			$html = '';
			$currentDepth = -1;

			while (!empty($tree))
			{
				unset($sql, $result);

				$currentNode = array_shift($tree);

				if ($currentNode['depth'] > $currentDepth)
				{
					if ($currentDepth == -1)
					{
						$html .= '<ul class="navigation-tree">';
					}
					else
					{
						$html .= '<ul>';
					}
				}

				if ($currentNode['depth'] < $currentDepth)
				{
					$html .= str_repeat('</ul>', $currentDepth - $currentNode['depth']);
				}

				/**
				 * If the current node has children, add a CSS class to expand/collapse it
				 */
				$sql = $this->_database->prepare('SELECT COUNT(*) AS cnt FROM faculty_documents_category WHERE parent_id = ?');
				$sql->execute(array($currentNode['id']));
				$result = $sql->fetchColumn();

				$class = '';
				$icon = '';

				if (intval($result) > 0)
				{
					$class = ' class="navigation-toggle collapsed"';
					$icon = '<span class="icon-toggle-nav"></span>';
				}

				$title = $currentNode['title'];

				if (strtolower($title) == 'root')
				{
					$faculty = \FATS\Application\ObjectManager::getContextFaculty();
					$title = $faculty->name;
					$class = ' class="navigation-toggle"';
				}

				$html .= '<li class="navigation-item" data-id="' . $currentNode['id'] . '" data-parent="' . $currentNode['parent_id'] . '" data-lft="' . $currentNode['lft'] . '" data-rgt="' . $currentNode['rgt'] . '">' . $icon . '<a' . $class . ' href="#">' . $title . '</a>';
				$currentDepth = $currentNode['depth'];

				if (empty($tree))
				{
					$html .= str_repeat('</li></ul>', $currentDepth + 1);
				}
			}

			return $html;
		}
		catch (\Exception $x)
		{
			$this->setState(new \FATS\BLL\LogEntry(\FATS\Diagnostics\LogOperation::READ, \FATS\Diagnostics\LogLevel::ERROR, "Failed to display navigation tree : {$x->getMessage()}"));
			trigger_error($x->getMessage(), E_USER_ERROR);

			return false;
		}
	}

	/**
	 * Gets the folders within the specified area of the navigation tree
	 *
	 * @param int $leftMin
	 * @param int $leftMax
	 * @throws \Exception
	 * @return bool|null
	 */
	function getFolders($leftMin = null, $leftMax = null)
	{
		try
		{
			$roleId = \FATS\Application\ObjectManager::getContextUser()->role;

			if (!isset($roleId))
			{
				throw new \Exception('Cannot display navigation tree for empty or missing role ID.');
			}

			// TODO:  Handle the case wherein only one of the parameter values is null.
			if (null === $leftMin && null === $leftMax)
			{
				$sql = $this->_database->prepare('SELECT node.id, node.title, node.rgt, node.lft, (COUNT(parent.id) - 1) AS depth FROM faculty_documents_category AS node CROSS JOIN faculty_documents_category AS parent LEFT JOIN faculty_documents_access AS access ON node.id = access.faculty_documents_category_id WHERE (node.lft BETWEEN parent.lft AND parent.rgt) AND access.roles_id = ? GROUP BY node.id HAVING depth = 1 ORDER BY node.lft');
				$isSuccess = $sql->execute(array($roleId));
			}
			else
			{
				$sql = $this->_database->prepare('SELECT node.id, node.title, node.rgt, node.lft FROM faculty_documents_category AS node LEFT JOIN faculty_documents_access AS access ON node.id = access.faculty_documents_category_id WHERE lft BETWEEN ? AND ? AND parent != "Root" AND access.roles_id = ? ORDER BY lft ASC');
				$isSuccess = $sql->execute(array($leftMin, $leftMax, $roleId));
			}

			if ($isSuccess)
			{
				$folders = $sql->fetchAll();
			}
			else
			{
				throw new \Exception('The system is unable to retrieve the folders. ' . implode(' : ', $this->_database->errorInfo()));
			}

			if (empty($folders))
			{
				return null;
			}

			$folderList = array();

			foreach ($folders as $f)
			{
				array_push($folderList, new \FATS\BLL\Folders($f['id'], $f['title'], $f['rgt'], $f['lft']));
			}

			$this->setState('The system successfully retrieved the folders from the database.');

			return $folderList;
		}
		catch (\Exception $x)
		{
			$this->setState(new \FATS\BLL\LogEntry(\FATS\Diagnostics\LogOperation::READ, \FATS\Diagnostics\LogLevel::ERROR, "Failed to retrive folders : {$x->getMessage()}"));
			trigger_error($x->getMessage(), E_USER_ERROR);

			return false;
		}
	}
}

?>