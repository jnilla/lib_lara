<?php
namespace Jnilla\Lara\View;

use \Jnilla\Lara\View\BaseList as BaseListView;

defined('_JEXEC') or die;

/**
 * Admin List view class
 */
class AdminList extends BaseListView{

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
		if(count($errors = $model->getErrors())){
			throw new \Exception(implode("\n", $errors));
		}

		$helper::addToolbar($this);

		$helper::addSidebar($this);

		parent::display($tpl);
	}
}



