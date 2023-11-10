<?php
/*******
 * @package xbArticleManager
 * file administrator/components/com_xbarticleman/views/arttags/tmpl/default.php
 * @version 2.0.5.0 10th November 2023
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

//JLoader::register('XbarticlemanHelper', JPATH_ADMINISTRATOR . '/components/com_xbarticleman/helpers/xbarticleman.php');

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');

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
$columns   = 10;

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

<form action="<?php echo Route::_('index.php?option=com_xbarticleman&view=arttags'); ?>" method="post" name="adminForm" id="adminForm">
<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif; ?>
		<h3><?php echo Text::_('Articles with Tags')?></h3>
		<h4> Found <?php echo count($this->alltags); ?> distinct tags across <?php echo $this->taggedarticles; ?> tagged articles from <?php echo $this->statearticles.' '.$this->statefilt; ?> articles</h4>
    	<ul class="inline">
    		<li><i>Counts for each type:</i></li>
    		<?php foreach ($this->alltags as $key=>$cnt) : ?>
    		    <li><span class="label label-tag"><?php echo $key.' ('.$cnt.')'; ?></span></li>
    		<?php endforeach; ?>
    	</ul>
    	<p><?php echo Text::_('Unfiltered list shows').' ';
    	if (array_key_exists('artlist', $this->activeFilters)) {
    	    switch ($this->activeFilters['artlist']) {
    	    case 2:
    	        echo $this->pagination->total.' '.Text::_('articles without tags');
    	       break;
    	    case 1:
    	       echo $this->pagination->total.' '.Text::_('tagged articles');
    	       break;
    	    default:
    	       echo Text::_('all articles');
    	       break;
    	   }  	    
    	} else {
    	    echo $this->pagination->total.' '.Text::_('tagged articles');
    	}

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
			<?php $columns   = 7; 
                $rowcnt = count($this->items);
			?>	
    		<p>              
                <?php echo 'Sorted by '.$listOrder.' '.$listDirn ; ?>
    		</p>
            <p><center>Auto close details dropdowns <input  type="checkbox" id="autoclose" name="autoclose" value="yes" checked="true" style="margin:0 5px;" /></center></p>
			
			<table class="table table-striped" id="articleList">
    			<colgroup>
    				<col class="nowrap center hidden-phone" style="width:25px;"><!-- ordering -->
    				<col class="center hidden-phone" style="width:25px;"><!-- checkbox -->
    				<col style="width:55px;"><!-- status -->
    				<col ><!-- title, -->
    				<col ><!-- tags -->
    				<col class="nowrap hidden-phone" style="width:110px;" ><!-- date -->
    				<col class="nowrap hidden-phone" style="width:45px;"><!-- id -->
    			</colgroup>	
				<thead>
					<tr>
						<th>
							<?php echo HTMLHelper::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
						</th>
						<th >
							<?php echo HTMLHelper::_('grid.checkall'); ?>
						</th>
						<th >
							<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
						</th>
						<th >
							<?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
							<span class="xbnorm xbo9">(edit) |</span>  alias <span class="xbnorm xb09">(pv) |</span>
							<?php echo HTMLHelper::_('searchtools.sort', 'Category', 'category_title', $listDirn, $listOrder); ?>							
						</th>
						<th>
							<?php echo Text::_('Tags'); ?>
						</th>
						<th >
							<?php echo HTMLHelper::_('searchtools.sort', 'XBARTMAN_HEADING_DATE_' . strtoupper($orderingColumn), 'a.' . $orderingColumn, $listDirn, $listOrder); ?>
						</th>
						<th >
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
					$helper = new TagsHelper;
					$itemtags = $helper->getItemTags('com_content.article',$item->id);
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
							<div class="pull-left break-word"><p>
								<?php if ($item->checked_out) : ?>
									<?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'articles.', $canCheckin); ?>
								<?php endif; ?>
								<?php if ($canEdit || $canEditOwn) : ?>
									<a class="hasTooltip" href="
									<?php echo Route::_('index.php?option=com_xbarticleman&task=article.edit&id=' . $item->id).'&retview=artimgs';?>
									" title="<?php echo Text::_('quick edit (not content)'); ?>">
										<?php echo $this->escape($item->title); ?></a> 
									<a class="hasTooltip" href="
									<?php echo Route::_('index.php?option=com_content&task=article.edit&id=' . $item->id).'&retview=artimgs';?>
									" title="<?php echo Text::_('Full edit'); ?>">										
										<span class="icon-edit"></span></a>
								<?php else : ?>
									<span title="<?php echo Text::sprintf('JFIELD_ALIAS_LABEL', $this->escape($item->alias)); ?>"><?php echo $this->escape($item->title); ?></span>
								<?php endif; ?>
								</p>
								<span><i>Alias</i>: <?php echo $this->escape($item->alias); ?>
										<?php echo '<a class="hasTooltip"  data-toggle="modal" title="'.JText::_('XBARTMAN_MODAL_PREVIEW').'" href="#pvModal"
                                        onClick="window.pvid='.$item->id.';">';
										echo ' <span class="icon-eye"></span></a>'; ?>
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
                        	<div class="tags small">
                        	<?php  
                                $founders = []; 
                                foreach ($itemtags as  $tag) :
                                    $founder = explode('/',$tag->path)[0];
                                    if (!array_key_exists($founder, $founders)) {
                                        $founders[$founder] = array('founder'=>$founder,'children'=>[]);
                                    }
                                    $founders[$founder]['children'][$tag->title] = $tag;
                                endforeach; 
                                ksort($founders);
                                foreach ($founders as $f) { ?>
                        			<span>
                        				<span class="tagline"><i><?php echo $f["founder"]; ?>:&nbsp; </i></span>
                                		<?php 
                                		ksort($f["children"]);
                                        foreach ($f["children"] as $tg) :
                                         //index.php?option=com_tags&task=tag.edit&id=
                                            echo '<a href="index.php?option=com_content&view=articles&filter[tag]='.$tg->id.'" class="label label-info">'.$tg->title.'</a> ';   		
                                        endforeach; ?>
                        	    	</span><br />       
                            	<?php } ?>
	</div>
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
						'title'  => Text::_('COM_CONTENT_BATCH_OPTIONS'),
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
					'title'  => Text::_('Article Preview'),
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

