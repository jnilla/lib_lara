<?php
defined('_JEXEC') or die;

use Jnilla\Jom\Jom as Jom;
use Joomla\CMS\Language\Text as JText;

// Extract framework configurations
extract($this->frameworkConfigurations);

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.tabstate');
Jom::addCss('media/lib_lara/css/site_item_default.css');

// Define page heading
$pageHeading = JText::_("LIB_LARA_EDITING_ITEM")." id ".$this->item->id;

?>
<script type="text/javascript">
	js = jQuery.noConflict();
	js(document).ready(function () {
		//
	});

	Joomla.submitbutton = function (task) {
		if(task == '<?php echo $singularNameInLowerCase; ?>.cancel'){
			Joomla.submitform(task, document.getElementById('<?php echo $singularNameInLowerCase; ?>-form'));
		}else{
			if (task != '<?php echo $singularNameInLowerCase; ?>.cancel' && document.formvalidator.isValid(document.id('<?php echo $singularNameInLowerCase; ?>-form'))) {
				Joomla.submitform(task, document.getElementById('<?php echo $singularNameInLowerCase; ?>-form'));
			}else{
				alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			}
		}
	}
</script>

<h1 class="page-title"><?php echo $pageHeading; ?></h1>




