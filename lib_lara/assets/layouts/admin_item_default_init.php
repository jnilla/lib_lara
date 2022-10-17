<?php
defined('_JEXEC') or die;

// Extract framework configurations
extract($this->frameworkConfigurations);

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.tabstate');
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

