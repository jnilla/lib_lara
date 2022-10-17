<?php
namespace Jnilla\Lara\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\Object\CMSObject as JObject;
use Joomla\CMS\Toolbar\ToolbarHelper as JToolbarHelper;
use Joomla\CMS\Toolbar\Toolbar as Toolbar;

/**
 * Admin helper class
 */
class Admin extends Base{

	/**
	 * Adds the sidebar.
	 *
	 * @param   JView  $view  Views object.
	 *
	 * @return  void
	 */
	public static function addSidebar(&$view){
		// Extract framework configurations
		extract($view->frameworkConfigurations);

		foreach($sidebarItemNamesInLowerCase as $item)
		{
			$singularNameInUpperCase = strtoupper($item);
			\JHtmlSidebar::addEntry(
				JText::_("COM_{$componentNameInUpperCase}_SIDEBAR_ITEM_$singularNameInUpperCase"),
				"index.php?option=com_$componentNameInLowerCase&view=$item",
				$item == $view->getName()
				);
		}

		$view->sidebar = \JHtmlSidebar::render();
	}

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
		
		$buttons = isset($toolbar["buttons"]) ? $toolbar["buttons"] : [];
		$iconClass = preg_replace('/^icon-/i', '', $toolbar["iconClass"]);
		$canDo = $helper::getActions($view);

		JToolbarHelper::title(JText::_("COM_{$componentNameInUpperCase}_PAGETITLE_{$currentNameInUpperCase}"), $iconClass);

		if($buttons){
			foreach($buttons as $button){
				switch($button['type']){
					case 'divider':
						JToolbarHelper::divider();
						break;
					case 'add':
						if($canDo->get("core.create")){
							JToolbarHelper::addNew("$singularNameInLowerCase.add", "JTOOLBAR_NEW");
						}
						break;
					case 'edit':
						if($canDo->get("core.edit")){
							JToolbarHelper::editList("$singularNameInLowerCase.edit", "JTOOLBAR_EDIT");
						}
						break;
					case 'delete':
						if($canDo->get("core.edit")){
							JToolbarHelper::deleteList("", "$pluralNameInLowerCase.delete", "JTOOLBAR_DELETE");
						}
						break;
					case 'checkin':
						if($canDo->get("core.edit")){
							JToolbarHelper::custom("$pluralNameInLowerCase.checkin", "checkin.png", "checkin_f2.png", "JTOOLBAR_CHECKIN", true);
						}
						break;
					case 'options':
						if($canDo->get("core.admin")){
							JToolbarHelper::preferences("com_$componentNameInLowerCase");
						}
						break;
					case 'save':
						if($canDo->get('core.edit') || ($canDo->get('core.create'))){
							JToolbarHelper::apply("$singularNameInLowerCase.apply", 'JTOOLBAR_APPLY');
						}
						break;
					case 'saveAndClose':
						if($canDo->get('core.edit') || ($canDo->get('core.create'))){
							JToolbarHelper::save("$singularNameInLowerCase.save", 'JTOOLBAR_SAVE');
						}
						break;
					case 'saveAndNew':
						if($canDo->get('core.create')){
							JToolbarHelper::custom("$singularNameInLowerCase.save2new", 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
						}
						break;
					case 'saveToCopy':
						if($canDo->get('core.create')){
							JToolbarHelper::save2copy("$singularNameInLowerCase.save2copy");
						}
						break;
					case 'cancel':
						JToolbarHelper::cancel("$singularNameInLowerCase.cancel", 'JTOOLBAR_CANCEL');
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
						$iconClass = preg_replace('/^icon-/i', '', $button["iconClass"]);
						JToolbarHelper::custom(
							$button['task'],
							$iconClass,
							'',
							JText::_($button['text']),
							$button['listSelected']
						);
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
						$iconClass = preg_replace('/^icon-/i', '', $button["iconClass"]);
						JToolbarHelper::link(
							$button['url'],
							JText::_($button['text']),
							$iconClass
						);
						break;
					case 'html':
						/*
						 * Exmample configuration:
						 * [
						 *		'type' => 'link',
						 *		'action' => 'core.create', // Optional
						 *		'html' => '<a class="btn btn-small">Test</a>',
						 *	],
						 */
						if(!empty($button['action'])){
							if(!$canDo->get($button['action'])) break;
						}
						$bar = Toolbar::getInstance('toolbar');
						$bar->appendButton('Custom', $button['html']);
						break;
				}
			}
		}
	}

}



