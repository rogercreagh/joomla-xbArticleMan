<?php
/*******
 * @package xbArticleManager
 * file administrator/components/com_xbarticleman/models/artimgs.php
 * @version 2.0.6.0 14th November 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2019
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Table\Table;

class XbarticlemanModelArtimgs extends ListModel
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
                'category_id', 'level', 'artlist'
            );
            
        }
        
        parent::__construct($config);
    }
    
    protected function populateState($ordering = 'a.id', $direction = 'desc')
    {
        $app = Factory::getApplication();
        
        // Adjust the context to support modal layouts.
        if ($layout = $app->input->get('layout'))
        {
            $this->context .= '.' . $layout;
        }
        
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
        $artlist        = $this->getUserStateFromRequest($this->context . '.filter.artlist', 'filter_artlist', '1');
        
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
        }
        
        // List state information.
        parent::populateState($ordering, $direction);
        
    }
    
    protected function getStoreId($id = '')
    {
        // Compile the store id.
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . serialize($this->getState('filter.access'));
        $id .= ':' . $this->getState('filter.published');
        $id .= ':' . serialize($this->getState('filter.category_id'));
        $id .= ':' . serialize($this->getState('filter.author_id'));
        
        return parent::getStoreId($id);
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
                'DISTINCT a.id, a.title, a.alias, a.checked_out, a.checked_out_time, a.catid, a.images' .
                ', a.state, a.access, a.created, a.created_by, a.created_by_alias, a.modified, a.ordering, a.featured, a.language, a.hits' .
                ', a.publish_up, a.publish_down, a.note, a.urls, CONCAT(a.introtext," ",a.fulltext) AS arttext'
                )
            );
        $query->from('#__content AS a');
        
        // list all articles or only tagged or only untagged
        $artlist = $this->getState('filter.artlist');
        switch ($artlist) {
            case 1: //with <img 
                $query->where('CONCAT(a.introtext," ",a.fulltext)'.' REGEXP '.$db->q('<img '));
                break;
            case 2: //with Intro/Full
                $query->where('a.images REGEXP '.$db->q('image_((intro)|(fulltext))\":\"[^,]+\"'));
                break;
            case 3: //with either
                $query->where('CONCAT(a.introtext," ",a.fulltext)'.' REGEXP '.$db->q('<img ').' OR '
                    .'a.images REGEXP '.$db->q('image_((intro)|(fulltext))\":\"[^,]+\"'));
                // {"image_intro":"images\/xbbooks\/samples\/books\/ashes-of-london.jpg","float_intro":"","image_intro_alt":"","image_intro_caption":"","image_fulltext":"images\/xbfilms\/samples\/films\/faces-places.jpg","float_fulltext":"","image_fulltext_alt":"","image_fulltext_caption":""}
                break;
            case 4: //no <img
                $query->where('NOT CONCAT(a.introtext," ",a.fulltext)'.' REGEXP '.$db->q('<img '));
                break;
            case 5: //no Intro/Full
                $query->where('NOT a.images REGEXP '.$db->q('image_((intro)|(fulltext))\":\"[^,]+\"'));
                break;
            case 6: //no images
                $query->where('NOT CONCAT(a.introtext," ",a.fulltext)'.' REGEXP '.$db->q('<img ').' AND NOT '
                    .'a.images REGEXP '.$db->q('image_((intro)|(fulltext))\":\"[^,]+\"'));
                break;
                
            default: //all arrticles
                // do nothing;
            break;
        }
        
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
                $imgtags = XbarticlemanHelper::getDocImgs($item->arttext);
                $item->imgtags = array();
                foreach ($imgtags as $img) {
                    $thisimg = array();
                    $imguri = $img->getAttribute('src');
                    $uri_info = parse_url($imguri);
                    if (key_exists('host', $uri_info)) {
                        $thisimg['host'] = $uri_info['scheme'].'://'.$uri_info['host'];
                        $uri = $imguri;
                    } else {
                        $uri = Uri::root().$imguri;
                        $thisimg['host'] = '';
                    }
                    $thisimg['uri']= $uri;
                    $pathinfo = pathinfo($uri_info['path']);
                    $thisimg['filename']= $pathinfo['basename'];
                    $thisimg['path']= $pathinfo['dirname'].'/';
                    
                    $w = ($img->getAttribute('width')) ? $img->getAttribute('width') : '';
                    $h = ($img->getAttribute('height')) ? $img->getAttribute('height') : '';
                    $specsize = ''; 
                    if ($w != '') {
                        $specsize .= $w;
                        if ($h != '') {
                            $specsize .= ' x '.$h.'px';
                        } else {
                            $specsize .= 'px wide';
                        }
                    } elseif ($h != '') {
                        $specsize .= $h.'px high';
                    }
                    $thisimg['specsize'] = $specsize;                        

                    $thisimg['nativesize'] ='??';
                    $thisimg['mime'] ='??';
                    if (XbarticlemanHelper::check_url($uri)) {
                        $attr = getimagesize($uri);
                        if ($attr !== false) {
                            $thisimg['nativesize'] = $attr[0].' x '.$attr[1].'px';
                            $thisimg['mime'] = $attr['mime'];
                        }
                    }
                    
                    $thisimg['class'] = $img->getAttribute('class');
                    $thisimg['style'] = $img->getAttribute('style');
                    $thisimg['alttext'] = $img->getAttribute('alt');
                    $thisimg['title'] = $img->getAttribute('title');
                    $item->imgtags[] = $thisimg;                    
                }
                
                $intfull = json_decode($item->images);
                $item->introimg = array();
                if ($intfull->image_intro != '') {
                    $imguri = $intfull->image_intro;
                    $uri_info = parse_url($imguri);
                    if (key_exists('hostname', $uri_info)) {
                        $item->introimg['host'] = $uri_info['hostname'];
                    } else {
                        $uri = Uri::root().$imguri;
                        $item->introimg['host'] = '';
                    }
                    $item->introimg['uri']= $uri;
                    $pathinfo = pathinfo($uri);
                    $item->introimg['filename']= $pathinfo['basename'];
                    $item->introimg['path']= $pathinfo['dirname'];
                    $item->introimg['alttext']= $intfull->image_intro_alt;
                    $item->introimg['caption']= $intfull->image_intro_caption;
                    $thisimg['nativesize'] ='??';
                    $thisimg['mime'] ='??';
                    if (XbarticlemanHelper::check_url($uri)) {
                        $attr = getimagesize($uri);
                        if ($attr !== false) {
                            $thisimg['nativesize'] = $attr[0].' x '.$attr[1].'px';
                            $thisimg['mime'] = $attr['mime'];
                        }
                    }
                }
                $item->fullimg = array();
                if ($intfull->image_fulltext != '') {
                    $imguri = $intfull->image_intro;
                    $uri_info = parse_url($imguri);
                    if (key_exists('hostname', $uri_info)) {
                        $item->fullimg['host'] = $uri_info['hostname'];
                    } else {
                        $uri = Uri::root().$imguri;
                        $item->fullimg['host'] = '';
                    }
                    $item->fullimg['uri']= $uri;
                    $pathinfo = pathinfo($uri);
                    $item->fullimg['filename']= $pathinfo['basename'];
                    $item->fullimg['path']= $pathinfo['dirname'];
                    $item->fullimg['alttext']= $intfull->image_fulltext_alt;
                    $item->fullimg['caption']= $intfull->image_fulltext_caption;
                    $thisimg['nativesize'] ='??';
                    $thisimg['mime'] ='??';
                    if (XbarticlemanHelper::check_url($uri)) {
                        $attr = getimagesize($uri);
                        if ($attr !== false) {
                            $thisimg['nativesize'] = $attr[0].' x '.$attr[1].'px';
                            $thisimg['mime'] = $attr['mime'];
                        }
                    }
                }
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
