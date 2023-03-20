<?php
/*******
 * @package xbArticleManager
 * file administrator/components/com_xbartman/helpers/xbartman.php
 * @version 1.0.0.0 22nd January 2019
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2019
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
defined('_JEXEC') or die;

class XbarticlemanHelper // extends JHelper
{
	public static $extension = 'com_xbartman';

	public static function getActions($categoryid = 0) {
	    $user 	=JFactory::getUser();
	    $result = new JObject;
	    if (empty($categoryid)) {
	        $assetName = 'com_xbartman';
	        $level = 'component';
	    } else {
	        $assetName = 'com_xbartman.category.'.(int) $categoryid;
	        $level = 'category';
	    }
	    $actions = JAccess::getActions('com_xbartman', $level);
	    foreach ($actions as $action) {
	        $result->set($action->name, $user->authorise($action->name, $assetName));
	    }
	    return $result;
	}
	
	public static function addSubmenu($vName)
	{
		JHtmlSidebar::addEntry(
			JText::_('Articles->tags'),
			'index.php?option=com_xbartman&view=articles',
			$vName == 'articles'
		);
		JHtmlSidebar::addEntry(
		    JText::_('Articles->links'),
		    'index.php?option=com_xbartman&view=artlinks',
		    $vName == 'artlinks'
		    );
		JHtmlSidebar::addEntry(
		    JText::_('Articles->images'),
		    'index.php?option=com_xbartman&view=artimgs',
		    $vName == 'artimgs'
		    );
		JHtmlSidebar::addEntry('<hr /><b>Other Views</b>');
		JHtmlSidebar::addEntry(
		    JText::_('Content : Articles'),
		    'index.php?option=com_content&view=articles',
		    $vName == 'contentarticles'
		    );
		JHtmlSidebar::addEntry(
		    JText::_('Tags : Tags'),
		    'index.php?option=com_tags&view=tags',
		    $vName == 'tagstags'
		    );
	}

    /**
     * getDocAnchors
     * @param unknown $html - html doc text to parse and find anchors 
     * @return array[] - array or arrays of DomNodes for <a ..> tags in doc
     */	
    public static function getDocAnchors($html) {	    
        //container for different types of links
        //pageLinks are links to anchor tags within the doc
        //pageTargs are the anchor target tags in the doc
        //localLinks are links to pages on this site (may be sef or raw, complete or relative)
        //extLinks are links to other websites
        //others are 'mailto: and other services
	    $atags = array("pageLinks"=>array(),
	        "pageTargs"=>array(),
	        "localLinks"=>array(),
	        "extLinks"=>array(),
	        "others"=>array()
	    );
	    
	    $dom = new DOMDocument;
	    $dom->loadHTML($html,LIBXML_NOERROR);
	    $as = $dom->getElementsByTagName('a');
	    foreach ($as as $atag) {
	        $text = $atag->textContent;
	        $href = $atag->getAttribute('href');
	        if (!$href) //no href specified so must be target
	        {
	            array_push($atags["pageTargs"], $atag);
	        } else {
	            if (substr($href,0,1)=='#') { //the href starts with # so target is on same page
	                array_push($atags["pageLinks"], $atag);
	            } else {
	                $arrHref = parse_url($href);
	                if ((isset($arrHref["scheme"])) && (!stristr($arrHref["scheme"],'http'))) {
	                    // scheme is not http or https so it is some other type of link
	                    array_push($atags["others"], $atag);
	                } else {
	                    if (self::isLocalLink($href)) {
	                        array_push($atags["localLinks"], $atag);
	                    } else {
	                        array_push($atags["extLinks"], $atag);
	                    }
	                }
	            }
	        }
	    }
	    return $atags;
	}
	
	public static function check_url($url) {
	    $headers = @get_headers( $url);
	    $headers = (is_array($headers)) ? implode( "\n ", $headers) : $headers;	    
	    return (bool)preg_match('#^HTTP/.*\s+[(200|301|302)]+\s#i', $headers);
	}
	
	public static function isLocalLink($link) {
	    $ret=false;
	    $arrLink = parse_url($link);
	    if (isset($arrLink["host"])) {
	        if (stristr($arrLink["host"],parse_url(JUri::root(),PHP_URL_HOST))) {
	            //the joomla server name is in the host (whatever http/https and subdomain)
	            return true;
	        }
	        return false;
	    }  //no host so assume it is local
	    if (isset($arrLink["path"])) {
	        return true;	    
	    }
	    return false; //we have no host or path WTF; its not local!
	}

	public static function getLinkDisplay($link, $text, $target, $chkint, $chkext) {

    	$tip = 'Text: <b>'.$text.'</b><br />';
    	$tip .= 'Link: '.$link.'<br />';

    	if ($target==1) {
    	    $tip .= '[new tab] ';
    	}
    	$class='';
    	$colour = 'blue';
    	$parsed = parse_url($link);
    	$display =  $link;
    	if (XbarticlemanHelper::isLocalLink($link)) {
    	    $thisServer = parse_url(JUri::root(),PHP_URL_HOST);
    	    if (!(array_key_exists('host',$parsed))) {
    	        $thisPath = parse_url(JUri::root(),PHP_URL_PATH);
    	        $path = $parsed["path"];
    	        if (stristr($path,$thisPath)==false) {
    	            $path = $thisPath.ltrim($path,'/');
    	        }
    	        $link = 'http://'.$thisServer.'/'.ltrim($path,'/');
    	    } else {
    	        $link = 'http://'.$thisServer.'/'.ltrim($parsed["path"],'/');
    	    }
    	    $display = '/'.ltrim($parsed["path"],'/');
    	    if (array_key_exists('query',$parsed)) { //add back the query string
    	        $link .= '?'.$parsed["query"];
    	        //if there is a ? string we are just going to display that
    	        //$display = '?'.$parsed["query"];
    	        $display = ''; //$parsed["query"];
    	        parse_str($parsed["query"], $arrQuery);
    	        if  (array_key_exists('option',$arrQuery) && $arrQuery["option"]=='com_content') {
    	            $display .= 'Content view:';
    	            if  (array_key_exists('view',$arrQuery)) {
    	                $display .= $arrQuery["view"].' ';
    	            }
    	            if  (array_key_exists('id',$arrQuery)) {
    	                $display .= 'id:'.$arrQuery["id"].' ';
    	            }
    	            if  (array_key_exists('catid',$arrQuery)) {
    	                $display .= 'cat:'.$arrQuery["id"].' ';
    	            }
    	        } else {
    	            $display .= $parsed["query"];
    	        }
    	        $link .= '&';
    	    } else {
    	        $link .= '?';
    	    }
    	    $link .= 'tmpl=component';
       	    $targ = '';
    	    $class .=' modal';
    	    $tip .= '[local] ';
    	    if ($chkint) {
    	        $colour = (!XbarticlemanHelper::check_url($link)) ? 'red' : 'green';
    	    }
    	} else {
    	    $targ = 'target="_blank" ';
    	    $display = str_replace('https://','',$display);
    	    $display = str_replace('http://','',$display);
    	    $display = str_replace('www.','',$display);  	    
    	    if ($chkext) {
    	        $colour = (!XbarticlemanHelper::check_url($link)) ? 'red' : 'green';
    	    }
    	}
    	$ret ='';
    	if ($target == 1) $ret .= '[T] ';
    	$ret .= '<a class="hasTooltip'.$class.'" title="'.$tip.'" ';
    	$ret .= 'href="'.$link.'" '.$targ.'><span style="color:'.$colour.';">';
    	$ret .= $display.'</span></a><br />';
    	return $ret;
	}
	
	public static function getDocImgs($html) {
	    $aimgs = array();
	    
	    $dom = new DOMDocument;
	    $dom->loadHTML($html,LIBXML_NOERROR);
	    $as = $dom->getElementsByTagName('img');
	    foreach ($as as $aimg) {
	        array_push($aimgs,$aimg);
	    }
	    return $aimgs;
	}
	
	
	/**
	 * Applies the content tag filters to arbitrary text as per settings for current user group
	 *
	 * @param   text  $text  The string to filter
	 *
	 * @return  string  The filtered string
	 *
	 * @deprecated  4.0  Use JComponentHelper::filterText() instead.
	 */
// 	public static function filterText($text)
// 	{
// 		try
// 		{
// 			JLog::add(
// 				sprintf('%s() is deprecated. Use JComponentHelper::filterText() instead', __METHOD__),
// 				JLog::WARNING,
// 				'deprecated'
// 			);
// 		}
// 		catch (RuntimeException $exception)
// 		{
// 			// Informational log only
// 		}

// 		return JComponentHelper::filterText($text);
// 	}

	/**
	 * Adds Count Items for Category Manager.
	 *
	 * @param   stdClass[]  &$items  The category objects
	 *
	 * @return  stdClass[]
	 *
	 * @since   3.5
	 */
// 	public static function countItems(&$items)
// 	{
// 		$config = (object) array(
// 			'related_tbl'   => 'content',
// 			'state_col'     => 'state',
// 			'group_col'     => 'catid',
// 			'relation_type' => 'category_or_group',
// 		);

// 		return parent::countRelations($items, $config);
// 	}

	/**
	 * Adds Count Items for Tag Manager.
	 *
	 * @param   stdClass[]  &$items     The tag objects
	 * @param   string      $extension  The name of the active view.
	 *
	 * @return  stdClass[]
	 *
	 * @since   3.6
	 */
// 	public static function countTagItems(&$items, $extension)
// 	{
// 		$parts   = explode('.', $extension);
// 		$section = count($parts) > 1 ? $parts[1] : null;

// 		$config = (object) array(
// 			'related_tbl'   => ($section === 'category' ? 'categories' : 'content'),
// 			'state_col'     => ($section === 'category' ? 'published' : 'state'),
// 			'group_col'     => 'tag_id',
// 			'extension'     => $extension,
// 			'relation_type' => 'tag_assigments',
// 		);

// 		return parent::countRelations($items, $config);
// 	}

	/**
	 * Returns a valid section for articles. If it is not valid then null
	 * is returned.
	 *
	 * @param   string  $section  The section to get the mapping for
	 *
	 * @return  string|null  The new section
	 *
	 * @since   3.7.0
	 */
// 	public static function validateSection($section)
// 	{
// 		if (JFactory::getApplication()->isClient('site'))
// 		{
// 			// On the front end we need to map some sections
// 			switch ($section)
// 			{
// 				// Editing an article
// 				case 'form':

// 				// Category list view
// 				case 'featured':
// 				case 'category':
// 					$section = 'article';
// 			}
// 		}

// 		if ($section != 'article')
// 		{
// 			// We don't know other sections
// 			return null;
// 		}

// 		return $section;
// 	}

	/**
	 * Returns valid contexts
	 *
	 * @return  array
	 *
	 * @since   3.7.0
	 */
// 	public static function getContexts()
// 	{
// 		JFactory::getLanguage()->load('com_xbartman', JPATH_ADMINISTRATOR);

// 		$contexts = array(
// 		    'com_xbartman.article'    => JText::_('XBARTMAN'),
// 		    'com_content.categories' => JText::_('JCATEGORY')
// 		);

// 		return $contexts;
// 	}
}
