<?php
namespace Jnilla\Lara\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper as JComponentHelper;
use Jnilla\Lara\Helper\Base as BaseHelper;
use Joomla\CMS\MVC\Model\ListModel as JModelList;
use Joomla\CMS\MVC\View\HtmlView as JViewLegacy;
use Joomla\CMS\Factory as JFactory;

/**
 * Base list model class
 */
class BaseList extends JModelList{

	public $frameworkConfigurations = array();

	/**
	 * Constructor
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see        JController
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

		// Get related view name
		$name = preg_replace('/^'.$componentNameInPascalCase.'Model/i', '', $currentClassName);
		$viewNameInPascalCase = $componentNameInPascalCase.'View'.$name;

		// Get default ordering from related view framework configurations
		$this->frameworkConfigurations['defaultOrdering'] = $this->frameworkConfigurations[$viewNameInPascalCase]['defaultOrdering'];

		// Get text search fields from related view framework configurations
		$this->frameworkConfigurations['textSearchFields'] = $this->frameworkConfigurations[$viewNameInPascalCase]['textSearchFields'];

		// Create list of ordering fields using the column field names
		if(!isset($orderingFields)){
			foreach ($this->frameworkConfigurations[$viewNameInPascalCase]['columns'] as $column) {
				$this->frameworkConfigurations['orderingFields'][] = $column['field'];
			}
		}

		// Generate context string if not set
		if (!isset($this->context))
		{
			$this->context = $helper::generateContextString(array(
				'option' => "com_$componentNameInLowerCase",
				'model' => $currentNameInLowerCase,
			));
		}

		// Filter form file is required. Check if exist
		$formPath = JPATH_COMPONENT."/models/forms/filter_{$pluralNameInLowerCase}.xml";
		if(!file_exists($formPath)){
			throw new \Exception("Form file is required: $formPath");
		}

		// Create list of filter fields. This will be used to create database queries
		$form = $this->getFilterForm(null, false);
		$fields = $helper::listFormFieldsNames($form, "filter", array("search"));
		$this->frameworkConfigurations['filterFields'] = $helper::arrayMergeAndRemoveDuplicates(
			isset($this->frameworkConfigurations['filterFields']) ? $this->frameworkConfigurations['filterFields'] : '',
			$fields
		);

		// Create list of state fields. This will be used to store the fields values
		// in the user state vars
		$this->frameworkConfigurations['stateFields'] = $helper::arrayMergeAndRemoveDuplicates(
			isset($this->frameworkConfigurations['stateFields']) ? $this->frameworkConfigurations['stateFields'] : '',
			isset($this->frameworkConfigurations['filterFields']) ? $this->frameworkConfigurations['filterFields'] : ''
		);

		// Create list of whitelist input fields. Input fields that are not listed here will be excluded
		// for security reasons
		$this->frameworkConfigurations['whitelistFields'] = $helper::arrayMergeAndRemoveDuplicates(
			isset($this->frameworkConfigurations['whitelistFields']) ? $this->frameworkConfigurations['whitelistFields'] : '',
			isset($this->frameworkConfigurations['filterFields']) ? $this->frameworkConfigurations['filterFields'] : ''
		);
		$this->frameworkConfigurations['whitelistFields'] = $helper::arrayMergeAndRemoveDuplicates(
			$this->frameworkConfigurations['whitelistFields'],
			$this->frameworkConfigurations['stateFields']
		);
		$this->frameworkConfigurations['whitelistFields'] = $helper::arrayMergeAndRemoveDuplicates(
			$this->frameworkConfigurations['whitelistFields'],
			$this->frameworkConfigurations['orderingFields']
		);

		// Pass whitelist input fields to parent constructor
		$fields = $this->frameworkConfigurations['whitelistFields'];
		foreach($fields as $field){
			$config["filter_fields"][] = $field;
			$config["filter_fields"][] = "a.$field";
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   Elements order
	 * @param   string  $direction  Order direction
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function populateState($ordering = null, $direction = null){
		// Extract framework configurations
		extract($this->frameworkConfigurations);

		// Set fields states
		foreach($stateFields as $stateField){
			$state = $this->getUserStateFromRequest($this->context.".filter.$stateField", "filter_$stateField");
			$this->setState("filter.$stateField", $state);
		}

		// Set component params state
		$state = JComponentHelper::getParams("com_$componentNameInLowerCase");
		$this->setState('params', $state);

		// Set ordering state
		parent::populateState($defaultOrdering['field'], $defaultOrdering['direction']);
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return   string A store id.
	 *
	 * @since    1.6
	 */
	protected function getStoreId($id = ''){
		// Extract framework configurations
		extract($this->frameworkConfigurations);

		// Compile id
		foreach($whitelistFields as $whitelistField){
			$id .= ":".$this->getState("filter.$whitelistField");
		}

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return   JDatabaseQuery
	 *
	 * @since    1.6
	 */
	protected function getListQuery(){
		// Extract framework configurations
		extract($this->frameworkConfigurations);

		// Get dbo
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select
		$query->select($this->getState('list.select', 'a.*'));

		// From
		$query->from("#__{$componentNameInLowerCase}_{$pluralNameInLowerCase} AS a");

// 		// Join users table for row checkout support (id)
// 		$query->select("uc.name AS uEditor");
// 		$query->join("LEFT", "#__users AS uc ON uc.id=a.checked_out");

// 		// Join users table for row checkout support (created_by)
// 		$query->select('created_by.name AS created_by');
// 		$query->join('LEFT', '#__users AS created_by ON created_by.id = a.created_by');

// 		// Join users table for row checkout support (modified_by)
// 		$query->select('modified_by.name AS modified_by');
// 		$query->join('LEFT', '#__users AS modified_by ON modified_by.id = a.modified_by');

		// Filter fields
		foreach($filterFields as $filterField){
			$state = $this->getState("filter.$filterField");
			if($state == '') continue;
			
			$state = $db->Quote($db->escape($state, true));
			$query->where("a.$filterField = $state");
		}

		// Text search. Basically applies the search term to all fields in $textSearchFields
		$state = $this->getState('filter.search');
		if(!empty($state)){
			if(stripos($state, 'id:') === 0){
				// Text search by id support
				$query->where('a.id = '.(int)substr($state, 3));
			}else{
				// TODO i think isset needs to be changed by count instead, do some testing first
				if(isset($textSearchFields)){
					$state = $db->Quote('%'.$db->escape($state, true).'%');
					$where = '';
					foreach($textSearchFields as $textSearchField){
						if($where == ""){
							$where = "a.$textSearchField LIKE $state";
						}else{
							$where .= " OR a.$textSearchField LIKE $state";
						}
					}
					$query->where("($where)");
				}
			}
		}

		// Ordering
		$orderingField = $this->getState('list.ordering');
		$orderDirection = $this->getState('list.direction');
		if($orderingField && $orderDirection){
			$query->order($db->escape($orderingField.' '.$orderDirection));
		}

		return $query;
	}
}





