<?php
namespace Jnilla\Lara\View;

defined('_JEXEC') or die;

/**
 * Site list view class
 */
class SiteList extends BaseList{
	
	protected $items;
	
	protected $pagination;
	
	protected $state;
	
	protected $params;
	
	/**
	 * Display the view
	 */
	public function display($tpl = null){
		// Extract framework configurations
		extract($this->frameworkConfigurations);
		
		$model = $this->getModel();
		
		$this->items = $model->getItems();
		$this->pagination = $model->getPagination();
		$this->state = $model->getState();
		$this->filterForm = $model->getFilterForm();
		$this->activeFilters = $model->getActiveFilters();
		
		// Check for errors
		if(count($errors = $model->getErrors()))
		{
			throw new \Exception(implode("\n", $errors));
		}
		
		$helper::addToolbar($this);
		
		parent::display($tpl);
	}
}



