<?php
/*******
 * @package xbArticleManager
 * file administrator/components/com_xbarticleman/views/article/tmpl/edit.php
 * @version 1.0.0.0 22nd January 2019
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2019
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\Registry\Registry;

JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', '#jform_catid', null, array('disable_search_threshold' => 0 ));
JHtml::_('formbehavior.chosen', 'select');

$this->configFieldsets  = array('editorConfig');
$this->hiddenFieldsets  = array('basic-limited');
$this->ignore_fieldsets = array('jmetadata', 'item_associations');

// Create shortcut to parameters.
$params = clone $this->state->get('params');
$params->merge(new Registry($this->item->attribs));

$app = JFactory::getApplication();
$input = $app->input;

JFactory::getDocument()->addScriptDeclaration('
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
    <?php echo JRoute::_('index.php?option=com_content&task=article.edit&id='.(int) $this->item->id); ?>"
    class="btn">Content : Article Edit</a> to switch to full edit view (unsaved changes here will be lost). &nbsp;
	To create new file use <a href="
	<?php echo JRoute::_('index.php?option=com_content&view=article&layout=edit'); ?>" class="btn">
	Content : Add New Article</a>
</i></p>
<hr />
<form action="<?php echo JRoute::_('index.php?option=com_xbarticleman&layout=edit&id='. (int) $this->item->id); ?>"
	method="post" name="adminForm" id="item-form" class="form-validate" >
	<?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>
	<hr />
		<div class="row-fluid">
			<div class="span9">
				<fieldset class="adminform">
					<div class="control-label">
						<?php echo $this->form->getLabel('tags'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('tags'); ?>
					</div>
					<p></br>Links to related items</p>
					<?php foreach ($this->form->getGroup('urls') as $field) : ?>
    					<div class="span3">
    						<?php echo $field->renderField(); ?>
    					</div>
					<?php endforeach; ?>
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
					<div class="controls style="margin-bottom:20px;"">
						<?php echo $this->form->getInput('note'); ?>
					</div>
			</div>
		</div>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
		<input type="hidden" name="retview" value="<?php echo $input->getCmd('retview'); ?>" />

</form>
	