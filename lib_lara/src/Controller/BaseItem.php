<?php
namespace Jnilla\Lara\Controller;

use Jnilla\Lara\Helper\Base as BaseHelper;
use Joomla\CMS\MVC\Controller\FormController as JControllerForm;

defined('_JEXEC') or die;

/**
 * Base item controller class
 */
class BaseItem extends JControllerForm{

	public $frameworkConfigurations = array();

	/**
	 * Constructor
	 */
	public function __construct(){
		// Set if this class is for a list
		$this->frameworkConfigurations['isList'] = false;
		
		// Get class name of current instantiated object
		$this->frameworkConfigurations['currentClassName'] = get_class($this);
		
		// Initialize framework configurations
		$this->frameworkConfigurations = BaseHelper::prepareFrameworkConfigurations($this->frameworkConfigurations);

		// Set the list view name
		$this->view_list = $this->frameworkConfigurations["pluralNameInCamelCase"];

		parent::__construct();
	}
}



