<?php
/*******
 * @package xbArticleMan
 * file administrator/components/com_xbarticleman/controller.php
 * @version 2.1.0.0 19th November 2023
 * @since 0.1.0.0 22nd January 2019
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2013
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die();

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Uri\Uri;

if (!Factory::getUser()->authorise('core.manage', 'com_xbarticleman')) 
{
//    throw new JAccessExceptionNotallowed(Text::_('JERROR_ALERTNOAUTHOR'), 403);
    Factory::getApplication()->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'),'warning');
    return false;
    
}

$params = ComponentHelper::getParams('com_xbarticleman');
$document = Factory::getDocument();
$cssPath = Uri::root(true)."/media/com_xbarticleman/css/";
$document->addStyleSheet($cssPath.'xbarticleman.css');
if (($params->get('extlinkhint',1) == 1) || ($params->get('extlinkhint') == 3)) {
    $document->addStyleSheet($cssPath. 'xbextlinks.css', array('version'=>'auto'));
}

JLoader::register('XbarticlemanHelper', JPATH_COMPONENT. '/helpers/xbarticleman.php');

$controller = BaseController::getInstance('xbarticleman');

$controller->execute(Factory::getApplication()->input->get('task'));

$controller->redirect();
