<?php
/*******
 * @package xbartman
 * file administrator/components/com_xbartman/views/artlinks/tmpl/default.php
 * @version 1.0.0.0 27th January 2019
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2019
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
defined('_JEXEC') or die;
use Joomla\Registry\Registry;
//use Joomla\Utilities\ArrayHelper;

JLoader::register('XbarticlemanHelper', JPATH_ADMINISTRATOR . '/components/com_xbartman/helpers/xbartman.php');
JLoader::register('ContentHelperRoute', JPATH_SITE . '/components/com_content/helpers/route.php');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', '.multipleTags', null, array('placeholder_text_multiple' => JText::_('JOPTION_SELECT_TAG')));
JHtml::_('formbehavior.chosen', '.multipleCategories', null, array('placeholder_text_multiple' => JText::_('JOPTION_SELECT_CATEGORY')));
JHtml::_('formbehavior.chosen', 'select');
JHTML::_('behavior.modal');

$app       = JFactory::getApplication();
$user      = JFactory::getUser();
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
	$saveOrderingUrl = 'index.php?option=com_xbartman&task=articles.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'articleList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

?>
<form action="<?php echo JRoute::_('index.php?option=com_xbartman&view=artlinks'); ?>" method="post" name="adminForm" id="adminForm">

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
		echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
		?>
		<?php if (empty($this->items)) : ?>
			<div class="alert alert-no-items">
				<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
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
							<?php echo JHtml::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
						</th>
						<th width="1%" class="center">
							<?php echo JHtml::_('grid.checkall'); ?>
						</th>
						<th width="1%" class="nowrap center">
							<?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
						</th>
						<th style="min-width:100px" class="nowrap">
							<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
							| (alias) |
							<?php echo JHtml::_('searchtools.sort', 'Category', 'category_title', $listDirn, $listOrder); ?>							
						</th>
						<th>
							<span class="hasPopover" title="<?php echo JText::_('XBARTMAN_COL_RELLNK_TITLE'); ?> " 
							data-content="<?php echo JText::_('XBARTMAN_COL_RELLNK_DESC').JText::_('XBARTMAN_COL_LINKS_GENERIC'); ?>">
								<?php echo JText::_('XBARTMAN_COL_RELLNK_TITLE'); ?>
							</span>
						</th>
						<th>
							<span class="hasPopover" title="<?php echo JText::_('XBARTMAN_COL_LINKS_TITLE'); ?>" 
							data-content="<?php echo JText::_('XBARTMAN_COL_LINKS_DESC').JText::_('XBARTMAN_COL_LINKS_GENERIC'); ?>">
								<?php echo JText::_('XBARTMAN_COL_LINKS_TITLE'); ?>
							</span>
						</th>
						<th width="10%" class="hidden-phone">
							<span class="hasPopover" title="<?php echo JText::_('XBARTMAN_COL_TARGS_TITLE'); ?>"
							data-content=" <?php echo JText::_('XBARTMAN_COL_TARGS_DESC'); ?>">
								<?php echo JText::_('XBARTMAN_COL_TARGS_TITLE'); ?>
							</span>
						</th>
						<th width="10%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'XBARTMAN_HEADING_DATE_' . strtoupper($orderingColumn), 'a.' . $orderingColumn, $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
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
					$canCreate  = $user->authorise('core.create',     'com_xbartman.category.' . $item->catid);
					$canEdit    = $user->authorise('core.edit',       'com_xbartman.article.' . $item->id);
					$canCheckin = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
					$canEditOwn = $user->authorise('core.edit.own',   'com_xbartman.article.' . $item->id) && $item->created_by == $userId;
					$canChange  = $user->authorise('core.edit.state', 'com_xbartman.article.' . $item->id) && $canCheckin;
					$canEditCat    = $user->authorise('core.edit',       'com_xbartman.category.' . $item->catid);
					$canEditOwnCat = $user->authorise('core.edit.own',   'com_xbartman.category.' . $item->catid) && $item->category_uid == $userId;
					$canEditParCat    = $user->authorise('core.edit',       'com_xbartman.category.' . $item->parent_category_id);
					$canEditOwnParCat = $user->authorise('core.edit.own',   'com_xbartman.category.' . $item->parent_category_id) && $item->parent_category_uid == $userId;
					$helper = new XbarticlemanHelper;
					$links = $helper->getDocAnchors($item->arttext);
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
								$iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::_('tooltipText', 'JORDERINGDISABLED');
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
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						<td class="center">
							<div class="btn-group">
								<?php echo JHtml::_('jgrid.published', $item->state, $i, 'articles.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
								<?php //echo JHtml::_('contentadministrator.featured', $item->featured, $i, $canChange); ?>
								<?php // Create dropdown items and render the dropdown list.
								if ($canChange)
								{
									JHtml::_('actionsdropdown.' . ((int) $item->state === 2 ? 'un' : '') . 'archive', 'cb' . $i, 'articles');
									JHtml::_('actionsdropdown.' . ((int) $item->state === -2 ? 'un' : '') . 'trash', 'cb' . $i, 'articles');
									echo JHtml::_('actionsdropdown.render', $this->escape($item->title));
								}
								?>
							</div>
						</td>
						<td class="has-context">
							<div class="pull-left">
								<?php if ($item->checked_out) : ?>
									<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'articles.', $canCheckin); ?>
								<?php endif; ?>
								<?php if ($canEdit || $canEditOwn) : ?>
									<a class="hasTooltip" href="
									<?php echo JRoute::_('index.php?option=com_xbartman&task=article.edit&id=' . $item->id).'&retview=artlinks';?>
									" title="<?php echo JText::_('JACTION_EDIT').' '.JText::_('tags & links'); ?>">
										<?php echo $this->escape($item->title); ?></a>
								<?php else : ?>
									<span title="<?php echo JText::sprintf('JFIELD_ALIAS_LABEL', $this->escape($item->alias)); ?>"><?php echo $this->escape($item->title); ?></span>
								<?php endif; ?>
								<br />
								<span class="small">
										<?php echo '(Alias: <a class="modal hasTooltip" title="'.JText::_('XBARTMAN_MODAL_PREVIEW').'" href="'.JUri::root().'index.php?option=com_content&view=article&id='.(int)$item->id.'&tmpl=component">';
										echo $this->escape($item->alias).'</a>)'; ?>
								</span>
								<div class="small">
									<?php
									$ParentCatUrl = JRoute::_('index.php?option=com_categories&task=category.edit&id=' . $item->parent_category_id . '&extension=com_content');
									$CurrentCatUrl = JRoute::_('index.php?option=com_categories&task=category.edit&id=' . $item->catid . '&extension=com_content');
									$EditCatTxt = JText::_('JACTION_EDIT') . ' ' . JText::_('JCATEGORY');

										if ($item->category_level != '1') :
											     $bits = explode('/', $item->category_path);
											     for ($i=0; $i<$item->category_level-1; $i++) {
    											     echo $bits[$i].' &#187; ';
											     }
										endif;
										echo '<br /><span style="padding-left:15px;">';
										if ($canEditCat || $canEditOwnCat) :
											echo '<a class="hasTooltip" href="' . $CurrentCatUrl . '" title="' . $EditCatTxt . '">';
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
							echo $date > 0 ? JHtml::_('date', $date, JText::_('D d M \'y')) : '-';
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
			<?php if ($user->authorise('core.create', 'com_xbartman')
				&& $user->authorise('core.edit', 'com_xbartman')
				&& $user->authorise('core.edit.state', 'com_xbartman')) : ?>
				<?php echo JHtml::_(
					'bootstrap.renderModal',
					'collapseModal',
					array(
						'title'  => JText::_('XBARTMAN_BATCH_OPTIONS'),
						'footer' => $this->loadTemplate('batch_footer'),
					),
					$this->loadTemplate('batch_body')
				); ?>
			<?php endif; ?>
		<?php endif; ?>

		<?php echo $this->pagination->getListFooter(); ?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
