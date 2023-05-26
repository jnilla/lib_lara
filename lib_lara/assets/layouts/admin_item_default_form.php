<?php
defined('_JEXEC') or die;

use Jnilla\Jom\Jom as Jom;

?>
<form name="adminForm" action="" method="post" 
	enctype="multipart/form-data" id="<?php echo $singularNameInLowerCase; ?>-form" 
	class="form-validate form-horizontal">
	
	<?php echo Jom::frRenderFieldsetsAsTabs($this->form, true); ?>
	
	<!-- Hidden fields -->
	<input type="hidden" name="option" value="<?php echo "com_$componentNameInLowerCase"; ?>"/>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="layout" value="edit"/>
	<?php echo Jom::getJoomlaFormToken(); ?>
	<?php echo Jom::frRenderHiddenFields($this->form); ?>
	<!-- Hidden fields - End -->
</form>

