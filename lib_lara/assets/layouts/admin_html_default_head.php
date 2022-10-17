<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\HTML\HTMLHelper as JHtml;
use Joomla\CMS\Layout\LayoutHelper as JLayoutHelper;
use Joomla\CMS\Language\Text as JText;

// Extract framework configurations
extract($this->frameworkConfigurations);

echo "<pre>"; echo var_dump($this->frameworkConfigurations); echo "</pre>"; die; // DEBUG

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
?>




