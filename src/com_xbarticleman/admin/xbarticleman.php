<?php
/*******
 * @package xbArticleMan
 * file administrator/components/com_xbarticleman/controller.php
 * @version 1.0.9.0 8th March 2023
 * @since 0.1.0.0 22nd January 2019
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2013
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die();

if (!JFactory::getUser()->authorise('core.manage', 'com_xbarticleman')) 
{
    throw new JAccessExceptionNotallowed(JText::_('JERROR_ALERTNOAUTHOR'), 403);
}

JLoader::register('XbarticlemanHelper', __DIR__ . '/helpers/xbarticleman.php');

$controller = JControllerLegacy::getInstance('xbarticleman');

$controller->execute(JFactory::getApplication()->input->get('task'));

$controller->redirect();
