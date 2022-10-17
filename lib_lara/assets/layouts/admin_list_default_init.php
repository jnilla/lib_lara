<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\HTML\HTMLHelper as JHtml;
use Joomla\CMS\Router\Route as JRoute;
use Joomla\CMS\Layout\LayoutHelper as JLayoutHelper;
use Joomla\CMS\Language\Text as JText;

// Extract framework configurations
extract($this->frameworkConfigurations);

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user = JFactory::getUser();
$userId = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirection  = $this->escape($this->state->get('list.direction'));
$saveOrder = $listOrder == 'a.ordering';

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




