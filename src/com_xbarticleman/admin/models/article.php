<?php
/*******
 * @package xbArticleManager
 * file administrator/components/com_xbarticleman/models/article.php
 * @version 2.0.3.3 7th November 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2019
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Table\Table;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\UCM\UCMType;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\String\PunycodeHelper;

class XbarticlemanModelArticle extends AdminModel
{
	/**
	 * The type alias for this type 
	 *
	 * @var    string
	 * @since  3.2
	 */
	public $typeAlias = 'com_xbarticleman.article';

	protected $xbarticle_batch_commands = array(
	    'untag' => 'batchUntag',
	);
	
	protected function batchUntag($value, $pks, $contexts) {
	    $taghelper = new TagsHelper();
	    $message = 'tag:'.$value.' removed from articles :';
//	    $basePath = JPATH_ADMINISTRATOR.'/components/com_content';
//	    require_once $basePath.'/models/article.php';
//	    $articlemodel = new ContentModelArticle(array('table_path' => $basePath . '/tables'));
	    foreach ($pks as $pk) {
	        if ($this->user->authorise('core.edit', $contexts[$pk])) {
	            $existing = $taghelper->getItemTags('com_content.article', $pk, false);
	            $oldtags = array_column($existing,'tag_id');
	            $newtags = array();
	            for ($i = 0; $i<count($oldtags); $i++) {
	                if ($oldtags[$i] != $value) {
	                    $newtags[] = $oldtags[$i];
	                }
	            }
	            $params = array( 'id' => $pk, 'tags' => $newtags );
	            
	            if($this->save($params)){
    	            $message .= ' '.$pk;
	            }
	        } else {
	            $this->setError(Text::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));
	            return false;
	        }
	        Factory::getApplication()->enqueueMessage($message);
	    }
        return true;
	}
	    
	/**
	 * Function that can be overriden to do any data cleanup after batch copying data
	 *
	 * @param   \JTableInterface  $table  The table object containing the newly created item
	 * @param   integer           $newId  The id of the new item
	 * @param   integer           $oldId  The original item id
	 *
	 * @return  void
	 *
	 * @since  3.8.12
	 */
	protected function cleanupPostBatchCopy(\JTableInterface $table, $newId, $oldId)
	{
		// Check if the article was featured and update the #__content_frontpage table
		if ($table->featured == 1)
		{
			$db = $this->getDbo();
			$query = $db->getQuery(true)
				->insert($db->quoteName('#__content_frontpage'))
				->values($newId . ', 0');
			$db->setQuery($query);
			$db->execute();
		}

		// Register FieldsHelper
		JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');

		$oldItem = $this->getTable();
		$oldItem->load($oldId);
		$fields = FieldsHelper::getFields('com_xbarticleman.article', $oldItem, true);

		$fieldsData = array();

		if (!empty($fields))
		{
			$fieldsData['com_fields'] = array();

			foreach ($fields as $field)
			{
				$fieldsData['com_fields'][$field->name] = $field->rawvalue;
			}
		}

		JEventDispatcher::getInstance()->trigger('onContentAfterSave', array('com_xbarticleman.article', &$this->table, true, $fieldsData));
	}

	public function batch($commands, $pks, $contexts)
	{
	    $this->batch_commands = array_merge($this->batch_commands, $this->xbarticle_batch_commands);
	    return parent::batch($commands, $pks, $contexts);
	}
		
	
	/**
	 * Batch move categories to a new category.
	 *
	 * @param   integer  $value     The new category ID.
	 * @param   array    $pks       An array of row IDs.
	 * @param   array    $contexts  An array of item contexts.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   3.8.6
	 */
	protected function batchMove($value, $pks, $contexts)
	{
		if (empty($this->batchSet))
		{
			// Set some needed variables.
			$this->user = Factory::getUser();
			$this->table = $this->getTable();
			$this->tableClassName = get_class($this->table);
			$this->contentType = new UcmType;
			$this->type = $this->contentType->getTypeByTable($this->tableClassName);
		}

		$categoryId = (int) $value;

		if (!$this->checkCategoryId($categoryId))
		{
			return false;
		}

		PluginHelper::importPlugin('system');
		$dispatcher = JEventDispatcher::getInstance();

		// Register FieldsHelper
		JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');

		// Parent exists so we proceed
		foreach ($pks as $pk)
		{
			if (!$this->user->authorise('core.edit', $contexts[$pk]))
			{
				$this->setError(Text::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));

				return false;
			}

			// Check that the row actually exists
			if (!$this->table->load($pk))
			{
				if ($error = $this->table->getError())
				{
					// Fatal error
					$this->setError($error);

					return false;
				}
				else
				{
					// Not fatal error
					$this->setError(Text::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
					continue;
				}
			}

			$fields = FieldsHelper::getFields('com_content.article', $this->table, true);
			$fieldsData = array();

			if (!empty($fields))
			{
				$fieldsData['com_fields'] = array();

				foreach ($fields as $field)
				{
					$fieldsData['com_fields'][$field->name] = $field->rawvalue;
				}
			}

			// Set the new category ID
			$this->table->catid = $categoryId;

			// Check the row.
			if (!$this->table->check())
			{
				$this->setError($this->table->getError());

				return false;
			}

			if (!empty($this->type))
			{
				$this->createTagsHelper($this->tagsObserver, $this->type, $pk, $this->typeAlias, $this->table);
			}

			// Store the row.
			if (!$this->table->store())
			{
				$this->setError($this->table->getError());

				return false;
			}

			// Run event for moved article
			$dispatcher->trigger('onContentAfterSave', array('com_content.article', &$this->table, false, $fieldsData));
		}

		// Clean the cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected function canDelete($record)
	{
		if (!empty($record->id))
		{
			if ($record->state != -2)
			{
				return false;
			}

			return Factory::getUser()->authorise('core.delete', 'com_xbarticleman.article.' . (int) $record->id);
		}

		return false;
	}

	/**
	 * Method to test whether a record can have its state edited.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected function canEditState($record)
	{
		$user = Factory::getUser();

		// Check for existing article.
		if (!empty($record->id))
		{
			return $user->authorise('core.edit.state', 'com_xbarticleman.article.' . (int) $record->id);
		}

		// New article, so check against the category.
		if (!empty($record->catid))
		{
			return $user->authorise('core.edit.state', 'com_xbarticleman.category.' . (int) $record->catid);
		}

		// Default to component settings if neither article nor category known.
		return parent::canEditState($record);
	}

	/**
	 * Prepare and sanitise the table data prior to saving.
	 *
	 * @param   JTable  $table  A JTable object.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function prepareTable($table)
	{
		// Set the publish date to now
		if ($table->state == 1 && (int) $table->publish_up == 0)
		{
			$table->publish_up = Factory::getDate()->toSql();
		}

		if ($table->state == 1 && intval($table->publish_down) == 0)
		{
			$table->publish_down = $this->getDbo()->getNullDate();
		}

		// Increment the content version number.
		$table->version++;

		// Reorder the articles within the category so the new article is first
		if (empty($table->id))
		{
			$table->reorder('catid = ' . (int) $table->catid . ' AND state >= 0');
		}
	}

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable    A database object
	 */
	public function getTable($type = 'Content', $prefix = 'JTable', $config = array())
	{
		return Table::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed  Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{	    
		if ($item = parent::getItem($pk))
		{
			// Convert the params field to an array.
			$registry = new Registry($item->attribs);
			$item->attribs = $registry->toArray();

			// Convert the metadata field to an array.
			$registry = new Registry($item->metadata);
			$item->metadata = $registry->toArray();

			// Convert the images field to an array.
			$registry = new Registry($item->images);
			$item->images = $registry->toArray();

			// Convert the urls field to an array.
			$registry = new Registry($item->urls);
			$item->urls = $registry->toArray();

			$item->articletext = trim($item->fulltext) != '' ? $item->introtext . "<hr id=\"system-readmore\" />" . $item->fulltext : $item->introtext;

			if (!empty($item->id))
			{
				$item->tags = new TagsHelper;
				$item->tags->getTagIds($item->id, 'com_content.article');
			}
		}

		return $item;
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm|boolean  A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_xbarticleman.article', 'article', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		$jinput = Factory::getApplication()->input;

		/*
		 * The front end calls this model and uses a_id to avoid id clashes so we need to check for that first.
		 * The back end uses id so we use that the rest of the time and set it to 0 by default.
		 */
		$id = $jinput->get('a_id', $jinput->get('id', 0));

		// Determine correct permissions to check.
		if ($this->getState('article.id'))
		{
			$id = $this->getState('article.id');

			// Existing record. Can only edit in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.edit');

			// Existing record. Can only edit own articles in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.edit.own');
		}
		else
		{
			// New record. Can only create in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.create');
		}

		$user = Factory::getUser();

		// Check for existing article.
		// Modify the form based on Edit State access controls.
		if ($id != 0 && (!$user->authorise('core.edit.state', 'com_xbarticleman.article.' . (int) $id))
			|| ($id == 0 && !$user->authorise('core.edit.state', 'com_xbarticleman')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('featured', 'disabled', 'true');
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('publish_up', 'disabled', 'true');
			$form->setFieldAttribute('publish_down', 'disabled', 'true');
			$form->setFieldAttribute('state', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is an article you can edit.
			$form->setFieldAttribute('featured', 'filter', 'unset');
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('publish_up', 'filter', 'unset');
			$form->setFieldAttribute('publish_down', 'filter', 'unset');
			$form->setFieldAttribute('state', 'filter', 'unset');
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$app  = Factory::getApplication();
		$data = $app->getUserState('com_xbarticleman.edit.article.data', array());

		if (empty($data))
		{
			$data = $this->getItem();

			// Pre-select some filters (Status, Category, Language, Access) in edit form if those have been selected in Article Manager: Articles
			if ($this->getState('article.id') == 0)
			{
				$filters = (array) $app->getUserState('com_xbarticleman.arttags.filter');
				$data->set(
					'state',
					$app->input->getInt(
						'state',
						((isset($filters['published']) && $filters['published'] !== '') ? $filters['published'] : null)
					)
				);
				$data->set('catid', $app->input->getInt('catid', (!empty($filters['category_id']) ? $filters['category_id'] : null)));
				$data->set('language', $app->input->getString('language', (!empty($filters['language']) ? $filters['language'] : null)));
				$data->set('access',
					$app->input->getInt('access', (!empty($filters['access']) ? $filters['access'] : Factory::getConfig()->get('access')))
				);
			}
		}

		// If there are params fieldsets in the form it will fail with a registry object
		if (isset($data->params) && $data->params instanceof Registry)
		{
			$data->params = $data->params->toArray();
		}

		$this->preprocessData('com_content.article', $data);

		return $data;
	}

	/**
	 * Method to validate the form data.
	 *
	 * @param   JForm   $form   The form to validate against.
	 * @param   array   $data   The data to validate.
	 * @param   string  $group  The name of the field group to validate.
	 *
	 * @return  array|boolean  Array of filtered data if valid, false otherwise.
	 *
	 * @see     JFormRule
	 * @see     JFilterInput
	 * @since   3.7.0
	 */
	public function validate($form, $data, $group = null)
	{
		// Don't allow to change the users if not allowed to access com_users.
		if (Factory::getApplication()->isClient('administrator') && !Factory::getUser()->authorise('core.manage', 'com_users'))
		{
			if (isset($data['created_by']))
			{
				unset($data['created_by']);
			}

			if (isset($data['modified_by']))
			{
				unset($data['modified_by']);
			}
		}

		return parent::validate($form, $data, $group);
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.6
	 */
	public function save($data)
	{
		$input  = Factory::getApplication()->input;
		$filter = InputFilter::getInstance();

		if (isset($data['metadata']) && isset($data['metadata']['author']))
		{
			$data['metadata']['author'] = $filter->clean($data['metadata']['author'], 'TRIM');
		}

		if (isset($data['created_by_alias']))
		{
			$data['created_by_alias'] = $filter->clean($data['created_by_alias'], 'TRIM');
		}

		if (isset($data['images']) && is_array($data['images']))
		{
			$registry = new Registry($data['images']);

			$data['images'] = (string) $registry;
		}

		JLoader::register('CategoriesHelper', JPATH_ADMINISTRATOR . '/components/com_categories/helpers/categories.php');

		// Cast catid to integer for comparison
		$catid = (int) $data['catid'];

		// Check if New Category exists
		if ($catid > 0)
		{
			$catid = CategoriesHelper::validateCategoryId($data['catid'], 'com_content');
		}

		// Save New Category
		if ($catid == 0 && $this->canCreateCategory())
		{
			$table = array();
			$table['title'] = $data['catid'];
			$table['parent_id'] = 1;
			$table['extension'] = 'com_content';
			$table['language'] = $data['language'];
			$table['published'] = 1;

			// Create new category and get catid back
			$data['catid'] = CategoriesHelper::createCategory($table);
		}

		if (isset($data['urls']) && is_array($data['urls']))
		{
			$check = $input->post->get('jform', array(), 'array');

			foreach ($data['urls'] as $i => $url)
			{
				if ($url != false && ($i == 'urla' || $i == 'urlb' || $i == 'urlc'))
				{
					if (preg_match('~^#[a-zA-Z]{1}[a-zA-Z0-9-_:.]*$~', $check['urls'][$i]) == 1)
					{
						$data['urls'][$i] = $check['urls'][$i];
					}
					else
					{
					    $data['urls'][$i] = PunycodeHelper::urlToPunycode($url);
					}
				}
			}

			unset($check);

			$registry = new Registry($data['urls']);

			$data['urls'] = (string) $registry;
		}


		// Automatic handling of alias for empty fields
		if (in_array($input->get('task'), array('apply', 'save', 'save2new')) && (!isset($data['id']) || (int) $data['id'] == 0))
		{
			if ($data['alias'] == null)
			{
				if (Factory::getConfig()->get('unicodeslugs') == 1)
				{
					$data['alias'] = OutputFilter::stringURLUnicodeSlug($data['title']);
				}
				else
				{
					$data['alias'] = OutputFilter::stringURLSafe($data['title']);
				}

				$table = Table::getInstance('Content', 'JTable');

				if ($table->load(array('alias' => $data['alias'], 'catid' => $data['catid'])))
				{
					$msg = Text::_('XBARTMAN_SAVE_WARNING');
				}

				list($title, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['title']);
				$data['alias'] = $alias;

				if (isset($msg))
				{
					Factory::getApplication()->enqueueMessage($msg, 'warning');
				}
			}
		}

		if (parent::save($data))
		{
			if (isset($data['featured']))
			{
				$this->featured($this->getState($this->getName() . '.id'), $data['featured']);
			}

			return true;
		}

		return false;
	}


	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param   object  $table  A record object.
	 *
	 * @return  array  An array of conditions to add to add to ordering queries.
	 *
	 * @since   1.6
	 */
	protected function getReorderConditions($table)
	{
		return array('catid = ' . (int) $table->catid);
	}

	/**
	 * Allows preprocessing of the JForm object.
	 *
	 * @param   JForm   $form   The form object
	 * @param   array   $data   The data to be merged into the form object
	 * @param   string  $group  The plugin group to be executed
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	protected function preprocessForm(Form $form, $data, $group = 'content')
	{
		if ($this->canCreateCategory())
		{
			$form->setFieldAttribute('catid', 'allowAdd', 'true');
		}

		parent::preprocessForm($form, $data, $group);
	}

	/**
	 * Custom clean the cache of com_content and content modules
	 *
	 * @param   string   $group      The cache group
	 * @param   integer  $client_id  The ID of the client
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function cleanCache($group = null, $client_id = 0)
	{
	    parent::cleanCache('com_content');
	    parent::cleanCache('com_xbarticleman');
	    parent::cleanCache('mod_articles_archive');
		parent::cleanCache('mod_articles_categories');
		parent::cleanCache('mod_articles_category');
		parent::cleanCache('mod_articles_latest');
		parent::cleanCache('mod_articles_news');
		parent::cleanCache('mod_articles_popular');
	}

	/**
	 * Is the user allowed to create an on the fly category?
	 *
	 * @return  boolean
	 *
	 * @since   3.6.1
	 */
	private function canCreateCategory()
	{
		return Factory::getUser()->authorise('core.create', 'com_xbarticleman');
	}

	/**
	 * Delete #__content_frontpage items if the deleted articles was featured
	 *
	 * @param   object  $pks  The primary key related to the contents that was deleted.
	 *
	 * @return  boolean
	 *
	 * @since   3.7.0
	 */
	public function delete(&$pks)
	{
		$return = parent::delete($pks);

		if ($return)
		{
			// Now check to see if this articles was featured if so delete it from the #__content_frontpage table
			$db = $this->getDbo();
			$query = $db->getQuery(true)
				->delete($db->quoteName('#__content_frontpage'))
				->where('content_id IN (' . implode(',', $pks) . ')');
			$db->setQuery($query);
			$db->execute();
		}

		return $return;
	}
}
