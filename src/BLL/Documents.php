<?php
/**
 * Documents.php
 *
 * This class contains methods to perform CRUD operations for documents.
 *
 * PHP version 5
 *
 * @category   Web Application
 * @package    Faculty Advancement Tracking System
 * @author     Geoffrey Pagel <geoffrey.pagel@northwestern.edu>
 * @version    SVN: $Id$
 */
namespace FATS\BLL;

require_once 'DAL/DataAccessLayer.php';
require_once 'DAL/DataTransactionMonitor.php';
require_once 'Library/Observable.php';

class Documents extends \FATS\Library\Observable
{
	/**
	 * Document ID
	 *
	 * @var int
	 */
	protected $id;

	/**
	 * Document title (name)
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Document MIME type
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * Document file size
	 *
	 * @var int
	 */
	protected $size;

	/**
	 * Initializes a new instance of the Documents class
	 *
	 * @param $id
	 * @param $name
	 * @param $type
	 * @param $size
	 */
	function __construct($id, $name, $type, $size)
	{
		$this->id = $id;
		$this->name = $name;
		$this->type = $type;
		$this->size = $size;
	}

	/**
	 * Gets the specified folder from the database
	 *
	 * @param $id
	 * @return bool|null
	 */
	public static function getFolders($id)
	{
		$dal = new \FATS\DAL\DataAccessLayer();

		/**
		 * Attach the event handler
		 */
		new \FATS\DAL\DataTransactionMonitor($dal);

		return $dal->getFolders($id);
	}

	/**
	 * Gets the specified document from the database
	 *
	 * @param $id
	 * @return null
	 */
	public static function getDocuments($id)
	{
		$dal = new \FATS\DAL\DataAccessLayer();

		/**
		 * Attach the event handler
		 */
		new \FATS\DAL\DataTransactionMonitor($dal);

		return $dal->getDocuments($id);
	}

	/**
	 * Gets the specified documents from the database and prepares them for user download
	 *
	 * @param $ids
	 * @return null|string
	 */
	public static function getDocumentsForDownload($ids)
	{
		$dal = new \FATS\DAL\DataAccessLayer();

		/**
		 * Attach the event handler
		 */
		new \FATS\DAL\DataTransactionMonitor($dal);

		return $dal->getDocumentsForDownload($ids);
	}

	/**
	 * Gets all documents for the context faculty member and prepares them for user download
	 *
	 * @return null|string
	 */
	public static function getAllDocumentsForDownload()
	{
		$dal = new \FATS\DAL\DataAccessLayer();

		/**
		 * Attach the event handler
		 */
		new \FATS\DAL\DataTransactionMonitor($dal);

		return $dal->getAllDocumentsForDownload();
	}

	/**
	 * Uploads a document to the database
	 *
	 * @return bool
	 */
	public static function uploadDocument()
	{
		$dal = new \FATS\DAL\DataAccessLayer();

		/**
		 * Attach the event handler
		 */
		new \FATS\DAL\DataTransactionMonitor($dal);

		return $dal->createDocument();
	}

	/**
	 * Archives the specified document
	 *
	 * @param $ids
	 * @return bool
	 */
	public static function deleteDocument($ids)
	{
		$dal = new \FATS\DAL\DataAccessLayer();

		/**
		 * Attach the event handler
		 */
		new \FATS\DAL\DataTransactionMonitor($dal);

		return $dal->deleteDocument($ids);
	}
}

?>