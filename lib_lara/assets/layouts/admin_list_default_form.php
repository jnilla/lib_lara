<?php
defined('_JEXEC') or die;
?>
<form action="<?php echo JRoute::_("index.php?option=com_$componentNameInLowerCase&view=$pluralNameInLowerCase"); ?>" method="post" name="adminForm" id="adminForm">
	<!-- Sidebar -->
	<div id="j-sidebar-container" class="span2"><?php echo $this->sidebar; ?></div>

	<!-- Main container -->
	<div id="j-main-container" class="span10">

		<!-- Search tools -->
		<?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));?>

		<!-- Table -->
		<?php if (empty($this->items)) : ?>
			<div class="alert"><?php echo JText::_('LIB_LARA_NO_RESULTS'); ?></div>
		<?php else : ?>
			<table class="table table-striped" id="<?php echo $pluralNameInLowerCase; ?>">
				<!-- Table head -->
				<thead>
					<tr>
						<?php foreach($columns as $column) : ?>
							<?php
							$field = $column['field'];
							if(!isset($column['header'])) $column['header'] = $field;
							$header = $column['header'];
							$headerInUpperCase = strtoupper($header);
							$attribs = isset($column['th.attribs']) ? $column['th.attribs'] : '';
							?>
							<!-- Column: <?php echo $header; ?> -->
							<th <?php echo $attribs; ?>>
								<?php if($field === 'checkbox') : ?>
									<?php
									// Checkbox
									echo JHtml::_('grid.checkall');
									?>
								<?php elseif($field === 'id') : ?>
									<?php
									// ID field
									echo JHtml::_('searchtools.sort', "LIB_LARA_COLUMN_ID", "a.id", $listDirection, $listOrder);
									?>
								<?php elseif(strpos($field, 'fw_') === 0) : ?>
									<?php
									// Framework fields
									$headerInUpperCase = strtoupper($field);
									echo JHtml::_('searchtools.sort', "LIB_LARA_COLUMN_$headerInUpperCase", "a.$field", $listDirection, $listOrder);
									?>
								<?php else : ?>
									<?php
									// Custom
									echo JHtml::_('searchtools.sort', "COM_{$componentNameInUpperCase}_COLUMN_$headerInUpperCase", "a.$field", $listDirection, $listOrder);
									?>
								<?php endif;?>
							</th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<!-- Table body -->
				<tbody>
					<?php foreach ($this->items as $i => $item) :
// 						$canEdit    = $user->authorise('core.edit', "com_$componentNameInLowerCase");
// 						$canCheckin = $user->authorise('core.manage', "com_$componentNameInLowerCase");
// 						$canEditOwn = $user->authorise('core.edit.own', "com_$componentNameInLowerCase") && $item->created_by == $userId;
// 						$canChange  = $user->authorise('core.edit.state', "com_$componentNameInLowerCase") && $canCheckin;
						$canEdit    = true;
						$canCheckin = true;
						$canEditOwn = true;
						$canChange  = true;
						?>
						<tr>
							<?php foreach($columns as $column) : ?>
								<?php
								$field = $column['field'];
								if(!isset($column['header'])) $column['header'] = $field;
								$header = $column['header'];
								$headerInUpperCase = strtoupper($header);
								$attribs = isset($column['td.attribs']) ? $column['td.attribs'] : '';
								$translateRows = isset($column['translateRows']) ? $column['translateRows'] : '';
								$translateRowsPrefix = isset($column['translateRowsPrefix']) ? $column['translateRowsPrefix'] : '';
								$addCheckout = isset($column['addCheckout']) ? $column['addCheckout'] : '';
								$addLink = isset($column['addLink']) ? $column['addLink'] : '';
								$translate = isset($column['td.translate']) ? $column['td.translate'] : '';
								$translatePrefix = isset($column['td.translatePrefix']) ? $column['td.translatePrefix'] : '';
								?>
								<!-- Column: <?php echo $header; ?> -->
								<td <?php echo $attribs; ?>>
									<?php if($field === 'checkbox') : ?>
										<?php // Checkbox ?>
										<?php echo JHtml::_('grid.id', $i, $item->id); ?>
									<?php elseif($field === 'fw_enable') : ?>
										<?php // Enabled ?>
										<?php
										if($item->{$field} === "1"){
											$fieldText = JText::_("JYES");
											echo "<span class=\"badge badge-success\">$fieldText</span>";
										}else{
											$fieldText = JText::_("JNO");
											echo "<span class=\"badge badge-important\">$fieldText</span>";
										}
										?>
									<?php else : ?>
										<?php // Custom ?>
										<?php
										$fieldText = $this->escape($item->{$field});
										// Translate if required
										if($translate){
											$fieldText = strtoupper("COM_{$componentNameInUpperCase}_{$translatePrefix}{$fieldText}");
											$fieldText = JText::_($fieldText);
										}
										?>
										<?php if ($addLink && ($canEdit || $canEditOwn)) : ?>
											<?php // Add edit link ?>
											<a href="<?php echo JRoute::_("index.php?option=com_$componentNameInLowerCase&task=$singularNameInLowerCase.edit&id=".(int) $item->id); ?>"
												title="<?php echo JText::_('JACTION_EDIT'); ?>">
												<?php echo (trim($fieldText) === '') ? '<i class="icon-link"></i>' : $fieldText; ?>
											</a>
										<?php else : ?>
											<?php // Plain text ?>
											<?php echo $fieldText; ?>
										<?php endif; ?>
									<?php endif; ?>
								</td>
							<?php endforeach; ?>
						</tr>
					<?php endforeach; ?>
				</tbody>
				<!-- Table footer -->
				<tfoot>
					<tr>
						<td colspan="<?php echo count($columns); ?>">
							<div style="padding: 15px 0">
								<?php echo JText::sprintf('LIB_LARA_PAGINATION_TOTAL_ITEMS', number_format($this->pagination->total, 0, '.', ',')); ?>
							</div>
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
			</table>
		<?php endif; ?>

		<!-- Hidden fields -->
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="boxchecked" value="0"/>
		<?php echo JHtml::_('form.token'); ?>
	</div>

</form>





