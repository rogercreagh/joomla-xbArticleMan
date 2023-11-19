<?php
/*******
 * @package xbArticleMan
 * @filesource admin/views/dashboard/tmpl/default.php
 * @version 2.1.0.0 19th November 2023
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
					<div class="pull-right">
						<span class="badge badge-info"><?php echo Text::_('XB_TOTAL').' '. $this->artcnts['total']; ?>
						</span> 
						articles on the site							
					</div>
					<h4>
						<b><?php echo Text::_('XB_ARTICLES'); ?></b>
					</h4>
					<table class="xbwp100">
						<tr>
							<td>published</td>
							<td><span class="badge badge-info"><?php echo $this->artcnts['published']; ?></td>
    					</tr>
    					<tr>
							<td>unpublished</td>
							<td><span class="badge badge-info"><?php echo $this->artcnts['unpublished']; ?> </td>
						</tr>
						<tr>
							<td>archived</td>
							<td><span class="badge badge-info"><?php echo $this->artcnts['archived']; ?></td>
						</tr>
						<tr>
							<td>trashed</td>
							<td><span class="badge badge-info"><?php echo $this->artcnts['trashed']; ?></td>
						</tr>
						<tr>
							<td>article categories defined</td>
							<td><span class="badge badge-info"><?php echo $this->artcnts['catcnt']; ?></td>
						</tr>
						<tr>
							<td>articles uncategorised</td>
							<td><span class="badge badge-info"><?php echo $this->artcnts['uncat']; ?></td>
						</tr>
						<tr>
							<td>articles have no category (error)</td>
							<td><span class="badge badge-info"><?php echo $this->artcnts['nocat']; ?></td>
						</tr>
						<tr>
							<td>articles with 1 or more tags</td>
							<td><span class="badge badge-info"><?php echo $this->artcnts['tagged']; ?></td>
						</tr>
						<tr>
							<td>articles with images in-content</td>
							<td><span class="badge badge-info"><?php echo $this->artcnts['embimaged']; ?></td>
						</tr>
						<tr>
							<td>articles using the related image fields</td>
							<td><span class="badge badge-info"><?php echo $this->imagecnts['related']; ?></span></td>
						</tr>
						<tr>
							<td>articles with links in content</td>
							<td><span class="badge badge-info"><?php echo $this->artcnts['emblinked']; ?></td>
						</tr>
						<tr>
							<td>articles with related links</td>
							<td><span class="badge badge-info"><?php echo $this->artcnts['rellinked']; ?></td>
						</tr>
						<tr>
							<td>articles using plugin shortcodes</td>
							<td><span class="badge badge-info"><?php echo $this->artcnts['scoded']; ?></td>
						</tr>
					</table>
				</div>
				<div class="xbbox gradpink">
					<div class="pull-right">
						<span class="badge badge-info"><?php echo Text::_('XB_TOTAL').' '. $this->tagcnts['totaltags']; ?></span>
						total joomla tags defined across all components    													 
					</div>
					<p>
						<b><?php echo Text::_('XBARTMAN_TAGS_ARTS'); ?></b>
					</p>
					<table class="xbwp100">
						<tr>
							<td>distinct tags used in articles</td>
							<td><span class="badge badge-info pull-right"><?php echo $this->tagcnts['tagsused']; ?></span></td>
						</tr>
					</table>
				</div>           			

				<div class="xbbox gradcyan">
					<div class="pull-right">
						<span class="badge badge-info"><?php echo Text::_('XB_TOTAL').' '. $this->imagecnts['embed']; ?>
						</span> 
						images used in articles    	
					</div>						
					<p>
						<b><?php echo Text::_('Images in articles'); ?></b>
					</p>
				</div>           			

				<div class="xbbox gradyellow">
					<div class="pull-right">
						<span class="badge badge-info"><?php echo Text::_('XB_TOTAL').' '. $this->linkcnts['totLinks']; ?> 
						</span>
						links found in articles
					</div>
					<p>
						<b><?php echo Text::_('Links'); ?></b>
					</p>
					<table class="xbwp100">
						<tr>
							<td>Local Links found in articles</td>
							<td><span class="badge badge-info pull-right"><?php echo $this->linkcnts['localLinks']; ?></span></td>
						</tr>
						<tr>
							<td>External Links found in articles</td>
							<td><span class="badge badge-info pull-right"><?php echo $this->linkcnts['extLinks']; ?></span></td>
						</tr>
						<tr>
							<td>Anchor targets in articles</td>
							<td><span class="badge badge-info pull-right"><?php echo $this->linkcnts['pageTargs']; ?></span></td>
						</tr>
						<tr>
							<td>Other Link types in articles</td>
							<td><span class="badge badge-info pull-right"><?php echo $this->linkcnts['others']; ?></span></td>
						</tr>
					</table>
				</div>

				<div class="xbbox gradgrey">
					<div class="pull-right">
						<span class="badge badge-info"><?php echo Text::_('XB_TOTAL').' '. $this->scodecnts['totalscs']; ?>
						</span>
						shortcodes used in articles
					</div>
					<p>
						<b><?php echo Text::_('Shortcodes'); ?></b>
					</p>
						<tr>
							<td>Distinct types of shortcode used</td>
							<td><span class="badge badge-info pull-right"><?php echo $this->scodecnts['uniquescs']; ?></span></td>
						</tr>
				</div>
          	</div>
			<div id="xbinfo" class="xbwp40 pull-left" style="max-width:400px;">
		        	<?php echo HTMLHelper::_('bootstrap.startAccordion', 'slide-dashboard', array('active' => 'sysinfo')); ?>
	        		<?php echo HTMLHelper::_('bootstrap.addSlide', 'slide-dashboard', Text::_('XB_SYSINFO'), 'sysinfo','xbaccordion'); ?>
            			<p><b><?php echo Text::_( 'XB_COMPONENT' ); ?></b>
    						<br /><?php echo Text::_('XB_VERSION').': <b>'.$this->xmldata['version'].'</b> '.
    							$this->xmldata['creationDate'];?>
                      	</p>
                        <hr />
                      	<p><b><?php echo Text::_( 'XB_CLIENT'); ?></b>
    						<br/><?php echo Text::_( 'XB_PLATFORM' ).' '.$this->client['platform'].'<br/>'.Text::_( 'XB_BROWSER').' '.$this->client['browser']; ?>
                     	</p>
    				<?php echo HtmlHelper::_('bootstrap.endSlide'); ?>
	        		<?php echo HTMLHelper::_('bootstrap.addSlide', 'slide-dashboard', Text::_('XB_KEY_CONFIG'), 'keyconfig','xbaccordion'); ?>
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
