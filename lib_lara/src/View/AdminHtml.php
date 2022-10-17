<?php
namespace Jnilla\Lara\View;

defined('_JEXEC') or die;

use Jnilla\Lara\Helper\Admin as LaraAdminHelper;

/**
 * Admin html view class
 */
class AdminHtml extends BaseHtml{

	/**
	 * Display the view
	 */
	public function display($tpl = null){
		LaraAdminHelper::addToolbar($this);
		
		LaraAdminHelper::addSidebar($this);

		parent::display($tpl);
	}


}




