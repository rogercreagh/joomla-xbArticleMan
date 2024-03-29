<?php
/*******
 * @package xbarticleman
 * file administrator/components/com_xbarticleman/views/artscodes/tmpl/default.php
 * @version 2.0.6.3 15th November 2023
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
use Joomla\Registry\Registry;

//use Joomla\Utilities\ArrayHelper;

//JLoader::register('XbarticlemanHelper', JPATH_ADMINISTRATOR . '/components/com_xbarticleman/helpers/xbarticleman.php');
//JLoader::register('ContentHelperRoute', JPATH_SITE . '/components/com_content/helpers/route.php');

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', '.multipleTags', null, array('placeholder_text_multiple' => Text::_('JOPTION_SELECT_TAG')));
HTMLHelper::_('formbehavior.chosen', '.multipleCategories', null, array('placeholder_text_multiple' => Text::_('JOPTION_SELECT_CATEGORY')));
HTMLHelper::_('formbehavior.chosen', 'select');
//HTMLHelper::_('behavior.modal');

$app       = Factory::getApplication();
$user      = Factory::getUser();
$userId    = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$saveOrder = $listOrder == 'a.ordering';
$columns   = 8;
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
	$saveOrderingUrl = 'index.php?option=com_xbarticleman&task=articles.saveOrderAjax&tmpl=component';
	HTMLHelper::_('sortablelist.sortable', 'articleList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

?>
<form action="<?php echo Route::_('index.php?option=com_xbarticleman&view=artscodes'); ?>" method="post" name="adminForm" id="adminForm">

<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif; ?>
		<h3><?php echo Text::_('ABARTMAN_ARTICLE_SHORTCODES')?></h3>
		<h4> <?php echo Text::_('XB_LISTING'); ?> <?php echo count($this->sccnts).' '.Text::_('XBARTMAN_DISTINCT_SHORTCODES').' '.$this->shortcodearticles; ?> 
			<?php echo Text::_('XBARTMAN_ARTICLES_USING_SHORTCODES').' '.$this->statearticles.' '.$this->statefilt.' '.lcfirst(Text::_('XB_ARTICLES')); ?></h4>
    	<ul class="inline">
    		<li><i><?php echo Text::_('XBARTMAN_COUNTS_SCODES'); ?>:</i></li>
    		<?php foreach ($this->sccnts as $key=>$cnt) : ?>
    		    <li><a href="index.php?option=com_xbarticleman&view=artscodes&sc=<?php echo $key; ?>&filter[scfilt]=<?php echo $key; ?>" 
					 class="label label-yellow"><?php echo $key; ?> (<?php echo $cnt; ?>)</a></li>
    	<?php endforeach; ?>
    	</ul>
       	<span class="xbnit xb09"><?php echo Text::_('XBARTMAN_CLICK_SCODE_ABOVE'); ?>.</span>
    	<p><?php echo Text::_('XB_LISTING').' ';
    	if (array_key_exists('artlist', $this->activeFilters)) {
    	    switch ($this->activeFilters['artlist']) {
    	    case 2:
    	        echo $this->pagination->total.' '.Text::_('XBARTMAN_ARTICLES_WITHOUT_SCODES');
    	       break;
    	    case 1:
    	       echo $this->pagination->total.' '.lcfirst(Text::_('XBARTMAN_ARTICLES_WITH_SCODES'));
    	       break;
    	    default:
    	       echo Text::_('XBARTMAN_ALL_ARTICLES');
    	       break;
    	   }  	    
    	} else {
    	    echo $this->pagination->total.' '.Text::_('XBARTMAN_ARTICLES_WITH_SCODES');
    	} ?>
    	<?php echo Text::_('XB_FROM').' '.XbarticlemanHelper::getItemCnt('#__content').' '.lcfirst(Text::_('XBARTMAN_TOTAL_ARTICLES')); ?>
    	<br /><span class="xbit xb09"><?php Text::_('XBARTMAN_ADD_FILTERS_BELOW'); ?></span>

		<?php // Search tools bar
		  echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
		?>

        <div class="pull-right pagination xbm0">
    		<?php  echo $this->pagination->getPagesLinks(); ?>
    	</div>
    
    	<div class="pull-left">
    		<?php  echo $this->pagination->getResultsCounter(); ?> 
          <?php if($this->pagination->pagesTotal > 1) echo ' on '.$this->pagination->getPagesCounter(); ?>
    	</div>
        <div class="clearfix"></div>      

		<?php if (empty($this->items)) : ?>
			<div class="alert alert-no-items">
				<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>
			<?php $columns   = 8; 
                $rowcnt = count($this->items);
			?>	
    		<p>              
                <?php echo 'Sorted by '.$listOrder.' '.$listDirn ; ?>
    		</p>
            <p><center><?php echo Text::_('XBARTMAN_AUTOCLOSE_DROPS'); ?> <input  type="checkbox" id="autoclose" name="autoclose" value="yes" checked="true" style="margin:0 5px;" /></center></p>
			
				
			<table class="table table-striped table-hover" id="xbArticlemanShortcodesList">
			<colgroup>
				<col class="nowrap center hidden-phone" style="width:25px;"><!-- ordering -->
				<col class="center hidden-phone" style="width:25px;"><!-- checkbox -->
				<col style="width:55px;"><!-- status -->
				<col ><!-- title, -->
				<col style="width:250px;"><!-- summary -->
				<col ><!-- artscodes -->
				<col class="nowrap hidden-phone" style="width:110px;" ><!-- date -->
				<col class="nowrap hidden-phone" style="width:45px;"><!-- id -->
			</colgroup>	
				<thead>
					<tr>
						<th>
							<?php echo HTMLHelper::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
						</th>
						<th>
							<?php echo HTMLHelper::_('grid.checkall'); ?>
						</th>
						<th class="nowrap center">
							<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
							<span class="xbnorm xbo9">(edit) (pv) |</span>  alias <span class="xbnorm xb09"> | </span>
							<?php echo HTMLHelper::_('searchtools.sort', 'Category', 'category_title', $listDirn, $listOrder); ?>							
						</th>
						<th>
							<?php echo Text::_('XB_SUMMARY'); ?>
						<th>
							<?php echo Text::_('XB_SCODES'); ?>
						</th>
						<th>
							<?php echo HTMLHelper::_('searchtools.sort', 'XBARTMAN_HEADING_DATE_'.strtoupper($orderingColumn), 'a.' . $orderingColumn, $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>
				<?php if ($rowcnt > 9) : ?>
				<tfoot>
					<tr>
						<th>
							<?php echo HTMLHelper::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
						</th>
						<th>
							<?php echo HTMLHelper::_('grid.checkall'); ?>
						</th>
						<th class="nowrap center">
							<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
							<span class="xbnorm xbo9">(edit) (pv) |</span>  alias <span class="xbnorm xb09"> | </span>
							<?php echo HTMLHelper::_('searchtools.sort', 'Category', 'category_title', $listDirn, $listOrder); ?>							
						</th>
						<th>
							<?php echo Text::_('XB_SUMMARY'); ?>
						<th>
							<?php echo Text::_('XB_SCODES'); ?>
						</th>
						<th>
							<?php echo HTMLHelper::_('searchtools.sort', 'XBARTMAN_HEADING_DATE_'.strtoupper($orderingColumn), 'a.' . $orderingColumn, $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</tfoot>
				<tbody>
				<?php endif; ?>
				<?php foreach ($this->items as $i => $item) :
					$item->max_ordering = 0;
//					$ordering   = ($listOrder == 'a.ordering');
//					$canCreate  = $user->authorise('core.create',     'com_xbarticleman.category.' . $item->catid);
					$canEdit    = $user->authorise('core.edit',       'com_xbarticleman.article.' . $item->id);
					$canCheckin = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
					$canEditOwn = $user->authorise('core.edit.own',   'com_xbarticleman.article.' . $item->id) && $item->created_by == $userId;
					$canChange  = $user->authorise('core.edit.state', 'com_xbarticleman.article.' . $item->id) && $canCheckin;
					$canEditCat    = $user->authorise('core.edit',       'com_xbarticleman.category.' . $item->catid);
					$canEditOwnCat = $user->authorise('core.edit.own',   'com_xbarticleman.category.' . $item->catid) && $item->category_uid == $userId;
//					$canEditParCat    = $user->authorise('core.edit',       'com_xbarticleman.category.' . $item->parent_category_id);
//					$canEditOwnParCat = $user->authorise('core.edit.own',   'com_xbarticleman.category.' . $item->parent_category_id) && $item->parent_category_uid == $userId;
					//$helper = new XbarticlemanHelper;
					$scodes = $item->artscodes; //XbarticlemanHelper::getDocShortcodes($item->arttext);
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
								<?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'articles.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
								<?php //echo HTMLHelper::_('contentadministrator.featured', $item->featured, $i, $canChange); ?>
								<?php // Create dropdown items and render the dropdown list.
								if ($canChange)
								{
									HTMLHelper::_('actionsdropdown.' . ((int) $item->state === 2 ? 'un' : '') . 'archive', 'cb' . $i, 'articles');
									HTMLHelper::_('actionsdropdown.' . ((int) $item->state === -2 ? 'un' : '') . 'trash', 'cb' . $i, 'articles');
									echo HTMLHelper::_('actionsdropdown.render', $this->escape($item->title));
								}
								?>
							</div>
						</td>
						<td class="has-context">
							<div class="pull-left"></div><p>
								<?php if ($item->checked_out) : ?>
									<?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'articles.', $canCheckin); ?>
								<?php endif; ?>
								<?php if ($canEdit || $canEditOwn) : ?>
									<a class="hasTooltip" href="
									<?php echo Route::_('index.php?option=com_xbarticleman&task=article.edit&id=' . $item->id).'&retview=artimgs';?>
									" title="<?php echo Text::_('XBARTMAN_QUICK_EDIT'); ?>">
										<?php echo $this->escape($item->title); ?></a> 
									<a class="hasTooltip" href="
									<?php echo Route::_('index.php?option=com_content&task=article.edit&id=' . $item->id);?>
									" title="<?php echo Text::_('XBARTMAN_FULL_EDIT'); ?>">										
										<span class="icon-edit xbpl10"></span></a>
								<?php else : ?>
									<span title="<?php echo Text::sprintf('JFIELD_ALIAS_LABEL', $this->escape($item->alias)); ?>"><?php echo $this->escape($item->title); ?></span>
								<?php endif; ?>
								<?php $pvuri = "'".(Uri::root().'index.php?option=com_content&view=article&id='.$item->id)."'"; ?>
                                <a class="hasTooltip"  data-toggle="modal" title="<?php echo Text::_('XBARTMAN_MODAL_PREVIEW'); ?>" href="#pvModal"
                                onClick="window.pvuri=<?php echo $pvuri; ?>;">';
									<span class="icon-eye xbpl10"></span></a>
								</p>
								<span class="xbpl20"><i>Alias</i>: <?php echo $this->escape($item->alias); ?>
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
    										echo '<span style="padding-left:15px;"><b>';
										endif;
										if ($canEditCat || $canEditOwnCat) :
											echo '<a class="hasTooltip label label-success" href="' . $CurrentCatUrl . '" title="' . $EditCatTxt . '">';
										endif;
										echo $this->escape($item->category_title);
										if ($canEditCat || $canEditOwnCat) :
											echo '</a>';
										endif;
										if ($item->category_level != '1') :
										  echo '</b></span>';
										endif;
									?>
								</div>
							</div>
						</td>
						<td>
							<?php echo XbarticlemanHelper::truncateToText($item->arttext,100,'exact',true); ?>
						</td>
						<td>
							<details>
								<summary><b><?php echo count($scodes); ?></b> artscodes in article. 
                            		<?php foreach ($item->thiscnts as $key=>$cnt) {
                            		    echo '<span style="display:inline-block;margin-right:10px;"><b>'.$key.'</b> : '.$cnt.'</span>';
                            		}?>
								</summary>
							   	<ul>
								<?php foreach ($scodes as $sc) : ?>
							       <li><b><?php echo $sc[1]; ?></b>
							       <?php if ((key_exists(3, $sc)) && ($sc[3] != '')) echo '&nbsp;&nbsp;<i>params:</i> '.$sc[3].'&nbsp;';
							       if ((key_exists(5, $sc)) && ($sc[4] !='')) echo '&nbsp;<i>content:</i> '.$sc[4]; ?>
							       </li>
								<?php endforeach; ?>
							   	</ul>
							</details>
						</td>
						<td class="nowrap small hidden-phone">
							<?php
							$date = $item->{$orderingColumn};
							echo $date > 0 ? HTMLHelper::_('date', $date, Text::_('D d M Y')) : '-';
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
					    'modalWidth' => '50',
					),
					$this->loadTemplate('batch_body')
				); ?>
			<?php endif; ?>
			<?php // Load the article preview modal ?>
			<?php echo HTMLHelper::_(
				'bootstrap.renderModal',
				'pvModal',
				array(
					'title'  => Text::_('XBARTMAN_ARTICLE_PREVIEW'),
					'footer' => '',
				    'height' => '900vh',
				    'bodyHeight' => '90',
				    'modalWidth' => '80',
				    'url' => Uri::root().'index.php?option=com_content&view=article&id='.'x'
				),
			); ?>

 		<?php endif; ?>

		<?php echo $this->pagination->getListFooter(); ?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
</form>
<script language="JavaScript" type="text/javascript"
  src="<?php echo Uri::root(); ?>media/com_xbarticleman/js/closedetails.js" ></script>
<script language="JavaScript" type="text/javascript"
  src="<?php echo Uri::root(); ?>media/com_xbarticleman/js/setifsrc.js" ></script>

<div class="clearfix"></div>
<?php echo XbarticlemanHelper::credit('xbArticleMan');?>

