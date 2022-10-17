<?php
namespace Jnilla\Lara\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController as JControllerAdmin;
use Jnilla\Lara\Helper\Base as BaseHelper;

/**
 * Base list controller class
 */
class BaseList extends JControllerAdmin{

	public $frameworkConfigurations = array();

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     \JControllerLegacy
	 * @since   1.6
	 * @throws  \Exception
	 */
	public function __construct($config = array()){
		// Set if this class is for a list
		$this->frameworkConfigurations['isList'] = true;
		
		// Get class name of current instantiated object
		$this->frameworkConfigurations['currentClassName'] = get_class($this);
		
		// Initialize framework configurations
		$this->frameworkConfigurations = BaseHelper::prepareFrameworkConfigurations($this->frameworkConfigurations);

		$this->text_prefix = "LIB_LARA";

		parent::__construct($config);
	}

	/**
	 * Proxy for getModel.
	 */
	public function getModel($name = '', $prefix = '', $config = array()){
		// Extract framework configurations
		extract($this->frameworkConfigurations);

		if(empty($name)){
			$name = $singularNameInLowerCase;
		}
		if(empty($prefix)){
			$prefix = "{$componentNameInLowerCase}Model";
		}
		if(empty($config)){
			$config = array('ignore_request' => true);
		}

		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}
}


