<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\HTML\HTMLHelper as JHtml;
use Joomla\CMS\Router\Route as JRoute;
use Joomla\CMS\Layout\LayoutHelper as JLayoutHelper;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\Uri\Uri as JUri;

// Extract framework configurations
extract($this->frameworkConfigurations);

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.formvalidator');

$app = JFactory::getApplication();
$user = JFactory::getUser();
$userId = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirection  = $this->escape($this->state->get('list.direction'));
$saveOrder = $listOrder == 'a.ordering';
$fullUri = JUri::getInstance()->toString();
$return = base64_encode($fullUri);

// Menu item
$menuItem = $app->getMenu()->getActive();
// if(empty($menuItem)) $menuItem = $app->getMenu()->getDefault();

// Define page heading
if(!empty($menuItem)){
	$pageHeading = $menuItem->params->get('page_heading');
	if(empty($pageHeading)) $pageHeading = $menuItem->params->get('page_title');
	if(empty($pageHeading)) $pageHeading = $menuItem->title;
}else{
	$pageHeading = JText::_("COM_{$componentNameInUpperCase}_PAGETITLE_{$currentNameInUpperCase}");
}

if ($saveOrder){
	$saveOrderingUrl = "index.php?option=com_$componentNameInLowerCase&task={$pluralNameInLowerCase}.saveOrderAjax&tmpl=component";
	JHtml::_('sortablelist.sortable', "{$pluralNameInLowerCase}", "adminForm", strtolower($listDirn), $saveOrderingUrl);
}

// Prepend the checkbox column to the $columns array
array_unshift(
	$columns,
	array(
		'field' => 'checkbox',
		'th.attribs' => 'width="1%" class="hidden-phone""',
		'td.attribs' => 'class="hidden-phone"'
	)
);

// Append the id column to the $columns array
array_push(
	$columns,
	array(
		'field' => 'id',
		'th.attribs' => 'width="1%" class="left"'
	)
);
?>



