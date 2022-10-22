<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper as JHtml;
use Jnilla\Jom\Jom as Jom;

JHtml::_('behavior.tabstate');

/**
 * Description: 
 * 
 * Renders a boostrap tab component. The tab save the 
 * current tab state during page reloads.
 * 
 * Required data:
 * 
 * $displayData = [
 *     'id'(optional) => string // Tab component id 
 *     'class'(optional) => string // Tab component class 
 *     'items' => [ // Tab items
 *         [
 *             'title' => string, // Tab title
 *             'content' => string, // Tab content 
 *         ], 
 *         ... 
 *     ]
 * ];
 */

if(isset($GLOBALS['bootstrap-tabs-counter'])){
	$GLOBALS['bootstrap-tabs-counter'] = intval($GLOBALS['bootstrap-tabs-counter'])+1;
}else{
	$GLOBALS['bootstrap-tabs-counter'] = 0;
}
$id = isset($displayData["id"]) ? $displayData["id"] : "bootstrap-tabs-".$GLOBALS['bootstrap-tabs-counter'];
$items = $displayData["items"];
$class = isset($displayData["class"]) ? $displayData["class"] : '';
$firstTabId = "$id-tab-0";
?>
<div id="$id" class="bootstrap-tabs <?php echo $class; ?>">
	<?php echo JHtml::_('bootstrap.startTabSet', $id, ['active' => $firstTabId]); ?>
		<?php foreach ($items as $itemKey => $item) : ?>
			<?php echo JHtml::_('bootstrap.addTab', $id, "$id-tab-$itemKey", Jom::translate($item['title'])); ?>
				<?php echo $item['content']; ?>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php endforeach; ?>
	<?php echo JHtml::_('bootstrap.endTabSet'); ?>
</div>

