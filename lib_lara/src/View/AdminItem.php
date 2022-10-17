<?php
namespace Jnilla\Lara\View;

defined('_JEXEC') or die;

/**
 * Admin item view class
 */
class AdminItem extends BaseItem{


	/**
	 * Display the view
	 */
	public function display($tpl = null){
		// Extract framework configurations
		extract($this->frameworkConfigurations);

		$helper::addToolbar($this);

		parent::display($tpl);
	}


}




