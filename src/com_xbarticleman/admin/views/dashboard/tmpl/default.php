<?php
/*******
 * @package xbArticleMan
 * @filesource admin/views/dashboard/tmpl/default.php
 * @version 2.1.0.0 23rd November 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
// No direct access to this file
defined('_JEXEC') or die;


use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Layout\LayoutHelper;

HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');

$user = Factory::getUser();
$userId = $user->get('id');

?>
<form action="<?php echo Route::_('index.php?option=com_xbarticlemans&view=dashboard'); ?>" method="post" name="adminForm" id="adminForm">
<div class="row-fluid">
	<div id="j-sidebar-container">
		<?php echo $this->sidebar; ?>		
	</div>
	<div id="j-main-container" >
		<h3><?php echo Text::_('XB_STATUS_SUM'); ?></h3>
		<div class="xbwp100">
        	<div class="xbwp60 pull-left xbpr20">
				<div class="xbbox gradmag">
					<table class="xbwp100">
            			<colgroup>
            				<col style="width:40%;"><!--  -->
            				<col style="width:10%;"><!--  -->
            				<col style="width:40%;"><!--  -->
            				<col ><!--  -->
            			</colgroup>
            			<thead>
						<tr>
							<th colspan="2" style="text-align:left;">
            					<h4>
            						<?php echo Text::_('Articles on the site'); ?><span class="xbpl20 xbnit">(<?php echo Text::_('including archived and trashed'); ?>)</span>
            					</h4>
							</th>
							<th colspan="2">
								<span class="badge badge-info"><?php echo Text::_('XB_TOTAL').' '. $this->artcnts['total']; ?></span> 
							</th>
						</tr>
						</thead>
						<tr>
							<td><?php echo Text::_('Published'); ?> </td>
							<td><span class="badge <?php echo $this->artcnts['published']>0 ?'badge-info' : ''; ?>"><?php echo $this->artcnts['published']; ?></span></td>
							<td><?php echo Text::_('Unpublished'); ?> </td><td>
							<span class="badge <?php echo $this->artcnts['unpublished']>0 ?'badge-yellow' : ''; ?>"><?php echo $this->artcnts['unpublished']; ?></span></td>
						</tr>
						<tr>
							<td><?php echo Text::_('Archived'); ?> </td>
							<td><span class="badge <?php echo $this->artcnts['archived']>0 ?'badge-black' : ''; ?>"><?php echo $this->artcnts['archived']; ?></span></td>
							<td><?php echo Text::_('Trashed'); ?> </td>
							<td><span class="badge <?php echo $this->artcnts['trashed']>0 ?'badge-red' : ''; ?>"><?php echo $this->artcnts['trashed']; ?></span></td>
						</tr>
					</table>
				</div>
				<div class="xbbox gradgreen">
					<table class="xbwp100">
            			<colgroup>
            				<col style="width:40%;"><!--  -->
            				<col style="width:10%;"><!--  -->
            				<col style="width:40%;"><!--  -->
            				<col ><!-- title, -->
            			</colgroup>
            			<thead>
						<tr>
							<th colspan="2" style="text-align:left;">
            					<h4>
            						<?php echo Text::_('Categories'); ?><span class="xbpl20 xbnit">(<?php echo Text::_('all content categories, including unused'); ?>)</span>
            					</h4>
							</th>
							<th colspan="2">
								<span class="badge badge-cat"><?php echo Text::_('XB_TOTAL').' '. $this->artcnts['catcnt']; ?></span> 
							</th>
						</tr>
						</thead>
						<tr>
							<td><?php echo Text::_('Uncategorised articles'); ?></td>
							<td><span class="badge <?php echo $this->artcnts['uncat']>0 ?'badge-yellow' : ''; ?>"><?php echo $this->artcnts['uncat']; ?></span></td>
							<td><?php echo Text::_('Articles with no category'); ?> (<span class="xbred"><?php echo Text::_('error'); ?></span>)</td>
							<td><span class="badge <?php echo $this->artcnts['nocat']>0 ?'badge-red' : ''; ?>"><?php echo $this->artcnts['nocat']; ?></span></td>
						</tr>
					</table>
				</div>
				<div class="xbbox gradcyan">
					<table class="xbwp100">
            			<colgroup>
            				<col style="width:40%;"><!-- ordering -->
            				<col style="width:10%;"><!-- checkbox -->
            				<col style="width:40%;"><!-- status -->
            				<col ><!-- title, -->
            			</colgroup>
            			<thead>
						<tr>
							<th colspan="2" style="text-align:left;">
            					<h4>
            						<?php echo Text::_('Tags'); ?><span class="xbpl20 xbnit">(<?php echo Text::_('all tags, including ones only used in other components'); ?>)</span>
            					</h4>
							</th>
							<th colspan="2">
								<span class="badge badge-tag"><?php echo Text::_('XB_TOTAL').' '. $this->tagcnts['totaltags']; ?></span> 
							</th>
						</tr>
						</thead>
						<tr>
							<td><?php echo Text::_('Tags used in articles'); ?></td>
							<td><span class="badge <?php echo $this->tagcnts['tagsused']>0 ?'badge-cyan' : ''; ?>"><?php echo $this->tagcnts['tagsused']; ?></span></td>
						</tr>						
						<tr>
							<td><?php echo Text::_('Articles with tags'); ?></td>
							<td><span class="badge <?php echo $this->artcnts['tagged']>0 ?'badge-info' : ''; ?>"><?php echo $this->artcnts['tagged']; ?></span></td>
						</tr>
					</table>
				</div>

				<div class="xbbox gradblue">
					<table class="xbwp100">
            			<colgroup>
            				<col style="width:40%;"><!--  -->
            				<col style="width:10%;"><!--  -->
            				<col style="width:40%;"><!--  -->
            				<col ><!-- title, -->
            			</colgroup>
            			<thead>
						<tr>
							<th colspan="2" style="text-align:left;">
            					<h4>
            						<?php echo Text::_('Images'); ?><span class="xbpl20 xbnit"></span>
            					</h4>
							</th>
							<th colspan="2">
							</th>
						</tr>
						</thead>
						<tr>
							<td><?php echo Text::_('Articles with images in-content'); ?></td>
							<td><span class="badge <?php echo $this->artcnts['embimaged']>0 ?'badge-info' : ''; ?>"><?php echo $this->artcnts['embimaged']; ?></span></td>
							<td><?php echo Text::_('Images used in articles'); ?></td>
							<td><span class="badge <?php echo $this->imgagecnts['embed']>0 ?'badge-ltblue' : ''; ?>"><?php echo $this->imagecnts['embed']; ?></span></td>
						</tr>
						<tr>
							<td><?php echo Text::_('Articles using the related image fields'); ?></td>
							<td><span class="badge <?php echo $this->imagecnts['related']>0 ?'badge-ltmag' : ''; ?>"><?php echo $this->imagecnts['related']; ?></span></td>
							<td></td><td></td>
						</tr>
					</table>
				</div>
				
				<div class="xbbox gradyellow">
					<table class="xbwp100">
            			<colgroup>
            				<col style="width:40%;"><!--  -->
            				<col style="width:10%;"><!--  -->
            				<col style="width:40%;"><!--  -->
            				<col ><!-- title, -->
            			</colgroup>
            			<thead>
    						<tr>
    							<th colspan="2" style="text-align:left;">
                					<h4>
                						<?php echo Text::_('Articles with Links'); ?><span class="xbpl20 xbnit"></span>
                					</h4>
    							</th>
    							<th>
    								<span class="xbit xbpr10"><?php echo Text::_('in content'); ?></span>
    								<span class="badge <?php echo $this->artcnts['emblinked']>0 ?'badge-drkcyan' : ''; ?> xbpr20">
    									<?php echo Text::_('XB_TOTAL').' '. $this->artcnts['emblinked']; ?>
    								</span> 
    								<span class="xbit xbpr10"><?php echo Text::_('related links'); ?></span>
    								<span class="badge <?php echo $this->artcnts['rellinked']>0 ?'badge-drkcyan' : ''; ?>">
    									<?php echo Text::_('XB_TOTAL').' '. $this->artcnts['rellinked']; ?>
    								</span> 
    							</th>
    						</tr>
						</thead>
						<tbody>
						<tr>
							<td><?php echo Text::_('Total embedded links'); ?></td>
							<td><span class="badge <?php echo $this->linkcnts['totLinked']>0 ?'badge-info' : ''; ?>">
								<?php echo $this->linkcnts['totLinked']; ?>
							</span></td>
							<td></td><td></td>
						</tr>
						<tr>
							<td><?php echo Text::_('Local Links'); ?></td>
							<td><span class="badge <?php echo $this->linkcnts['localLinks']>0 ?'badge-ltgreen' : ''; ?>"><?php echo $this->linkcnts['localLinks']; ?></span></td>
							<td><?php echo Text::_('External Links'); ?></td>
							<td><span class="badge <?php echo $this->linkcnts['extLinks']>0 ?'badge-aoy' : ''; ?>"><?php echo $this->linkcnts['extLinks']; ?></span></td>
						</tr>
						<tr>
							<td><?php echo Text::_('Anchor targets in articles'); ?></td>
							<td><span class="badge <?php echo $this->linkcnts['pageTargs']>0 ?'badge-black' : ''; ?>"><?php echo $this->linkcnts['pageTargs']; ?></span></td>
							<td><?php echo Text::_('In-page links'); ?></td>
							<td><span class="badge <?php echo $this->linkcnts['pageLinks']>0 ?'badge-black' : ''; ?>"><?php echo $this->linkcnts['pageLinks']; ?></span></td>
						</tr>
						<tr>
							<td><?php echo Text::_('Other Link types in articles'); ?></td>
							<td><span class="badge <?php echo $this->linkcnts['others']>0 ?'badge-grey' : ''; ?>"><?php echo $this->linkcnts['others']; ?></span></td>
						</tr>
						<tr>
							<td><?php echo Text::_('Articles with related links'); ?></td>
							<td><span class="badge <?php echo $this->artcnts['rellinked']>0 ?'badge-ltmag' : ''; ?>"><?php echo $this->artcnts['rellinked']; ?></span></td>
														<td></td><td></td>
							
						</tr>
						</tbody>
					</table>
				</div>


				<div class="xbbox gradpink">
					<table class="xbwp100">
            			<colgroup>
            				<col style="width:40%;"><!--  -->
            				<col style="width:10%;"><!--  -->
            				<col style="width:40%;"><!--  -->
            				<col ><!-- title, -->
            			</colgroup>
            			<thead>
						<tr>
							<th colspan="2" style="text-align:left;">
            					<h4>
            						<?php echo Text::_('Articles with Shortcodes'); ?><span class="xbpl20 xbnit"></span>
            					</h4>
							</th>
							<th colspan="2">
								<span class="badge <?php echo $this->artcnts['scoded']>0 ?'badge-pink' : ''; ?>"><?php echo Text::_('XB_TOTAL').' '. $this->artcnts['scoded']; ?></span> 
							</th>
						</tr>
						<tr>
							<td colspan="2"><?php echo Text::_('Distinct shortcodes used in articles'); ?></td>
							<td colspan="2"><span class="badge <?php echo $this->scodecnts['uniquescs']>0 ?'badge-orange' : ''; ?>"><?php echo $this->scodecnts['uniquescs']; ?></span></td>
						</tr>
						</thead>
					</table>
				</div>
          	</div>
          	
			<div id="xbinfo" class="xbwp40 pull-left" style="max-width:400px;">
		        	<?php echo HTMLHelper::_('bootstrap.startAccordion', 'slide-dashboard', array('active' => 'sysinfo')); ?>
	        		<?php echo HTMLHelper::_('bootstrap.addSlide', 'slide-dashboard', Text::_('XBARTMAN_SYSINFO'), 'sysinfo','xbaccordion'); ?>
            			<p><b><?php echo Text::_( 'XBARTMAN_COMPONENT' ); ?></b>
    						<br /><?php echo Text::_('XB_VERSION').': <b>'.$this->xmldata['version'].'</b> '.
    							$this->xmldata['creationDate'];?>
                      	</p>
                        <hr />
                      	<p><b><?php echo Text::_( 'XB_CLIENT'); ?></b>
    						<br/><?php echo Text::_( 'XB_PLATFORM' ).' '.$this->client['platform'].'<br/>'.Text::_( 'XB_BROWSER').' '.$this->client['browser']; ?>
                     	</p>
    				<?php echo HtmlHelper::_('bootstrap.endSlide'); ?>
	        		<?php echo HTMLHelper::_('bootstrap.addSlide', 'slide-dashboard', Text::_('XB_KEY_CONFIG_OPTIONS'), 'keyconfig','xbaccordion'); ?>
	        			<p>Config (Options) Key Settings:
	        			</p>
	        			<ul>
	        			<li>list here</li>
	        			</ul>
        			<?php echo HTMLHelper::_('bootstrap.endSlide'); ?>
    				<?php echo HtmlHelper::_('bootstrap.addSlide', 'slide-dashboard', Text::_('XB_ABOUT'), 'about','xbaccordion'); ?>
						<p><?php echo Text::_( 'XB_ABOUT_INFO' ); ?></p>
					<?php echo HtmlHelper::_('bootstrap.endSlide'); ?>
					<?php echo HtmlHelper::_('bootstrap.addSlide', 'slide-dashboard', Text::_('XB_LICENCE'), 'license','xbaccordion'); ?>
						<p><?php echo Text::_( 'XB_LICENSE_GPL' ); ?>
							<br><?php echo Text::sprintf('XB_LICENSE_INFO','xbJournals');?>
							<br /><?php echo $this->xmldata['copyright']; ?>
						</p>		        		
        			<?php echo HTMLHelper::_('bootstrap.endSlide'); ?>
	        		<?php echo HTMLHelper::_('bootstrap.addSlide', 'slide-dashboard', Text::_('XB_REGINFO'), 'reginfo','xbaccordion'); ?>
                        <?php  if (XbarticlemanHelper::penPont()) {
                            echo Text::_('XB_BEER_THANKS'); 
                        } else {
                            echo Text::_('XB_BEER_LINK');
                        }?>
        			<?php echo HTMLHelper::_('bootstrap.endSlide'); ?>
					<?php echo HTMLHelper::_('bootstrap.endAccordion'); ?>
			</div>
			<div class="clearfix"></div>
		</div>	
	</div>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo HTMLHelper::_('form.token'); ?>
</div>
</form>

<div class="clearfix"></div>
<?php echo XbarticlemanHelper::credit('xbArticleMan');?>
