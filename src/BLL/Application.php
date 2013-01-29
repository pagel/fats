<?php
/**
 * Application.php
 *
 * This class contains methods to get and set application options.
 *
 * PHP version 5
 *
 * @category   Web Application
 * @package    Faculty Advancement Tracking System
 * @author     Geoffrey Pagel <geoffrey.pagel@northwestern.edu>
 * @version    SVN: $Id$
 */
namespace FATS\BLL;

require_once 'Library/Observable.php';

class Application extends \FATS\Library\Observable
{
	/**
	 * Gets application options
	 *
	 * @return array
	 */
	public static function getApplicationOptions()
	{
		$xmlFile = '/var/www/sites/fats/src/application.xml';

		if (!file_exists($xmlFile))
		{
			trigger_error("Application options file ({$xmlFile}) not found or is unreadable.", E_USER_ERROR);

			return null;
		}

		$dom = new \DOMDocument();
		$dom->load($xmlFile);

		$root = $dom->documentElement;

		$nodeList = $root->getElementsByTagName('options')->item(0)->childNodes;

		$options = array();

		foreach ($nodeList as $node)
		{
			if ($node->nodeType !== XML_ELEMENT_NODE)
			{
				continue;
			}

			$key = $node->nodeName;
			$value = $node->nodeValue;

			$options[$key] = $value;
		}

		return $options;
	}

	/**
	 * Sets application options
	 *
	 * @param $options
	 * @return bool
	 */
	public static function setApplicationOptions($options)
	{
		$xmlFile = '/var/www/sites/fats/src/application.xml';

		try
		{
			if (!file_exists($xmlFile))
			{
				trigger_error("Application options file ({$xmlFile}) not found or is unreadable.", E_USER_ERROR);

				return false;
			}

			$dom = new \DOMDocument();
			$dom->load($xmlFile);

			foreach ($options as $key => $value)
			{
				$dom->getElementsByTagName($key)->item(0)->nodeValue = $value;
			}

			return (bool)$dom->save($xmlFile);
		}
		catch (\Exception $x)
		{
			return false;
		}
	}
}

?>