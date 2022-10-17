<?php
namespace Jnilla\Lara\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Uri\Uri as JUri;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\Router\Route as JRoute;

/**
 * Site helper class.
 */
class Site extends Base{

	/**
	 * Add the page title and toolbar.
	 *
	 * @param   JView  $view  View object.
	 *
	 * @return  void
	 */
	public static function addToolbar(&$view){
		// Extract framework configurations
		extract($view->frameworkConfigurations);
		
		if(!isset($view->frameworkConfigurations['toolbar'])){
		    return;
		}

		$buttons = $toolbar["buttons"];
		$canDo = $helper::getActions($view);
		$html = array();
		$baseUri = JUri::base();
		$currentUri = JUri::current();
		$relativeUri = self::getRelativeUriPath();
		$fullUri = JUri::getInstance()->toString();
		$return = base64_encode($fullUri);

		if($buttons){
			foreach($buttons as $button){
				switch($button['type']){
					case 'divider':
						$html[] = "<a class=\"btn btn-link\">|</a>";
						break;
					case 'add':
						if($canDo->get("core.create")){
							$text = JText::_('LIB_LARA_TOOLBAR_NEW');
							$href = "index.php?option=com_$componentNameInLowerCase&view=$singularNameInLowerCase&task=$singularNameInLowerCase.add&return=$return";
							$href = JRoute::_($href);
							$html[] = "<a href=\"$href\" class=\"btn btn-small btn-success\"><i class=\"icon-plus\"></i>&nbsp;$text</a>";
						}
						break;
					case 'edit':
						if($canDo->get("core.edit")){
							$text = \JText::_('LIB_LARA_TOOLBAR_EDIT');
							$id = JFactory::getApplication()->input->get('id', null, 'INT');
							$href = "index.php?option=com_$componentNameInLowerCase&view=$singularNameInLowerCase&task=$singularNameInLowerCase.edit&id=$id&return=$return";
							$href = JRoute::_($href);
							$html[] = "<a href=\"$href\" class=\"btn btn-small\"><i class=\"icon-edit text-info\"></i>&nbsp;$text</a>";
						}
						break;
					case 'delete':
						if($canDo->get("core.edit")){
							\JText::script('LIB_LARA_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST');
							$text = JText::_('LIB_LARA_TOOLBAR_DELETE');
							$html[] = "<span onclick=\"if (document.adminForm.boxchecked.value == 0) { alert(Joomla.JText._('LIB_LARA_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST')); } else { Joomla.submitbutton('$pluralNameInLowerCase.delete'); }\" class=\"btn btn-small\"><i class=\"icon-remove\"></i>&nbsp;$text</span>";
						}
						break;
					case 'checkin':
						if($canDo->get("core.edit")){
							$html[] = '';
						}
						break;
					case 'options':
						if($canDo->get("core.admin")){
							$html[] = '';
						}
						break;
					case 'save':
						if($canDo->get('core.edit') || ($canDo->get('core.create'))){
							$text = JText::_('LIB_LARA_TOOLBAR_APPLY');
							$html[] = "<span onclick=\"Joomla.submitbutton('$singularNameInLowerCase.apply');\" class=\"btn btn-small btn-success\"><i class=\"icon-edit\"></i>&nbsp;$text</span>";
						}
						break;
					case 'saveAndClose':
						if($canDo->get('core.edit') || ($canDo->get('core.create'))){
							$text = JText::_('LIB_LARA_TOOLBAR_SAVE');
							$html[] = "<a onclick=\"Joomla.submitbutton('$singularNameInLowerCase.save');\" class=\"btn btn-small\"><i class=\"icon-ok text-success\"></i>&nbsp;$text</a>";
						}
						break;
					case 'saveAndNew':
						if($canDo->get('core.create')){
							$text = JText::_('LIB_LARA_TOOLBAR_SAVE_AND_NEW');
							$html[] = "<a onclick=\"Joomla.submitbutton('$singularNameInLowerCase.save2new');\" class=\"btn btn-small\"><i class=\"icon-plus text-success\"></i>&nbsp;$text</a>";
						}
						break;
					case 'cancel':
						$text = JText::_('LIB_LARA_TOOLBAR_CANCEL');
						$html[] = "<a onclick=\"Joomla.submitbutton('$singularNameInLowerCase.cancel');\" class=\"btn btn-small\"><i class=\"icon-remove-sign text-error\"></i>&nbsp;$text</a>";
						break;
					case 'custom':
						/*
						 * Exmample configuration:
						 * [
						 *		'type' => 'custom',
						 *		'action' => 'core.create', // Optional
						 *		'iconClass' => 'icon-apply',
						 *		'task' => 'mycontroller.add',
						 *		'text' => 'New Item',
						 *		'listSelected' => false,
						 *	],
						 */
						if(!empty($button['action'])){
							if(!$canDo->get($button['action'])) break;
						}
						$text = JText::_($button['text']);
						$iconClass = $button["iconClass"];
						$task = $button['task'];
						$html[] = "<a onclick=\"Joomla.submitbutton('$task');\" class=\"btn btn-small\"><i class=\"$iconClass\"></i>&nbsp;$text</a>";
						break;
					case 'link':
						/*
						 * Exmample configuration:
						 * [
						 *		'type' => 'link',
						 *		'action' => 'core.create', // Optional
						 *		'iconClass' => 'icon-apply',
						 *		'url' => 'some-url-here',
						 *		'text' => 'New Item',
						 *	],
						 */
						if(!empty($button['action'])){
							if(!$canDo->get($button['action'])) break;
						}
						$text = JText::_($button['text']);
						$iconClass = $button["iconClass"];
						$href = JRoute::_($button['url']);
						$html[] = "<a onclick=\"location.href='$href';\" class=\"btn btn-small\"><i class=\"$iconClass\"></i>&nbsp;$text</a>";
						break;
					case 'html':
						/*
						 * Exmample configuration:
						 * [
						 *		'type' => 'html',
						 *		'action' => 'core.create', // Optional
						 *		'html' => '<a class="btn btn-small">Test</a>',
						 *	],
						 */
						if(!empty($button['action'])){
							if(!$canDo->get($button['action'])) break;
						}
						$html[] = $button['html'];
						break;
				}
			}

			if(count($html))
			{
				$html = implode(' ', $html);
				$view->toolbar = '<div class="form-actions">'.$html.'</div>';
			}
			else
			{
				$view->toolbar = '';
			}
		}
	}
}


