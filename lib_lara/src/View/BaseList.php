<?php
namespace Jnilla\Lara\View;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as JViewLegacy;
use Exception;
use Jnilla\Lara\Helper\Base as BaseHelper;

/**
 * Base list view class
 */
class BaseList extends JViewLegacy{

	public $frameworkConfigurations = array();

	/**
	 * Constructor
	 */
	public function __construct($config = array()){
		// Set if this class is for a list
		$this->frameworkConfigurations['isList'] = true;
		
		// Get class name of current instantiated object
		$this->frameworkConfigurations['currentClassName'] = get_class($this);

		// Initialize framework configurations
		$this->frameworkConfigurations = BaseHelper::prepareFrameworkConfigurations($this->frameworkConfigurations);

		// Extract framework configurations
		extract($this->frameworkConfigurations);

		// Filter form file is required. Check if exist
		$formPath = JPATH_COMPONENT."/models/forms/filter_{$pluralNameInLowerCase}.xml";
		if(!file_exists($formPath)) throw new Exception("Form file is required: $formPath");

		parent::__construct($config);
	}
}




