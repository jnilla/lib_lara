<?php
namespace Jnilla\Lara\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\AdminModel as JModelAdmin;
use Joomla\CMS\Table\Table as JTable;
use Joomla\CMS\Factory as JFactory;
use Exception;
use Jnilla\Lara\Helper\Base as BaseHelper;

/**
 * Base item model class
 */
class BaseItem extends JModelAdmin{

	public $frameworkConfigurations = array();

	protected $item = null;

	// TODO, check if this code can be removed
// 	public function populateState(){
// 		parent::populateState();
// 	}

	/**
	 * Constructor
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 */
	public function __construct($config = array()){
		// Set if this class is for a list
		$this->frameworkConfigurations['isList'] = false;
		
		// Get class name of current instantiated object
		$this->frameworkConfigurations['currentClassName'] = get_class($this);
		
		// Initialize framework configurations
		$this->frameworkConfigurations = BaseHelper::prepareFrameworkConfigurations($this->frameworkConfigurations);

		parent::__construct($config);
	}

	/**
	 * Returns a reference to the a Table object, always creating it
	 */
	public function getTable($type = '', $prefix = '', $config = array()){
		// Extract framework configurations
		extract($this->frameworkConfigurations);

		if(empty($type)){
			$type = $pluralNameInLowerCase;
		}

		if(empty($prefix)){
			$prefix = "{$componentNameInCamelCase}Table";
		}

		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the form.
	 */
	public function getForm($data = array(), $loadData = true){
		// Extract framework configurations
		extract($this->frameworkConfigurations);

		// Check if form file exist
		$formPath = JPATH_COMPONENT . "/models/forms/$singularNameInLowerCase.xml";
		if(!file_exists($formPath)){
			throw new Exception("Form file is required: $formPath");
		}

		// Load the form
		$form = $this->loadForm("com_$componentNameInLowerCase.$singularNameInLowerCase", $singularNameInLowerCase, array(
			'control' => 'jform',
			'load_data' => $loadData
		));

		if(empty($form)){
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 */
	protected function loadFormData(){
		// Extract framework configurations
		extract($this->frameworkConfigurations);

		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState("com_$componentNameInLowerCase.edit.$singularNameInLowerCase.data", array());

		if(empty($data)){
			if($this->item === null) $this->item = $this->getItem();
			$data = $this->item;
		}

		return $data;
	}

	// /**
	//  * Prepare and sanitise the table prior to saving.
	//  */
	// protected function prepareTable($table){
	// 	// Extract framework configurations
	// 	extract($this->frameworkConfigurations);

	// 	jimport('joomla.filter.output');

	// 	// TODO: Add code to detect if ordering field exist or not, because this code throws an error if column does not exist
	// 	if(empty($table->id)){
	// 		// Set ordering to the last item if not set
	// 		if(!isset($table->ordering)){
	// 			$db = JFactory::getDbo();
	// 			$db->setQuery("SELECT MAX(ordering) FROM #__{$componentNameInLowerCase}_{$singularNameInLowerCase}");
	// 			$max = $db->loadResult();
	// 			$table->ordering = $max + 1;
	// 		}
	// 	}
	// }
}



