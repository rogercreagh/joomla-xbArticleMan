<?php
/*******
 * @package xbArticleMan Component
 * @filesource admin/models/dashboard.php
 * @version 2.1.0.0 18th November 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;

class XbarticlemanModelDashboard extends JModelList {
    
    protected $arttexts;
    
    public function __construct() {     
        $this->arttexts = $this->getArticlesText();
        parent::__construct();
    }
      
    public function getClient() {
        $result = array();
        $client = Factory::getApplication()->client;
        $class = new ReflectionClass('Joomla\Application\Web\WebClient');
        $constants = array_flip($class->getConstants());
        
        $result['browser'] = $constants[$client->browser].' '.$client->browserVersion;
        $result['platform'] = $constants[$client->platform].($client->mobile ? ' (mobile)' : '');
        $result['mobile'] = $client->mobile;
        return $result;
    }
    
    /**
     * @name getArticleCnts
     * @desc gets count of all articles and states
     * @return array()
     */
    public function getArticleCnts() {
        $artcnts = array('total'=>0, 'published'=>0, 'unpublished'=>0, 'archived'=>0, 'trashed'=>0, 
            'catcnt'=>0, 'uncat'=>0, 'nocat'=>0, 'tagged'=>0, 'embimaged'=>0, 'emblinked'=>0, 'rellinked'=>0, 'scoded'=>0);
        //get states
        $artcnts = array_merge($artcnts,$this->stateCnts());
        //get categories
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('COUNT(a.id) AS catcnt')->from('#__categories AS a')->where('a.extension = '.$db->q('com_content'));
        $query->order('title ASC');
        $db->setQuery($query);
        $artcnts['catcnt'] = $db->loadResult();
        $query->clear();
        $query->select('COUNT(a.id) AS catcnt')
            ->from('#__content AS a')
            ->join('LEFT','#__categories AS c ON a.catid = c.id')
            ->where('c.extension = '.$db->q('com_content').' AND c.alias = '.$db->q('uncategorised'));
        $db->setQuery($query);
        $artcnts['uncat'] = $db->loadResult();
        $query->clear();
        $query->select('COUNT(a.id) AS catcnt from #__content AS a')
            ->where(' a.catid = 0 ');
        $db->setQuery($query);
        $artcnts['nocat'] = $db->loadResult();
            
        //get tagged - articles with tags
        $query->clear();
        $query->select('COUNT(DISTINCT(a.content_item_id)) AS artstagged')
            ->from('#__contentitem_tag_map AS a')
            ->where('a.type_alias = '.$db->q('com_content.article'));
        $db->setQuery($query);
        $res = $db->loadResult();        
        if ($res>0) $artcnts['tagged'] = $res;
        
        //get imgcnts - articles with images by type (rel/embed)
        $query->clear();
        $query->select('COUNT(DISTINCT(a.id)) AS relimged')
        ->from('#__content AS a')
        ->where('a.images REGEXP '.$db->q('image_((intro)|(fulltext))\":\"[^,]+\"'));
        $db->setQuery($query);
        $res = $db->loadResult();
        if ($res>0) $artcnts['relimged'] = $res;
        
        $query->clear();
        $query->select('COUNT(DISTINCT(a.id)) AS embimaged')
            ->from('#__content AS a')
            ->where('CONCAT(a.introtext," ",a.fulltext)'.' REGEXP '.$db->q('<img '));
        $db->setQuery($query);
        $res = $db->loadResult();
        if ($res>0) $artcnts['embimged'] = $res;
        
        //get linkcnts - articles with links by type (art/embed)
        $query->clear();
        $query->select('COUNT(DISTINCT(a.id)) AS emblinked')
            ->from('#__content AS a')
            ->where('CONCAT(a.introtext," ",a.fulltext)'.' REGEXP '.$db->q('<a [^\>]*?href'));
        $db->setQuery($query);
        $res = $db->loadResult();
        if ($res>0) $artcnts['emblinked'] = $res;
        
        $query->clear();
        $query->select('COUNT(DISTINCT(a.id)) AS rellinked')
            ->from('#__content AS a')
            ->where('a.urls REGEXP '.$db->q('/\"url[a-c]\":[^,]+?\"'));
        $db->setQuery($query);
        $res = $db->loadResult();
        if ($res>0) $artcnts['rellinked'] = $res;
        
        //get scode cnts - articles with scodes
        $query->clear();
        $query->select('COUNT(DISTINCT(a.id)) AS embimged')
            ->from('#__content AS a')
            ->where('CONCAT(a.introtext," ",a.fulltext)'.' REGEXP '.$db->q('{[[:alpha:]].+?}'));
        $db->setQuery($query);
        $res = $db->loadResult();
        if ($res>0) $artcnts['scoded'] = $res;
        
        return $artcnts;    
    }
    
    public function getTagCnts() {
        $tagcnts = array('totaltags' =>0, 'tagsused'=>0);
        
        $tagcnts['totaltags'] = XbarticlemanHelper::getItemCnt('#__tags');
        
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        
        $query->select('COUNT(DISTINCT(a.tag_id)) AS tagsused')
            ->from('#__contentitem_tag_map AS a')
            ->where('a.type_alias = '.$db->q('com_content.article'));
        $db->setQuery($query);
        $res = $db->loadResult();
        if ($res>0) $tagcnts['tagsused'] = $res;
        return $tagcnts;
    }
    
    public function getImageCnts() {
        $imgcnts = array('totalimgs'=>0,'embed'=>0, 'related'=>0);
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('COUNT(DISTINCT(a.id)) AS relcnt')
            ->from('#__content AS a')
            ->where('a.images REGEXP '.$db->q('image_((intro)|(fulltext))\":\"[^,]+\"'));
        $db->setQuery($query);
        $res = $db->loadResult();
        if ($res>0) $imgcnts['related'] = $res;
        
        foreach ($this->arttexts as $arttext) {
            $artimgs = XbarticlemanHelper::getDocImgs($arttext);
            $imgcnts['embed'] += count($artimgs);
        }
        $imgcnts['totalimgs']= $imgcnts['embed'] + $imgcnts['related'];
        return $imgcnts;
    }
    
    public function getLinkCnts() {
        $linkcnts = array('totLinks'=> 0, 'pageTargs'=>0, 'localLinks'=>0, 'extLinks'=>0, 'others'=>0);
        foreach ($this->arttexts as $arttext) {
            $artlinks = XbarticlemanHelper::getDocAnchors($arttext);
            foreach ($artlinks as $link) {
                $linkcnts['pageTargs'] += count($artlinks['pageTargs']);
                $linkcnts['localLinks'] += count($artlinks['localLinks']);
                $linkcnts['extLinks'] += count($artlinks['extLinks']);
                $linkcnts['others'] += count($artlinks['others']);
            }
            $tot = array_sum($linkcnts);
            $linkcnts['totLinks'] = $tot;
        }        
        return $linkcnts;
    }

    /**
     * @name getShortcodes
     * @desc returns a count of distinct shortcodes used
     */
    public function getScodeCnts() {
        $scodes = array();
        $sccnts = array('totalscs'=>0, 'uniquescs'=>0);
        foreach ($this->arttexts as $arttext) {
            $artscodes = XbarticlemanHelper::getDocShortcodes($arttext);
            $sccnts['totalscs'] += count($artscodes);
            $scodes = array_unique(array_merge($scodes,array_column($artscodes,1)));
        }
        $sccnts['uniquescs'] = count($scodes);
        return $sccnts;
    }
    
    private function getArticlesText() {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        
        $query->select('CONCAT(a.introtext," ",a.fulltext) AS arttext')
        ->from('#__content AS a');
        $db->setQuery($query);
        $res = $db->loadColumn();
        return $res;
    }
        
    private function stateCnts() {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('DISTINCT a.state, a.id')
            ->from($db->quoteName('#__content').' AS a');
        $col = $db->loadColumn();
        $vals = array_count_values($col);
        $result['total'] = count($col);
        $result['published'] = key_exists('1',$vals) ? $vals['1'] : 0;
        $result['unpublished'] = key_exists('0',$vals) ? $vals['0'] : 0;
        $result['archived'] = key_exists('2',$vals) ? $vals['2'] : 0;
        $result['trashed'] = key_exists('-2',$vals) ? $vals['-2'] : 0;
        return $result;
    }
    
    
}