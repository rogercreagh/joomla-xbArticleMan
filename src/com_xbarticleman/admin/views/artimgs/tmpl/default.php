<?php
/*******
 * @package xbarticleman
 * @filesource administrator/components/com_xbarticleman/views/artimgs/tmpl/default.php
 * @version 2.0.6.5 16th November 2023
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
$rowcnt = count($this->items);

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
	$saveOrderingUrl = 'index.php?option=com_xbarticleman&task=arttags.saveOrderAjax&tmpl=component';
	HTMLHelper::_('sortablelist.sortable', 'articleList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

?>
<form action="<?php echo Route::_('index.php?option=com_xbarticleman&view=artimgs'); ?>" method="post" name="adminForm" id="adminForm">

<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif; ?>
		<h3><?php echo Text::_('XBARTMAN_ARTICLE_IMAGES')?></h3>
		<h4><?php echo Text::_('XBARTMAN_TOTAL_ARTICLES').' '.$this->totalarticles.'. '.Text::_('XB_LISTING').' '.$this->statearticles.' '.lcfirst(Text::_('XB_ARTICLES')).' '.$this->statefilt; ?></h4>
		<p> 
    	<?php if (array_key_exists('artlist', $this->activeFilters)) {
    	    echo Text::_('XBARTMAN_FILTERED_TO_SHOW').' '.$this->pagination->total.' ';
    	    $prompts = array('articles','articles with &lt;img&gt; tags.','articles with Intro or Fulltext images.','articles with &lt;img&gt; tags or Intro or Fulltext images.'
    	        ,'articles with no &lt;img&gt; tags.','articles with no Intro or Fulltext images.','articles with no images (Intro, Fulltext, or &lt;img&gt; tags).');
    	    if ($this->activeFilters['artlist'] > 0) {
    	        echo Text::_($prompts[$this->activeFilters['artlist']]);
    	    } else {
    	        echo lcfirst(Text::_('XB_ARTICLES'));
    	    }
    	} else {
    	    echo Text::_('XBARTMAN_SHOWING_ALL').' '.$this->statearticles.' '.lcfirst(Text::_('XB_ARTICLES'));
    	}
        ?>
        </p>
		<?php
		// Search tools bar
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
            <p><center>Auto close details dropdowns <input  type="checkbox" id="autoclose" name="autoclose" value="yes" checked="true" style="margin:0 5px;" /></center></p>
			
			<table class="table table-striped table-hover" id="articleList">
			<colgroup>
				<col class="nowrap center hidden-phone" style="width:25px;"><!-- ordering -->
				<col class="center hidden-phone" style="width:25px;"><!-- checkbox -->
				<col class="nowrap center" style="width:55px;"><!-- status -->
				<col ><!-- title, -->
				<col style="width:450px;"><!-- imgs -->
				<col style="width:450px;"><!-- intro/full -->
				<col class="nowrap hidden-phone" style="width:110px;" ><!-- date -->
				<col class="nowrap hidden-phone" style="width:45px;"><!-- id -->
			</colgroup>	
				<thead>
					<tr style="background-color:#d7d7d7;">
						<th>
							<?php echo HTMLHelper::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
						</th>
						<th>
							<?php echo HTMLHelper::_('grid.checkall'); ?>
						</th>
						<th>
							<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
							<span class="xbnorm xbo9">(edit) (pv) |</span>  alias <span class="xbnorm xb09"> | </span>
							<?php echo HTMLHelper::_('searchtools.sort', 'XB_CATEGORY', 'category_title', $listDirn, $listOrder); ?>							
						</th>
						<th>
							<?php echo Text::_('XBARTMAN_INART_IMAGES'); ?>
						</th>
						<th>
							<?php echo Text::_('XBARTMAN_ARTICLE_INTRO_FULL_IMAGES'); ?>
						</th>
						<th>
							<?php echo HTMLHelper::_('searchtools.sort', 'XBARTMAN_HEADING_DATE_' . strtoupper($orderingColumn), 'a.' . $orderingColumn, $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>
				<?php if ($rowcnt > 9) : ?>
				<tfoot>
					<tr style="background-color:#d7d7d7;">
						<th>
							<?php echo HTMLHelper::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
						</th>
						<th>
							<?php echo HTMLHelper::_('grid.checkall'); ?>
						</th>
						<th>
							<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
							<span class="xbnorm xbo9">(edit) (pv) |</span>  alias <span class="xbnorm xb09"> | </span>
							<?php echo HTMLHelper::_('searchtools.sort', 'XB_CATEGORY', 'category_title', $listDirn, $listOrder); ?>							
						</th>
						<th>
							<?php echo Text::_('XBARTMAN_INART_IMAGES'); ?>
						</th>
						<th>
							<?php echo Text::_('XBARTMAN_INTRO_FULL_IMAGES'); ?>
						</th>
						<th>
							<?php echo HTMLHelper::_('searchtools.sort', 'XBARTMAN_HEADING_DATE_' . strtoupper($orderingColumn), 'a.' . $orderingColumn, $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</tfoot>
				<?php endif; ?>
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
					$helper = new XbarticlemanHelper;
					$imgs = $helper->getDocImgs($item->arttext);
					$intfull = json_decode($item->images);
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
								<?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'arttags.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
								<?php //echo HTMLHelper::_('contentadministrator.featured', $item->featured, $i, $canChange); ?>
								<?php // Create dropdown items and render the dropdown list.
								if ($canChange)
								{
									HTMLHelper::_('actionsdropdown.' . ((int) $item->state === 2 ? 'un' : '') . 'archive', 'cb' . $i, 'arttags');
									HTMLHelper::_('actionsdropdown.' . ((int) $item->state === -2 ? 'un' : '') . 'trash', 'cb' . $i, 'arttags');
									echo HTMLHelper::_('actionsdropdown.render', $this->escape($item->title));
								}
								?>
							</div>
						</td>
						<td class="has-context">
							<div class="pull-left"><p>
								<?php if ($item->checked_out) : ?>
									<?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'arttags.', $canCheckin); ?>
								<?php endif; ?>
								<?php if ($canEdit || $canEditOwn) : ?>
									<a class="hasTooltip" href="
									<?php echo Route::_('index.php?option=com_xbarticleman&task=article.edit&id=' . $item->id).'&retview=artimgs';?>
									" title="<?php echo Text::_('XBARTMAN_QUICK_EDIT'); ?>">
										<?php echo $this->escape($item->title); ?></a> 
									<a class="hasTooltip" href="
									<?php echo Route::_('index.php?option=com_content&task=article.edit&id=' . $item->id);?>
									" title="<?php echo Text::_('XBARTMAN_FULL_EDIT'); ?>" >										
										<span class="icon-edit xbpl10"></span></a>
								<?php else : ?>
									<span title="<?php echo Text::sprintf('JFIELD_ALIAS_LABEL', $this->escape($item->alias)); ?>"><?php echo $this->escape($item->title); ?></span>
								<?php endif; ?>
								<?php $pvuri = "'".(Uri::root().'index.php?option=com_content&view=article&id='.$item->id)."'"; ?>
                                <a class="hasTooltip"  data-toggle="modal" title="<?php echo Text::_('XBARTMAN_MODAL_PREVIEW'); ?>" href="#pvModal"
                                onClick="window.pvuri=<?php echo $pvuri; ?>;">
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
						<td>
							<b><?php echo count($item->imgtags); ?></b> images found<br />
							<?php foreach ($item->imgtags as $a) : ?>
    							<details>
    								<summary>
    									<?php if ($a['nativesize']=='??') : ?>
        									<span style="color:red;"><?php echo $a['filename']; ?></span>
    									<?php else : ?>
    										<?php echo $a['filename']; ?> 
        									<a href="<?php echo $a['uri']; ?>" class="modal"> <span class="icon-eye"></span> </a>
    									<?php endif; ?>
    								</summary>
									<ul>
										<li><i>Host:</i>
											<?php echo ($a['host']=='') ? 'local' : $a['host']; ?>
										</li>
										<li><i>Path:</i>
											<?php echo $a['path'];?>
										</li>
										<li><i>Dimensions - native:</i>
											<?php echo $a['nativesize'];
											echo ($a['specsize'] != '') ? ' <i>img spec:</i> '.$a['specsize'] : ''; 
											?>
										</li>
										<li><i>Mime type:</i>
											<?php echo $a['mime'];?>
										</li>
										<?php if ($a['alttext'] != '') : ?>
    										<li><i>Alt.text:</i>
    											<?php echo $a['alttext'];?>
    										</li>   						
										<?php endif; ?>				
										<?php if ($a['title'] != '') : ?>
    										<li><i>Title</i>
    											<?php echo $a['alttext'];?>
    										</li>   						
										<?php endif; ?>				
										<?php if ($a['class'] != '') : ?>
    										<li><i>Class:</i>
    											<?php echo $a['class'];?>
    										</li>
										<?php endif; ?>
										<?php if ($a['style'] != '') : ?>
    										<li><i>Style:</i>
    											<?php echo $a['style'];?>
    										</li>   						
										<?php endif; ?>				
									</ul>
    							</details>
    						<?php endforeach; ?>
						</td>
						<td>
							<?php $a = $item->introimg;
							if (key_exists('uri',$a) ) : ?>
								<details>
									<summary><i>Intro</i> <?php echo $a['filename']; ?>
    									<a href="<?php echo $a['uri']; ?>" class="modal"> <span class="icon-eye"></span> </a>
									</summary>
									<ul>
										<li><i>Host:</i>
											<?php echo ($a['host']=='') ? 'local' : $a['host']; ?>
										</li>
										<li><i>Path:</i>
											<?php echo $a['path'];?>
										</li>
										<li><i>Dimensions - native:</i>
											<?php echo $a['nativesize']; ?>
										</li>
										<li><i>Mime type:</i>
											<?php echo $a['mime'];?>
										</li>
										<?php if ($a['alttext'] != '') : ?>
    										<li><i>Alt.text:</i>
    											<?php echo $a['alttext'];?>
    										</li>   						
										<?php endif; ?>				
										<?php if ($a['caption'] != '') : ?>
    										<li><i>Caption:</i>
    											<?php echo $a['caption'];?>
    										</li>   						
										<?php endif; ?>				
									</ul>
								</details>
							<?php endif; ?>
							<?php $a = $item->fullimg;
							if (key_exists('uri',$a) ) : ?>
								<details>
									<summary><i>Full</i> <?php echo $a['filename']; ?>
    									<a href="<?php echo $a['uri']; ?>" class="modal"> <span class="icon-eye"></span> </a>
									</summary>
									<ul>
										<li><i>Host:</i>
											<?php echo ($a['host']=='') ? 'local' : $a['host']; ?>
										</li>
										<li><i>Path:</i>
											<?php echo $a['path'];?>
										</li>
										<li><i>Dimensions - native:</i>
											<?php echo $a['nativesize']; ?>
										</li>
										<li><i>Mime type:</i>
											<?php echo $a['mime'];?>
										</li>
										<?php if ($a['alttext'] != '') : ?>
    										<li><i>Alt.text:</i>
    											<?php echo $a['alttext'];?>
    										</li>   						
										<?php endif; ?>				
										<?php if ($a['caption'] != '') : ?>
    										<li><i>Caption:</i>
    											<?php echo $a['caption'];?>
    										</li>   						
										<?php endif; ?>				
									</ul>
								</details>
							<?php endif; ?>
							
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

