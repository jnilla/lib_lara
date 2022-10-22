<?php
defined('_JEXEC') or die;

use Jnilla\Lara\Helper\Form as FormHelper;
use Jnilla\Jom\Jom as Jom;

?>
<form name="adminForm" 
	action="" method="post" 
	enctype="multipart/form-data" id="<?php echo $singularNameInLowerCase; ?>-form" 
	class="form-validate">
	
	<?php echo $this->toolbar; ?>

	<div class="form-horizontal">
		<?php FormHelper::renderFieldsetsAsTabs($this->form, true); ?>

		<!-- Hidden fields -->
		<input type="hidden" name="option" value="<?php echo "com_$componentNameInLowerCase"; ?>"/>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="layout" value="edit"/>
		<?php echo Jom::formToken(); ?>
		<?php FormHelper::renderHiddenFields($this->form); ?>
		<!-- Hidden fields - End -->
	</div>
</form>

