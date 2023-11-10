<?php
/*******
 * @package xbArticleManager
 * file administrator/components/com_xbarticleman/views/arttags/tmpl/default_batch_body.php
 * @version 2.0.5.0 10th November 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2019
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;

$published = $this->state->get('filter.published');
?>

<div class="container-fluid">
	<div class="row-fluid">
		<?php if (($published >= 0) || ($published == '')) : ?>
			<div class="control-group span6">
				<div class="controls">
					<?php echo HtmlHelper::_('batch.item', 'com_content'); ?>
				</div>
			</div>
        <?php else: ?>
          <div class="span6">to change category exclude trashed articles from list</div>
		<?php endif; ?>
		<div class="control-group span6">
			<div class="controls">
				<?php echo HTMLHelper::_('batch.tag'); ?>
			</div>
			<div class="controls">
				<?php echo LayoutHelper::render('untag', array()); ?>
			</div>
		</div>
	</div>
</div>
