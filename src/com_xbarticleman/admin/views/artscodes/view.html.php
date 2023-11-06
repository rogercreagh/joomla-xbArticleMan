<?php
/*******
 * @package xbArticleMan
 * file administrator/components/com_xbarticleman/views/artscodes/view.html.php
 * @version 2.0.0.1 3rd November 2023
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
 
 class XbarticlemanViewArtscodes extends JViewLegacy
{
    protected $items;
    protected $pagination;
    protected $state;
    protected $categories;
    protected $tags;
//    protected $sccnts;
     
	public $filterForm;

	public $activeFilters;

	protected $sidebar;

	public function display($tpl = null) {	    

	    XbarticlemanHelper::addSubmenu('artscodes');

		$this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->state         = $this->get('State');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

        $this->sccnts = array();
		foreach ($this->items as $item) {
		    $item->thiscnts = array_count_values(array_column($item->artscodes,1));		    
		    foreach($item->thiscnts as $key => $value) {
		        if (isset($this->sccnts[$key])) {
		            $this->sccnts[$key] += $value;
		        } else {
		            $this->sccnts[$key] = $value;
		        }
		    }
		}
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

		ToolbarHelper::title(Text::_('XBARTMAN_ADMIN_SHORTCODES_TITLE'), 'wand');

		if ($canDo->get('core.create') || count($user->getAuthorisedCategories('com_xbarticleman', 'core.create')) > 0)
		{
			ToolbarHelper::addNew('articles.newArticle');
		}

		if ($canDo->get('core.edit') || $canDo->get('core.edit.own'))
		{
		    ToolbarHelper::editList('article.edit','Edit Tags Links');
		    ToolbarHelper::editList('articles.fullEdit','Full Edit');
		}

		if ($canDo->get('core.edit.state'))
		{
			ToolbarHelper::publish('articles.publish', 'JTOOLBAR_PUBLISH', true);
			ToolbarHelper::unpublish('articles.unpublish', 'JTOOLBAR_UNPUBLISH', true);
		}

		// Add a batch button
		if ($user->authorise('core.create', 'com_xbarticleman')
			&& $user->authorise('cxbarticleman', 'com_xbarticleman')
			&& $user->authorise('core.edit.state', 'com_xbarticleman'))
		{
			$title = JText::_('JTOOLBAR_BATCH');

			// Instantiate a new JLayoutFile instance and render the batch button
			$layout = new FileLayout('joomla.toolbar.batch');

			$dhtml = $layout->render(array('title' => $title));
			$bar->appendButton('Custom', $dhtml, 'batch');
		}

		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete'))
		{
			ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'articles.delete', 'JTOOLBAR_EMPTY_TRASH');
		}
		elseif ($canDo->get('core.edit.state'))
		{
			ToolbarHelper::trash('articles.trash');
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
