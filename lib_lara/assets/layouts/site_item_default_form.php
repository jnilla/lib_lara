<?php
defined('_JEXEC') or die;
?>
<form name="adminForm" action="" method="post" enctype="multipart/form-data" id="<?php echo $singularNameInLowerCase; ?>-form" class="form-validate">
	<!-- Toolbar -->
	<?php echo $this->toolbar; ?>

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'basic')); ?>
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'basic', JText::_("COM_{$componentNameInUpperCase}_TAB_BASIC", true)); ?>
				<?php
				foreach ($formFields as $formField){
					$type = $this->form->getField($formField)->getAttribute("type");
					if($type == "hidden") continue;
					echo $this->form->renderField($formField);
				}
				?>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php echo JHtml::_('bootstrap.endTabSet'); ?>

		<!-- Hidden fields -->
		<input type="hidden" name="option" value="<?php echo "com_$componentNameInLowerCase"; ?>"/>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="layout" value="edit"/>
		<?php echo JHtml::_('form.token'); ?>
		<?php
		foreach ($formFields as $formField){
			$type = $this->form->getField($formField)->getAttribute("type");
			if($type === "hidden") echo $this->form->renderField($formField);
		}
		?>
	</div>
</form>

