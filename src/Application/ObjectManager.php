<?php
/**
 * ObjectManager.php
 *
 * This class manages objects that persist throughout the application.
 *
 * PHP version 5
 *
 * @category   Web Application
 * @package    Faculty Advancement Tracking System
 * @author     Geoffrey Pagel <geoffrey.pagel@northwestern.edu>
 * @version    SVN: $Id$
 */
namespace FATS\Application;

require_once 'BLL/User.php';
require_once 'BLL/Faculty.php';
require_once 'BLL/Folders.php';

class ObjectManager
{
	/**
	 * Sets the context (currently logged in) user
	 *
	 * @param \FATS\BLL\User $user
	 * @throws \InvalidArgumentException
	 */
	public static function setContextUser(\FATS\BLL\User $user)
	{
		if (!$user instanceof \FATS\BLL\User)
		{
			throw new \InvalidArgumentException('Cannot set context user.');
		}

		$serializedObject = serialize($user);

		$_SESSION['context']['user'] = $serializedObject;
	}

	/**
	 * Gets the context (currently logged in) user
	 * @return mixed
	 */
	public static function getContextUser()
	{
		if (!isset($_SESSION['context']['user']))
		{
			return null;
		}

		$serializedObject = $_SESSION['context']['user'];

		if (isset($serializedObject))
		{
			$user = unserialize($serializedObject);

			return $user;
		}
	}

	/**
	 * Sets the context (currently selected) faculty member
	 *
	 * @param \FATS\BLL\Faculty $faculty
	 * @throws \InvalidArgumentException
	 */
	public static function setContextFaculty(\FATS\BLL\Faculty $faculty)
	{
		if (!$faculty instanceof \FATS\BLL\Faculty)
		{
			throw new \InvalidArgumentException('Cannot set context faculty.');
		}

		$serializedObject = serialize($faculty);

		$_SESSION['context']['faculty'] = $serializedObject;
	}

	/**
	 * Gets the context (currently selected) faculty member
	 *
	 * @return mixed
	 */
	public static function getContextFaculty()
	{
		if (!isset($_SESSION['context']['faculty']))
		{
			return null;
		}

		$serializedObject = $_SESSION['context']['faculty'];

		if (isset($serializedObject))
		{
			$faculty = unserialize($serializedObject);

			return $faculty;
		}
	}

	/**
	 * Clears the context (currently selected) faculty member
	 */
	public static function clearContextFaculty()
	{
		unset($_SESSION['context']['faculty']);
	}

	/**
	 * Sets the context (currently selected) folder
	 *
	 * @param \FATS\BLL\Folders $folder
	 * @return mixed
	 * @throws \InvalidArgumentException
	 */
	public static function setContextFolder(\FATS\BLL\Folders $folder)
	{
		if (!$folder instanceof \FATS\BLL\Folders)
		{
			throw new \InvalidArgumentException('Cannot set context folder.');
		}

		$serializedObject = serialize($folder);

		$_SESSION['context']['folder'] = $serializedObject;
	}

	/**
	 * Gets the context (currently selected) folder
	 * @return mixed|null
	 */
	public static function getContextFolder()
	{
		if (!isset($_SESSION['context']['folder']))
		{
			return null;
		}

		$serializedObject = $_SESSION['context']['folder'];

		if (isset($serializedObject))
		{
			$folder = unserialize($serializedObject);

			return $folder;
		}
	}
}

?>