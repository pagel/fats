<?php
/**
 * Smarty.php
 *
 * This class sets default options for the Smarty template engine.
 *
 * PHP version 5
 *
 * @category   Web Application
 * @package    Faculty Advancement Tracking System
 * @author     Geoffrey Pagel <geoffrey.pagel@northwestern.edu>
 * @version    SVN: $Id$
 */
namespace FATS\Application;

require_once 'Smarty.class.php';

class Smarty extends \Smarty
{
	/**
	 * Initializes a new instance of the Smarty class and sets appropriate application options
	 */
	function __construct()
	{
		parent::__construct();

		$this->setTemplateDir('/var/www/sites/fats/src/Smarty/templates');
		$this->setCompileDir('/var/www/sites/fats/src/Smarty/templates_c');
		$this->setCacheDir('/var/www/sites/fats/src/Smarty/cache');
		$this->setConfigDir('/var/www/sites/fats/src/Smarty/configs');

		$this->caching = false;
		$this->debugging = false;
		$this->muteExpectedErrors();
		$this->assign('app_name', 'McCormick Faculty Advancement Tracking System');
	}
}

?> 