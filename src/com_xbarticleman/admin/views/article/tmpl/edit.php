<?php
/*******
 * @package xbArticleManager
 * file administrator/components/com_xbarticleman/views/article/tmpl/edit.php
 * @version 2.0.6.1 14th November 2023
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

HTMLHelper::_('behavior.formvalidation');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('formbehavior.chosen', '#jform_catid', null, array('disable_search_threshold' => 0 ));
HTMLHelper::_('formbehavior.chosen', 'select');

$this->configFieldsets  = array('editorConfig');
$this->hiddenFieldsets  = array('basic-limited');
$this->ignore_fieldsets = array('jmetadata', 'item_associations');

// Create shortcut to parameters.
$params = clone $this->state->get('params');
$params->merge(new Registry($this->item->attribs));

$app = Factory::getApplication();
$input = $app->input;

Factory::getDocument()->addScriptDeclaration('
	Joomla.submitbutton = function(task)
	{
		if (task == "article.cancel" || document.formvalidator.isValid(document.getElementById("item-form")))
		{
			jQuery("#permissions-sliders select").attr("disabled", "disabled");
	//		' . $this->form->getField('articletext')->save() . '
			Joomla.submitform(task, document.getElementById("item-form"));
   		}
	};
');

?>

<p><i>Use <a href="
    <?php echo Route::_('index.php?option=com_content&task=article.edit&id='.(int) $this->item->id); ?>"
    class="btn"><?php echo Text::_('XBARTMAN_CONTENT_ART_EDIT'); ?></a> <?php echo Text::_('XBARTMAN_CONTENT_ART_EDIT_NOTE'); ?>. &nbsp;
	To create new file use <a href="
	<?php echo Route::_('index.php?option=com_content&view=article&layout=edit'); ?>" class="btn">
	Content : Add New Article</a>
</i></p>
<hr />
<form action="<?php echo Route::_('index.php?option=com_xbarticleman&layout=edit&id='. (int) $this->item->id); ?>"
	method="post" name="adminForm" id="item-form" class="form-validate" >
	<?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>
	<hr />
	<div class="row-fluid">
		<div class="span9">
			<div class="control-label">
				<?php echo $this->form->getLabel('tags'); ?>
			</div>
			<div class="controls">
				<?php echo $this->form->getInput('tags'); ?>
			</div>
		</div>
	</div>
	<hr />
	<div class="row-fluid">
		<div class="span9">
			<fieldset class="adminform">
				<p><b><?php echo Text::_('XBARTMAN_ARTICLE_FEATURE_IMAGES'); ?></b></p>
				
				<div class="row-fluid">
    				<div class="span6">
                		<?php $cnt = 0; ?>
    					<?php foreach ($this->form->getGroup('images') as $field) : ?>
    						<?php echo $field->renderField(); ?>
    						<?php $cnt ++; 
    						if ($cnt == 4) echo '</div><div class="span6">'; ?>
    					<?php endforeach; ?>
    				</div>
				</div>
				<p><b><?php echo Text::_('XBARTMAN_RELATED_ITEMS_LINKS'); ?></b></p>
                <div class="row-fluid">
                <?php $cnt = 0; ?>
					 <?php foreach ($this->form->getGroup('urls') as $field) : ?>
						<div class="span4">
							<?php echo $field->renderField(); ?>
						</div>
						<?php $cnt ++; 
						if (($cnt % 3 ) == 0) { 
						    if ($cnt < 9) echo '</div><div class="row-fluid"><hr /></div><div class="row-fluid">';							    
						}?>
					<?php endforeach; ?>
				</div>
			</fieldset>
		</div>
		<div class="span3">
				<div class="control-label">
					<?php echo $this->form->getLabel('catid'); ?>
				</div>
				<div class="controls" style="margin-bottom:20px;">
					<?php echo $this->form->getInput('catid'); ?>
				</div>
				<div class="control-label">
					<?php echo $this->form->getLabel('state'); ?>
				</div>
				<div class="controls" style="margin-bottom:20px;">
					<?php echo $this->form->getInput('state'); ?>
				</div>
				<div class="control-label">
					<?php echo $this->form->getLabel('note'); ?>
				</div>
				<div class="controls" style="margin-bottom:20px;">
					<?php echo $this->form->getInput('note'); ?>
				</div>
		</div>
	</div>
	<input type="hidden" name="task" value="" />
	<?php echo HTMLHelper::_('form.token'); ?>
	<input type="hidden" name="retview" value="<?php echo $input->getCmd('retview'); ?>" />

</form>

<div class="clearfix"></div>
<?php echo XbarticlemanHelper::credit('xbArticleMan');?>
	