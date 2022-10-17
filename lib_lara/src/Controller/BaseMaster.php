<?php
namespace Jnilla\Lara\Controller;

use Jnilla\Lara\Helper\Base as BaseHelper;

defined('_JEXEC') or die;

/**
 * Base master controller class
 */
class BaseMaster extends \Joomla\CMS\MVC\Controller\BaseController{

	public $frameworkConfigurations = array();

	/**
	 * Constructor
	 */
	public function __construct($config = array()){
		// Initialize framework configurations
		$this->frameworkConfigurations = BaseHelper::prepareFrameworkConfigurations($this->frameworkConfigurations);

		parent::__construct($config);
	}

}


