<?php
/*******
 * @package xbArticleManager
 * file administrator/components/com_xbarticleman/models/artscodes.php
 * @version 2.0.6.0 14th November 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2019
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Table\Table;

class XbarticlemanModelArtscodes extends JModelList

//poss regex for mysql
//SELECT `id`, `introtext` FROM `n6vbq_content` WHERE `introtext` REGEXP '{(\\w+).*}.*{/(\\1)}';
//alternative to replace th {} with <> and use dom, but what tag to find?
{
    public function __construct($config = array())
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'title', 'a.title',
                'alias', 'a.alias',
                'checked_out', 'a.checked_out',
                'checked_out_time', 'a.checked_out_time',
                'catid', 'a.catid', 'category_title',
                'state', 'a.state',
                'access', 'a.access', 'access_level',
                'created', 'a.created',
                'modified', 'a.modified',
                'created_by', 'a.created_by',
                'created_by_alias', 'a.created_by_alias',
                'ordering', 'a.ordering',
                'publish_up', 'a.publish_up',
                'publish_down', 'a.publish_down',
                'published', 'a.published',
                'author_id',
                'category_id',
                'level', 'scfilt', 'artlist',
            );
            
        }
        
        parent::__construct($config);
    }
    
    protected function populateState($ordering = 'a.id', $direction = 'desc')
    {
        $app = Factory::getApplication();
        
        // Adjust the context to support modal layouts.
//         if ($layout = $app->input->get('layout'))
//         {
//             $this->context .= '.' . $layout;
//         }
        
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);
        
        $published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
        $this->setState('filter.published', $published);
        
        $level = $this->getUserStateFromRequest($this->context . '.filter.level', 'filter_level');
        $this->setState('filter.level', $level);
                
        $formSubmited = $app->input->post->get('form_submited');
        
        $access     = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access');
        $authorId   = $this->getUserStateFromRequest($this->context . '.filter.author_id', 'filter_author_id');
        $categoryId = $this->getUserStateFromRequest($this->context . '.filter.category_id', 'filter_category_id');
        $artlist        = $this->getUserStateFromRequest($this->context . '.filter.artlist', 'filter_artlist', '0');
        $scfilt        = $this->getUserStateFromRequest($this->context . '.filter.scfilt', 'filter_scfilt', '');
        
        if ($formSubmited)
        {
            $access = $app->input->post->get('access');
            $this->setState('filter.access', $access);
            
            $authorId = $app->input->post->get('author_id');
            $this->setState('filter.author_id', $authorId);
            
            $categoryId = $app->input->post->get('category_id');
            $this->setState('filter.category_id', $categoryId);

            $artlist = $app->input->post->get('artlist');
            $this->setState('filter.artlist', $artlist);
            
            $scfilt = $app->input->post->get('scfilt');
            $this->setState('filter.scfilt', $scfilt);
            
        }
        
        // List state information.
        parent::populateState($ordering, $direction);
        
    }
    
    protected function getListQuery()
    {
        // Create a new query object.
        $db    = $this->getDbo();
        $query = $db->getQuery(true);
        $user  = Factory::getUser();
        
        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select',
                'DISTINCT a.id, a.title, a.alias, a.checked_out, a.checked_out_time, a.catid' .
                ', a.state, a.access, a.created, a.created_by, a.created_by_alias, a.modified, a.ordering, a.featured, a.language, a.hits' .
                ', a.publish_up, a.publish_down, a.note, a.urls, CONCAT(a.introtext," ",a.fulltext) AS arttext'
                )
            );
        $query->from('#__content AS a');
        
        // Join over the language
        $query->select('l.title AS language_title, l.image AS language_image')
        ->join('LEFT', $db->quoteName('#__languages') . ' AS l ON l.lang_code = a.language');
        
        // Join over the users for the checked out user.
        $query->select('uc.name AS editor')
        ->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');
        
        // Join over the asset groups.
        $query->select('ag.title AS access_level')
        ->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');
        
        // Join over the categories.
        $query->select('c.title AS category_title, c.created_user_id AS category_uid, c.level AS category_level'.
            ',c.path AS category_path')
        ->join('LEFT', '#__categories AS c ON c.id = a.catid');
        
        // Join over the parent categories.
        $query->select('parent.title AS parent_category_title, parent.id AS parent_category_id,
								parent.created_user_id AS parent_category_uid, parent.level AS parent_category_level')
								->join('LEFT', '#__categories AS parent ON parent.id = c.parent_id');
								
		// Join over the users for the author.
		$query->select('ua.name AS author_name')
		->join('LEFT', '#__users AS ua ON ua.id = a.created_by');
										
		// Filter by access level.
		$access = $this->getState('filter.access');
		
		if (is_numeric($access))
		{
		    $query->where('a.access = ' . (int) $access);
		}
		elseif (is_array($access))
		{
		    $access = ArrayHelper::toInteger($access);
		    $access = implode(',', $access);
		    $query->where('a.access IN (' . $access . ')');
		}
		
		// Filter by access level on categories.
		if (!$user->authorise('core.admin'))
		{
		    $groups = implode(',', $user->getAuthorisedViewLevels());
		    $query->where('a.access IN (' . $groups . ')');
		    $query->where('c.access IN (' . $groups . ')');
		}
		
		// Filter by published state
		$published = $this->getState('filter.published');
		
		if (is_numeric($published))
		{
		    $query->where('a.state = ' . (int) $published);
		}
		elseif ($published === '')
		{
		    $query->where('(a.state = 0 OR a.state = 1)');
		}
		
		// Filter by categories and by level
		$categoryId = $this->getState('filter.category_id', array());
		$level = $this->getState('filter.level');
		
		if (!is_array($categoryId))
		{
		    $categoryId = $categoryId ? array($categoryId) : array();
		}
		
		// Case: Using both categories filter and by level filter
		if (count($categoryId))
		{
		    $categoryId = ArrayHelper::toInteger($categoryId);
		    $categoryTable = Table::getInstance('Category', 'JTable');
		    $subCatItemsWhere = array();
		    
		    foreach ($categoryId as $filter_catid)
		    {
		        $categoryTable->load($filter_catid);
		        $subCatItemsWhere[] = '(' .
						        ($level ? 'c.level <= ' . ((int) $level + (int) $categoryTable->level - 1) . ' AND ' : '') .
						        'c.lft >= ' . (int) $categoryTable->lft . ' AND ' .
						        'c.rgt <= ' . (int) $categoryTable->rgt . ')';
		    }
		    
		    $query->where('(' . implode(' OR ', $subCatItemsWhere) . ')');
		}
		
		// Case: Using only the by level filter
		elseif ($level)
		{
		    $query->where('c.level <= ' . (int) $level);
		}
		
		// Filter by author
		$authorId = $this->getState('filter.author_id');
		
		if (is_numeric($authorId))
		{
		    $type = $this->getState('filter.author_id.include', true) ? '= ' : '<>';
		    $query->where('a.created_by ' . $type . (int) $authorId);
		}
		elseif (is_array($authorId))
		{
		    $authorId = ArrayHelper::toInteger($authorId);
		    $authorId = implode(',', $authorId);
		    $query->where('a.created_by IN (' . $authorId . ')');
		}
		
		// Filter by search in title.
		$search = $this->getState('filter.search');
		
		if (!empty($search))
		{
		    if (stripos($search, 'id:') === 0)
		    {
		        $query->where('a.id = ' . (int) substr($search, 3));
		    }
		    elseif (stripos($search, 'author:') === 0)
		    {
		        $search = $db->quote('%' . $db->escape(substr($search, 7), true) . '%');
		        $query->where('(ua.name LIKE ' . $search . ' OR ua.username LIKE ' . $search . ')');
		    }
		    elseif (stripos($search, 'content:') === 0)
		    {
		        $search = $db->quote('%' . $db->escape(substr($search, 8), true) . '%');
		        $query->where('(a.introtext LIKE ' . $search . ' OR a.fulltext LIKE ' . $search . ')');
		    }
		    else
		    {
		        $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
		        $query->where('(a.title LIKE ' . $search . ' OR a.alias LIKE ' . $search . ' OR a.note LIKE ' . $search . ')');
		    }
		}
										
		// list all articles or only ones with/without shortcodes
		$artlist = $this->getState('filter.artlist');
//		if (($artlist === 0) || ($artlist == '')) { //all - no filter here
		if ($artlist == 1) { //with
		    $query->where('CONCAT(a.introtext," ",a.fulltext)'.' REGEXP '.$db->q('{[[:alpha:]].+?}'));
		} elseif ($artlist == 2) { //without
		    $query->where('CONCAT(a.introtext," ",a.fulltext)'.' NOT REGEXP '.$db->q('{[[:alpha:]].+?}'));
		}
		
		//filter by shortcode
		if ($artlist < 2) {
		    $scfilt = '';
		    $app = Factory::getApplication();
		    //filter by tags
		    $sc = $app->getUserStateFromRequest('sc', 'sc','','STRING');
		    $app->setUserState('sc', '');
		    if (!empty($sc)) {
		        $scfilt = $sc;
		    } else {
		        $scfilt = $this->getState('filter.scfilt','','STRING');
		    }
		    if ($scfilt != '') {
		        $query->where('CONCAT(a.introtext," ",a.fulltext) LIKE '.$db->q('%{'.$scfilt.'%'));
		    }
		}
		
		// Add the list ordering clause.
        $orderCol  = $this->state->get('list.ordering', 'a.id');
        $orderDirn = $this->state->get('list.direction', 'DESC');
        
        if ($orderCol=='a.ordering') {
            $orderCol='category_title '.$orderDirn.', a.ordering';
        }
        
        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

		return $query;
    }
    
    public function getItems() {
        $items  = parent::getItems();
        if ($items) {
            foreach ($items as $item) {
                $item->artscodes = XbarticlemanHelper::getDocShortcodes($item->arttext);
            }
        }
        return $items;
        
    }
    
     public function getAuthors()
    {
        // Create a new query object.
        $db    = $this->getDbo();
        $query = $db->getQuery(true);
        
        // Construct the query
        $query->select('u.id AS value, u.name AS text')
        ->from('#__users AS u')
        ->join('INNER', '#__content AS c ON c.created_by = u.id')
        ->group('u.id, u.name')
        ->order('u.name');
        
        // Setup the query
        $db->setQuery($query);
        
        // Return the result
        return $db->loadObjectList();
    }
}
