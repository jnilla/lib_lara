<?php
namespace Jnilla\Lara\View;

defined('_JEXEC') or die;

use Jnilla\Lara\Helper\Admin as LaraAdminHelper;
use Joomla\CMS\MVC\View\HtmlView as JViewLegacy;
use Exception;

/**
 * Base html view class
 */
class BaseHtml extends JViewLegacy{

	public $frameworkConfigurations = array();

	/**
	 * Constructor
	 */
	public function __construct($config = array()){
		// Set if this class is for a list
		$this->frameworkConfigurations['isList'] = false;
		
		// Get class name of current instantiated object
		$this->frameworkConfigurations['currentClassName'] = get_class($this);
		
		// Initialize framework configurations
		$this->frameworkConfigurations = LaraAdminHelper::prepareFrameworkConfigurations($this->frameworkConfigurations);

		parent::__construct($config);
	}

}




