<?php
namespace Jnilla\Lara\View;

defined('_JEXEC') or die;

/**
 * Site item view class
 */
class SiteItem extends BaseItem{
	
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




