<?php
/*******
 * @package xbArticleManager
 * file administrator/components/com_xbarticleman/views/arttags/view.html.php
 * @version 2.0.5.1 13th November 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2019
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
 defined('_JEXEC') or die();

 use Joomla\CMS\Factory;
 use Joomla\CMS\Layout\FileLayout;
 use Joomla\CMS\Toolbar\Toolbar;
 use Joomla\CMS\Toolbar\ToolbarHelper;
 use Joomla\CMS\Language\Text;
 
 class XbarticlemanViewArttags extends JViewLegacy
{
    protected $items;
    protected $pagination;
    protected $state;
    protected $categories;
    protected $alltags;
     
	public $filterForm;

	public $activeFilters;

	protected $sidebar;

	public function display($tpl = null)
	{	    
	    JLoader::register('TagsHelperRoute', JPATH_BASE . '/components/com_tags/helpers/route.php');

	    XbarticlemanHelper::addSubmenu('arttags');

		$this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->state         = $this->get('State');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		$tags = $this->get('Tags');
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

//		$this->alltags = array_count_values(array_column($tags,'title'));
//		$this->alltagsids = array_count_values(array_column($tags,'id'));

		$this->taggedarticles=count(array_count_values(array_column($tags, 'artid')));

		//because we want id, title and cnt we'll have to iterate the tags array rather than using array_count etc
		$this->tagcnts = array();
		foreach ($tags as $k=>$t) {
		    if (!key_exists($t->tagid, $this->tagcnts)) {
		        $this->tagcnts[$t->tagid] =array( 'tagid'=>$t->tagid,'title'=>$t->title,'cnt'=> 0 );
		    }
		    $this->tagcnts[$t->tagid]['cnt'] ++;
		}
		
		$where = 'state IN (1,0)';
		$this->statefilt = 'published and unpublished';
		if (array_key_exists('published', $this->activeFilters)) {
		    $published = $this->activeFilters['published'];
		    if (is_numeric($published)) {
		        $where = 'state = ' . (int) $published;
		        $this->statefilt = array('trashed','','unpublished','published','archived')[$published+2];
		    }
		}
		$this->statearticles = XbarticlemanHelper::getItemCnt('#__content', $where);
		
		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();

		return parent::display($tpl);
	}

	protected function addToolbar()
	{
		$canDo = XbarticlemanHelper::getActions();
		$user  = Factory::getUser();

		// Get the toolbar object instance
		$bar = Toolbar::getInstance('toolbar');

		ToolbarHelper::title(Text::_('XBARTMAN_ADMIN_ARTTAGS_TITLE'), 'stack article');

		if ($canDo->get('core.create') || count($user->getAuthorisedCategories('com_xbarticleman', 'core.create')) > 0)
		{
			ToolbarHelper::addNew('articles.newArticle');
		}

		if ($canDo->get('core.edit') || $canDo->get('core.edit.own'))
		{
		    ToolbarHelper::editList('article.edit','Edit - tags & links');
		    ToolbarHelper::editList('articles.fullEdit','Full Edit');
		}

		if ($canDo->get('core.edit.state'))
		{
			ToolbarHelper::publish('arttags.publish', 'JTOOLBAR_PUBLISH', true);
			ToolbarHelper::unpublish('arttags.unpublish', 'JTOOLBAR_UNPUBLISH', true);
		}

		// Add a batch button
		if ($user->authorise('core.create', 'com_xbarticleman')
			&& $user->authorise('core.edit', 'com_xbarticleman')
			&& $user->authorise('core.edit.state', 'com_xbarticleman'))
		{
			$title = Text::_('JTOOLBAR_BATCH');

			// Instantiate a new JLayoutFile instance and render the batch button
			$layout = new FileLayout('joomla.toolbar.batch');

			$dhtml = $layout->render(array('title' => $title));
			$bar->appendButton('Custom', $dhtml, 'batch');
		}

		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete'))
		{
			ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'arttags.delete', 'JTOOLBAR_EMPTY_TRASH');
		}
		elseif ($canDo->get('core.edit.state'))
		{
			ToolbarHelper::trash('arttags.trash');
		}

		if ($user->authorise('core.admin', 'com_xbarticleman') || $user->authorise('core.options', 'com_xbarticleman'))
		{
			ToolbarHelper::preferences('com_xbarticleman');
		}

		ToolbarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER');
	}

	protected function getSortFields()
	{
		return array(
			'a.ordering'     => JText::_('JGRID_HEADING_ORDERING'),
			'a.state'        => JText::_('JSTATUS'),
			'a.title'        => JText::_('JGLOBAL_TITLE'),
			'category_title' => JText::_('JCATEGORY'),
			'access_level'   => JText::_('JGRID_HEADING_ACCESS'),
			'a.created_by'   => JText::_('JAUTHOR'),
			'a.created'      => JText::_('JDATE'),
			'a.id'           => JText::_('JGRID_HEADING_ID'),
			'a.featured'     => JText::_('JFEATURED')
		);
	}
}
