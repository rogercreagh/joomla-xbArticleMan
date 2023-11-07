<?php
/*******
 * @package xbarticleman
 * file administrator/components/com_xbarticleman/views/artlinks/tmpl/default.php
 * @version 2.0.3.3 7th November 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2019
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\Registry\Registry;
//use Joomla\Utilities\ArrayHelper;

JLoader::register('XbarticlemanHelper', JPATH_ADMINISTRATOR . '/components/com_xbarticleman/helpers/xbarticleman.php');
JLoader::register('ContentHelperRoute', JPATH_SITE . '/components/com_content/helpers/route.php');

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', '.multipleTags', null, array('placeholder_text_multiple' => Text::_('JOPTION_SELECT_TAG')));
HTMLHelper::_('formbehavior.chosen', '.multipleCategories', null, array('placeholder_text_multiple' => Text::_('JOPTION_SELECT_CATEGORY')));
HTMLHelper::_('formbehavior.chosen', 'select');
HTMLHelper::_('behavior.modal');

$app       = Factory::getApplication();
$user      = Factory::getUser();
$userId    = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$saveOrder = $listOrder == 'a.ordering';
$columns   = 9;
$cnt = count($this->items);

if (strpos($listOrder, 'publish_up') !== false)
{
	$orderingColumn = 'publish_up';
}
elseif (strpos($listOrder, 'publish_down') !== false)
{
	$orderingColumn = 'publish_down';
}
elseif (strpos($listOrder, 'modified') !== false)
{
	$orderingColumn = 'modified';
}
else
{
	$orderingColumn = 'created';
}

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_xbarticleman&task=artlinks.saveOrderAjax&tmpl=component';
	HTMLHelper::_('sortablelist.sortable', 'articleList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

?>
<form action="<?php echo Route::_('index.php?option=com_xbarticleman&view=artlinks'); ?>" method="post" name="adminForm" id="adminForm">

<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif; ?>
		<?php
		// Search tools bar
		echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
		?>
		<?php if (empty($this->items)) : ?>
			<div class="alert alert-no-items">
				<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>
			<p><?php
                echo 'Showing '.$cnt.' items ';
				if ($cnt>10) { 
				    echo '(Link checking disabled while more than 10 items shown) ';
				} ?>
			</p>
			<div <?php if ($cnt>10) echo 'style="display:none;"';?> >
		         <p><b>Links to check:</b> Internal 
		         <input type="checkbox" name="checkint" value="1" 
		         <?php if (($this->checkint==1) && ($cnt<11)) echo 'checked="checked" ';
		              if ($cnt>10) echo 'disabled';
		          ?> /> 
		          External 
		          <input type="checkbox" name="checkext" value="1" 
		          <?php if (($this->checkext==1) && ($cnt<11)) echo 'checked="checked" ';
		              if ($cnt>10) echo 'disabled';
		          ?> /> <span style="padding-left:20px;"> </span>
    			<input type="button" class="btn" value="Check Now" onClick="this.form.submit();" 
		          <?php if ($cnt>10) { echo 'disabled';}?> /> 
</p>
				<?php  if ($cnt<11) :?>
				<div class="alert">
                    <p><i>NB Link checking may make the page take a while to load if there are many links 
                    - minimise number of links shown with filter and pagination settings before clicking [Check Now].</i>
                    </p>
				</div>
                <?php endif; ?>
			</div>
				
			<table class="table table-striped" id="articleList">
				<thead>
					<tr>
						<th width="1%" class="nowrap center hidden-phone">
							<?php echo HTMLHelper::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
						</th>
						<th width="1%" class="center">
							<?php echo HTMLHelper::_('grid.checkall'); ?>
						</th>
						<th width="1%" class="nowrap center">
							<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
						</th>
						<th style="min-width:100px" class="nowrap">
							<?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
							| (alias) |
							<?php echo HTMLHelper::_('searchtools.sort', 'Category', 'category_title', $listDirn, $listOrder); ?>							
						</th>
						<th>
							<span class="hasPopover" title="<?php echo Text::_('XBARTMAN_COL_RELLNK_TITLE'); ?> " 
							data-content="<?php echo Text::_('XBARTMAN_COL_RELLNK_DESC').Text::_('XBARTMAN_COL_LINKS_GENERIC'); ?>">
								<?php echo Text::_('XBARTMAN_COL_RELLNK_TITLE'); ?>
							</span>
						</th>
						<th>
							<span class="hasPopover" title="<?php echo Text::_('XBARTMAN_COL_LINKS_TITLE'); ?>" 
							data-content="<?php echo Text::_('XBARTMAN_COL_LINKS_DESC').Text::_('XBARTMAN_COL_LINKS_GENERIC'); ?>">
								<?php echo Text::_('XBARTMAN_COL_LINKS_TITLE'); ?>
							</span>
						</th>
						<th width="10%" class="hidden-phone">
							<span class="hasPopover" title="<?php echo Text::_('XBARTMAN_COL_TARGS_TITLE'); ?>"
							data-content=" <?php echo Text::_('XBARTMAN_COL_TARGS_DESC'); ?>">
								<?php echo Text::_('XBARTMAN_COL_TARGS_TITLE'); ?>
							</span>
						</th>
						<th width="10%" class="nowrap hidden-phone">
							<?php echo HTMLHelper::_('searchtools.sort', 'XBARTMAN_HEADING_DATE_' . strtoupper($orderingColumn), 'a.' . $orderingColumn, $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="nowrap hidden-phone">
							<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="<?php echo $columns; ?>">
						</td>
					</tr>
				</tfoot>
				<tbody>
				<?php foreach ($this->items as $i => $item) :
					$item->max_ordering = 0;
					$ordering   = ($listOrder == 'a.ordering');
					$canCreate  = $user->authorise('core.create',     'com_xbarticleman.category.' . $item->catid);
					$canEdit    = $user->authorise('core.edit',       'com_xbarticleman.article.' . $item->id);
					$canCheckin = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
					$canEditOwn = $user->authorise('core.edit.own',   'com_xbarticleman.article.' . $item->id) && $item->created_by == $userId;
					$canChange  = $user->authorise('core.edit.state', 'com_xbarticleman.article.' . $item->id) && $canCheckin;
					$canEditCat    = $user->authorise('core.edit',       'com_xbarticleman.category.' . $item->catid);
					$canEditOwnCat = $user->authorise('core.edit.own',   'com_xbarticleman.category.' . $item->catid) && $item->category_uid == $userId;
					$canEditParCat    = $user->authorise('core.edit',       'com_xbarticleman.category.' . $item->parent_category_id);
					$canEditOwnParCat = $user->authorise('core.edit.own',   'com_xbarticleman.category.' . $item->parent_category_id) && $item->parent_category_uid == $userId;
					//$helper = new XbarticlemanHelper;
					$links = XbarticlemanHelper::getDocAnchors($item->arttext);
					//$tags = $helper->getItemTags('com_content.article',$item->id);
					?>
					<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid; ?>">
						<td class="order nowrap center hidden-phone">
							<?php
							$iconClass = '';
							if (!$canChange)
							{
								$iconClass = ' inactive';
							}
							elseif (!$saveOrder)
							{
								$iconClass = ' inactive tip-top hasTooltip" title="' . HTMLHelper::_('tooltipText', 'JORDERINGDISABLED');
							}
							?>
							<span class="sortable-handler<?php echo $iconClass ?>">
								<span class="icon-menu" aria-hidden="true"></span>
							</span>
							<?php if ($canChange && $saveOrder) : ?>
								<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order" />
							<?php endif; ?>
							<?php echo $item->ordering;?>
						</td>
						<td class="center">
							<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
						</td>
						<td class="center">
							<div class="btn-group">
								<?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'artlinks.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
								<?php //echo HTMLHelper::_('contentadministrator.featured', $item->featured, $i, $canChange); ?>
								<?php // Create dropdown items and render the dropdown list.
								if ($canChange)
								{
									HTMLHelper::_('actionsdropdown.' . ((int) $item->state === 2 ? 'un' : '') . 'archive', 'cb' . $i, 'artlinks');
									HTMLHelper::_('actionsdropdown.' . ((int) $item->state === -2 ? 'un' : '') . 'trash', 'cb' . $i, 'artlinks');
									echo HTMLHelper::_('actionsdropdown.render', $this->escape($item->title));
								}
								?>
							</div>
						</td>
						<td class="has-context">
							<div class="pull-left">
								<?php if ($item->checked_out) : ?>
									<?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'articles.', $canCheckin); ?>
								<?php endif; ?>
								<?php if ($canEdit || $canEditOwn) : ?>
									<a class="hasTooltip" href="
									<?php echo Route::_('index.php?option=com_xbarticleman&task=article.edit&id=' . $item->id).'&retview=artlinks';?>
									" title="<?php echo Text::_('JACTION_EDIT').' '.Text::_('tags & links'); ?>">
										<?php echo $this->escape($item->title); ?></a>
								<?php else : ?>
									<span title="<?php echo Text::sprintf('JFIELD_ALIAS_LABEL', $this->escape($item->alias)); ?>"><?php echo $this->escape($item->title); ?></span>
								<?php endif; ?>
								<br />
								<span class="small">
										<?php echo '(Alias: <a class="modal hasTooltip" title="'.Text::_('XBARTMAN_MODAL_PREVIEW').'" href="'.Uri::root().'index.php?option=com_content&view=article&id='.(int)$item->id.'&tmpl=component">';
										echo $this->escape($item->alias).' <span class="icon-eye"></span></a>)'; ?>
								</span>
								<div class="small">
									<?php
									$ParentCatUrl = Route::_('index.php?option=com_categories&task=category.edit&id=' . $item->parent_category_id . '&extension=com_content');
									$CurrentCatUrl = Route::_('index.php?option=com_categories&task=category.edit&id=' . $item->catid . '&extension=com_content');
									$EditCatTxt = Text::_('JACTION_EDIT') . ' ' . Text::_('JCATEGORY');

										if ($item->category_level != '1') :
											     $bits = explode('/', $item->category_path);
											     for ($i=0; $i<$item->category_level-1; $i++) {
    											     echo $bits[$i].' &#187; ';
											     }
										endif;
										echo '<span style="padding-left:15px;">';
										if ($canEditCat || $canEditOwnCat) :
											echo '<a class="hasTooltip label label-success" href="' . $CurrentCatUrl . '" title="' . $EditCatTxt . '">';
										endif;
										echo $this->escape($item->category_title);
										if ($canEditCat || $canEditOwnCat) :
											echo '</a>';
										endif;
										if ($item->category_level != '1') :
										  echo '</span>';
										endif;
									?>
								</div>
							</div>
						</td>
						<td class="small"><?php 
							$urls = json_decode($item->urls); 
							if ($urls->urla) {
							    echo 'A: '.XbarticlemanHelper::getLinkDisplay($urls->urla, $urls->urlatext, $urls->targeta, $this->checkint, $this->checkext);
							}
							if ($urls->urlb) {
							    echo 'B: '.XbarticlemanHelper::getLinkDisplay($urls->urlb, $urls->urlbtext, $urls->targetb, $this->checkint, $this->checkext);
							}
							if ($urls->urlc) {
							    echo 'C: '.XbarticlemanHelper::getLinkDisplay($urls->urlc, $urls->urlctext, $urls->targetc, $this->checkint, $this->checkext);
							}
							?>
						</td>
						<td class="small">							
							<?php
							if (count($links["pageLinks"]) >0) {
							    echo '<b>Page Links:</b> : '.count($links["pageLinks"]).'<br /> ';
							    foreach ($links["pageLinks"] as $a) {
							        echo '<span class="hasTooltip" title="'.$a->textContent.'">';
							        echo $a->getAttribute('href').'</span>, ';
							    }
							    echo '<br />';
							}
							if (count($links["localLinks"]) >0) {
							    echo '<b>Internal Links: </b> : '.count($links["localLinks"]).'<br /> ';
							    foreach ($links["localLinks"] as $a) {
							        $targ = ($a->getAttribute('target') == '_blank') ? 1 : 0;
							        echo XbarticlemanHelper::getLinkDisplay($a->getAttribute('href'), $a->textContent, $targ, $this->checkint, $this->checkext);
 							    }
							}
							if (count($links["extLinks"]) >0) {
							    echo '<b>External Links: </b> : '.count($links["extLinks"]).'<br /> ';
							    foreach ($links["extLinks"] as $a) {
							        $targ = ($a->getAttribute('target') == '_blank') ? 1 : 0;
							        echo XbarticlemanHelper::getLinkDisplay($a->getAttribute('href'), $a->textContent, $targ, $this->checkint, $this->checkext);
							    }
							}							
							if (count($links["others"]) >0) {
							    echo '<b>Others: </b> : '.count($links["others"]).'<br /> ';
							    foreach ($links["others"] as $a) {
							        echo '<span class="hasTooltip" title="'.$a->textContent.'">';
							        echo $a->getAttribute('href').'</span>, ';
							    }
							}
							?>
						</td>
						<td class="small">
							<?php 
							echo count($links["pageTargs"]).' targets found<br />';
							if (count($links["pageTargs"]) >0) {
							    foreach ($links["pageTargs"] as $a) {
							        echo 'id:'.$a->getAttribute('id').'<br />';
							    }
							}							
							?>
						</td>
						<td class="nowrap small hidden-phone">
							<?php
							$date = $item->{$orderingColumn};
							echo $date > 0 ? HTMLHelper::_('date', $date, Text::_('D d M \'y')) : '-';
							?>
						</td>
						<td class="hidden-phone">
							<?php echo (int) $item->id; ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<?php // Load the batch processing form. ?>
			<?php if ($user->authorise('core.create', 'com_xbarticleman')
				&& $user->authorise('core.edit', 'com_xbarticleman')
				&& $user->authorise('core.edit.state', 'com_xbarticleman')) : ?>
				<?php echo HTMLHelper::_(
					'bootstrap.renderModal',
					'collapseModal',
					array(
						'title'  => Text::_('XBARTMAN_BATCH_OPTIONS'),
						'footer' => $this->loadTemplate('batch_footer'),
					),
					$this->loadTemplate('batch_body')
				); ?>
			<?php endif; ?>
		<?php endif; ?>

		<?php echo $this->pagination->getListFooter(); ?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
</form>

<div class="clearfix"></div>
<?php echo XbarticlemanHelper::credit('xbArticleMan');?>
