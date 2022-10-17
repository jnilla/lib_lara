<?php
namespace Jnilla\Lara\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri as JUri;
use Joomla\CMS\Router\Route as JRoute;
use Joomla\CMS\Language\Text as JText;

/**
 * Site master controller class
 */
class SiteMaster extends BaseMaster{
	
	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   mixed    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return   JController This object to support chaining.
	 *
	 */
	public function display($cachable = false, $urlparams = false){
		// Extract framework configurations
		extract($this->frameworkConfigurations);
		
		// If view is not set redirect user to default view and display a warning
		if($this->input->get('view', '', 'CMD') === '')
		{
			$defaultViewNameInLowerCase = $this->frameworkConfigurations['defaultViewNameInLowerCase'];
			$url = JRoute::_("index.php?option=com_$componentNameInLowerCase&view=$defaultViewNameInLowerCase");
			$this->setRedirect($url);
			$this->redirect();
		}
		
		parent::display($cachable, $urlparams);
		
		return $this;
	}
	
}




