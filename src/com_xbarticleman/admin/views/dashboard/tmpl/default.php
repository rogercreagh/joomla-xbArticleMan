<?php
/*******
 * @package xbArticleMan
 * @filesource admin/views/dashboard/tmpl/default.php
 * @version 2.1.0.0 18th November 2023
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
		<div class="row-fluid">
        	<div class="span8">
        		<div class="row-fluid">
        			<div class="span12">
    					<div class="xbbox gradmag">
    						<p>
    							<span class="badge badge-info pull-right"><?php echo Text::_('XB_TOTAL').' '. $this->artcnts['']; ?>
    							articles on the site
    							
    							
    							</span> 
    							<b><?php echo Text::_('XB_ARTICLES'); ?></b>
    						</p>
    							published	unpublished
    							archived	in trash
    							X article categories defined, X articles uncategorised, X articles have no category (error)
    							X are tagged, Y 
    							X include images
    							X have links or anchor targets
    							X use shortcodes with plugins to insert content
    					</div>
        			</div>
        		</div>
        		<div class="row-fluid">
        			<div class="span12">
    					<div class="xbbox gradpink">
    						<p>
    							<span class="badge badge-info pull-right"><?php echo Text::_('XB_TOTAL').' '. $this->tagcnts['']; ?>
    							total joomla tags defined across all components
    							
    							</span> 
    							<b><?php echo Text::_('XBARTMAN_TAGS_ARTS'); ?></b>
    						</p>
    							X tags used in X articles
    					</div>           			
        			</div>
        		</div>
        		
        		<div class="row-fluid">
        			<div class="span12">
    					<div class="xbbox gradcyan">
    						<p>
    							<span class="badge badge-info pull-right"><?php echo Text::_('XB_TOTAL').' '. $this->tagcnts['']; ?>
    							total joomla tags defined across all components
    							
    							</span> 
    							<b><?php echo Text::_('XBARTMAN_IMGS_ARTS'); ?></b>
    						</p>
    							X articles have itro/full images
    							X images are embedded in articles
    					</div>           			
        			</div>
        		</div>
        		
        		<div class="row-fluid">
        			<div class="span12">
    					<div class="xbbox gradyellow">
    						<p>
    							<span class="badge badge-info pull-right"><?php echo Text::_('XB_TOTAL').' '. $this->tagcnts['']; ?>
    							total joomla tags defined across all components
    							
    							</span> 
    							<b><?php echo Text::_('XBARTMAN_IMGS_ARTS'); ?></b>
    						</p>
    							X articles have itro/full images
    							X images are embedded in articles
    					</div>           			
        			</div>
        		</div>
        		
        		<div class="row-fluid">
        			<div class="span12">
    					<div class="xbbox gradyellow">
    						<p>
     							<span class="pull-right"><span class="badge badge-info"><?php echo Text::_('XBJOURNALS_TOTAL').' '. $this->notebookStates['total']; ?></span> 
									<?php echo 'from'.' <span class="badge badge-pink">'.$this->notebookStates['calendars'].'</span> '.Text::_('XBJOURNALS_CALENDARS').' '
                                        .'on'.' <span class="badge badge-ltmag">'.$this->notebookStates['servers'].'</span> '.Text::_('XBJOURNALS_SERVERS'); ?></span>
    							<b><?php echo Text::_('XBJOURNALS_NOTEBOOK_ENTRIES'); ?></b>
    						</p>
    						<div class="row-striped">
    							<div class="row-fluid">
    								<div class="span6">
    									<span class="badge badge-yellow xbmr10"><?php echo $this->notebookStates['parents']; ?></span>
    									<?php echo Text::_('have sub-entries'); ?>
    								</div>
    								<div class="span6">
    									<span class="badge badge-ltgreen xbmr10"><?php echo $this->notebookStates['children']; ?></span>
    									<?php echo Text::_('are sub-entries'); ?>
    								</div>
    							</div>
    							<div class="row-fluid">
    								<div class="span6">
    									<span class="badge badge-success xbmr10"><?php echo $this->notebookStates['published']; ?></span>
    									<?php echo Text::_('XBJOURNALS_PUBLISHED'); ?>
    								</div>
    								<div class="span6">
    									<span class="badge <?php echo $this->notebookStates['unpublished']>0 ?'badge-yellow' : ''; ?> xbmr10"><?php echo $this->notebookStates['unpublished']; ?></span>
    									<?php echo Text::_('XBJOURNALS_UNPUBLISHED'); ?>
    								</div>
    							</div>
    							<div class="row-fluid">
    								<div class="span6">
    									<span class="badge <?php echo $this->notebookStates['archived']>0 ?'badge-warning' : ''; ?> xbmr10"><?php echo $this->notebookStates['archived']; ?></span>
    									<?php echo Text::_('XBJOURNALS_ARCHIVED'); ?>
    								</div>
    								<div class="span6">
    									<span class="badge <?php echo $this->notebookStates['trashed']>0 ?'badge-important' : ''; ?> xbmr10"><?php echo $this->notebookStates['trashed']; ?></span>
    									<?php echo Text::_('XBJOURNALS_TRASHED'); ?>
    								</div>
    							</div>
    						</div>
    					</div>
        			</div>
        		</div>
        		<div class="row-fluid">
        			<div class="span12">
    					<div class="xbbox gradgrey">
    						<p>
     							<span class="pull-right"><span class="badge badge-info"><?php echo Text::_('XBJOURNALS_TOTAL').' '. $this->attachmentCounts['total']; ?></span> 
									<?php echo 'from'.' <span class="badge badge-pink">'.$this->attachmentCounts['calendars'].'</span> '.Text::_('XBJOURNALS_CALENDARS').' '
                                        .'on'.' <span class="badge badge-ltmag">'.$this->attachmentCounts['servers'].'</span> '.Text::_('XBJOURNALS_SERVERS'); ?></span>
    							<b><?php echo Text::_('XBJOURNALS_ATTACHMENTS'); ?></b>
    						</p>
    						<div class="row-striped">
    							<div class="row-fluid">
    								<div class="span6">
    									<span class="badge <?php echo $this->attachmentCounts['journals']>0 ?'badge-success' : ''; ?> xbmr10"><?php echo $this->attachmentCounts['journals']; ?></span>
    									<?php echo Text::_('attached to').' '.Text::_('XBJOURNALS_JOURNAL_ENTRIES'); ?>
    								</div>
    								<div class="span6">
    									<span class="badge <?php echo $this->attachmentCounts['notes']>0 ?'badge-yellow' : ''; ?> xbmr10"><?php echo $this->attachmentCounts['notes']; ?></span>
    									<?php echo Text::_('attached to').' '.Text::_('XBJOURNALS_NOTEBOOK_ENTRIES'); ?>
    								</div>
    							</div>
    							<div class="row-fluid">
    								<div class="span5">
    									<span class="badge <?php echo $this->attachmentCounts['embed']>0 ?'badge-warning' : ''; ?> xbmr10"><?php echo $this->attachmentCounts['embed']; ?></span>
    									<?php echo Text::_('XBJOURNALS_EMBEDDED'); ?>
    								</div>
    								<div class="span7">
    									<span class="badge <?php echo $this->attachmentCounts['remote']>0 ?'badge-important' : ''; ?> xbmr10"><?php echo $this->attachmentCounts['remote']; ?></span>
    									<?php echo Text::_('XBJOURNALS_REMOTE').' '.Text::_('XBJOURNALS_WITH').' '; ?>
    									<span class="badge <?php echo $this->attachmentCounts['rem2local']>0 ?'badge-important' : ''; ?> xbmr10 xbml10"><?php echo $this->attachmentCounts['rem2local']; ?></span>
    									<?php echo Text::_('XBJOURNALS_COPIED_LOCAL'); ?>
    								</div>
    							</div>
    						</div>
    					</div>
        			</div>
        		</div>
    			<div class="row-fluid">
                	<div class="span6">
    					<div class="xbbox gradcat">
    						<p>
    							<span class="badge badge-cat pull-right"><?php echo '0'; //echo Text::_('XBJOURNALS_TOTAL').' '. $this->calendarStates['total']; ?></span> 
    							<a href="index.php?option=com_xbjournals&view=catslist"><?php echo Text::_('XBJOURNALS_CATEGORIES'); ?></a>
    						</p>
            				<div class="row-striped">
            					<div class="row-fluid">
                                  <?php echo Text::_('XBJOURNALS_JOURNAL_CATS').': ';
            						echo '<span class="badge badge-ltgreen pull-right">'.$this->cats['journals'].'</span>'; ?>
                                </div>  
                                <div class="row-fluid">
                                  <?php echo Text::_('XBJOURNALS_NOTE_CATS').': ';
            						echo '<span class="badge badge-ltgreen pull-right">'.$this->cats['notes'].'</span>'; ?>
                                </div>  
                             </div>
    					</div>            			
                	</div>
                	<div class="span6">
            			<div class="xbbox gradtag">
            				<p>
            					<span class="badge badge-tag pull-right"><?php echo '0' ; ?></span> 
            					<a href="index.php?option=com_xbjournals&view=tagslist"><?php echo Text::_('XBJOURNALS_TAGS'); ?></a>
            				</p>
            				<div class="row-striped">
            					<div class="row-fluid">
                                  <?php echo Text::_('XBJOURNALS_JOURNAL_TAGS').': ';
            						echo '<span class="badge badge-ltblue  pull-right">'.$this->tags['journals'].'</span>'; ?>
                                </div>  
                                <div class="row-fluid">
                                  <?php echo Text::_('XBJOURNALS_NOTE_TAGS').': ';
            						echo '<span class="badge badge-ltblue pull-right">'.$this->tags['notes'].'</span>'; ?>
                                </div>  
    						</div>
    	        		</div>
                	</div>
                </div>
          	</div>
			<div id="xbinfo" class="span4">
				<div class="row-fluid">
		        	<?php echo HTMLHelper::_('bootstrap.startAccordion', 'slide-dashboard', array('active' => 'sysinfo')); ?>
	        		<?php echo HTMLHelper::_('bootstrap.addSlide', 'slide-dashboard', Text::_('XBJOURNALS_SYSINFO'), 'sysinfo','xbaccordion'); ?>
            			<p><b><?php echo Text::_( 'XBJOURNALS_COMPONENT' ); ?></b>
    						<br /><?php echo Text::_('XBJOURNALS_VERSION').': <b>'.$this->xmldata['version'].'</b> '.
    							$this->xmldata['creationDate'];?>
                      	</p>
                        <hr />
                      	<p><b><?php echo Text::_( 'XBJOURNALS_CLIENT'); ?></b>
    						<br/><?php echo Text::_( 'XBJOURNALS_PLATFORM' ).' '.$this->client['platform'].'<br/>'.Text::_( 'XBJOURNALS_BROWSER').' '.$this->client['browser']; ?>
                     	</p>
    				<?php echo HtmlHelper::_('bootstrap.endSlide'); ?>
	        		<?php echo HTMLHelper::_('bootstrap.addSlide', 'slide-dashboard', Text::_('XBJOURNALS_KEY_CONFIG'), 'keyconfig','xbaccordion'); ?>
	        			<p>Config (Options) Key Settings:
	        			</p>
	        			<ul>
	        				<li><?php echo Text::_('XBJOURNALS_CONFIG_DEL_UNINST_LABEL'); ?>: <?php echo ($this->savedata)?  Text::_('JYES') :  Text::_('JNO'); ?>
	        				</li>
	        				<li><?php echo Text::_('Save all attachments'); ?>:<?php echo ($this->copy_remote)?  Text::_('JYES') :  Text::_('XBJOURNALS_EMBED_ONLY'); ?>
	        				</li>
	        				<li><?php echo Text::_('XBJOURNALS_ATTACH_FOLDER'); ?>:<br />
	        					<code><?php echo ($this->attach_path); ?></code>
	        				</li>
	        			</ul>
        			<?php echo HTMLHelper::_('bootstrap.endSlide'); ?>
    				<?php echo HtmlHelper::_('bootstrap.addSlide', 'slide-dashboard', Text::_('XBJOURNALS_ABOUT'), 'about','xbaccordion'); ?>
						<p><?php echo Text::_( 'XBJOURNALS_ABOUT_INFO' ); ?></p>
					<?php echo HtmlHelper::_('bootstrap.endSlide'); ?>
					<?php echo HtmlHelper::_('bootstrap.addSlide', 'slide-dashboard', Text::_('XBJOURNALS_LICENCE'), 'license','xbaccordion'); ?>
						<p><?php echo Text::_( 'XBJOURNALS_LICENSE_GPL' ); ?>
							<br><?php echo Text::sprintf('XBJOURNALS_LICENSE_INFO','xbJournals');?>
							<br /><?php echo $this->xmldata['copyright']; ?>
						</p>		        		
        			<?php echo HTMLHelper::_('bootstrap.endSlide'); ?>
	        		<?php echo HTMLHelper::_('bootstrap.addSlide', 'slide-dashboard', Text::_('XBJOURNALS_REGINFO'), 'reginfo','xbaccordion'); ?>
                        <?php  if (XbjournalsHelper::penPont()) {
                            echo Text::_('XBJOURNALS_BEER_THANKS'); 
                        } else {
                            echo Text::_('XBJOURNALS_BEER_LINK');
                        }?>
        			<?php echo HTMLHelper::_('bootstrap.endSlide'); ?>
					<?php echo HTMLHelper::_('bootstrap.endAccordion'); ?>
				</div>		
			</div>
		</div>	
	</div>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo HTMLHelper::_('form.token'); ?>
</div>
</form>

<div class="clearfix"></div>
<?php echo XbarticlemanHelper::credit('xbArticleMan');?>
