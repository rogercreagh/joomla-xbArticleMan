<?php
/*******
 * @package xbArticleMan Component
 * file administrator/components/com_xbarticleman/views/artlinks/tmpl/default_batch_body.php
 * @version 2.0.5.0 12th November 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2019
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
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
          <div class="span6"><?php Text::_('XBARTMAN_CHANGE_CAT_FILTER'); ?></div>
		<?php endif; ?>
	</div>
</div>
