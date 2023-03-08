<?php
/*******
 * @package xbArticleManager
 * file script.xbarticleman.php
 * @version 1.0.9.0 8th March 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
// No direct access to this file
defined('_JEXEC') or die;

class com_xbarticlemanInstallerScript 
{
    function preflight($type, $parent)
    {
    }
    
    function install($parent)
    {
        echo '<h3>xbArtMan component installed</h3>';
        echo '<p>Version'.$parent->get('manifest')->version.' '.$parent->get('manifest')->creationDate.'</p>';
        echo '<p>For help and information see <a href="http://crosborne.co.uk/artmandoc" target="_blank">
            www.crosborne.co.uk/artmandoc</a></p>';
    }
    
    function uninstall($parent)
    {
        echo '<p>The xbArtMan component has been uninstalled</p>';
    }
    
    function update($parent)
    {
        echo '<p>The xbArtMan component has been updated to version ' . $parent->get('manifest')->version . '</p>';
        echo '<p>For details see <a href="http://crosborne.co.uk/artman#changelog" target="_blank">
            www.crosborne.co.uk/artman#changelog</a></p>';
    }
    
    function postflight($type, $parent)
    {
        $message = $parent->get('manifest')->name.' version'.$parent->get('manifest')->version.' has been ';
        switch ($type) {
            case 'install': $message .= 'installed'; break;
            case 'uninstall': $message .= 'uninstalled'; break;
            case 'update': $message .= 'updated'; break;
            case 'discover_install': $message .= 'discovered and installed'; break;
        }
        JFactory::getApplication()->enqueueMessage($message);       
    }
}