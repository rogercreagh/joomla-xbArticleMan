<?php
/*******
 * @package xbArticleMan Component
 * @filesource admin/views/dashboard/view.html.php
 * @version 2.1.0.0 18th November 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

class XbarticlemanViewDashboard extends JViewLegacy {
    
    public function display($tpl = null) {    
 
        $params = ComponentHelper::getParams('com_xbarticleman');
        
        $this->artcnts = $this->get('ArticleCnts');        
        $this->tagcnts = $this->get('TagCnts');
        $this->imagecnts = $this->get('ImageCnts');
        $this->emblinkcnts = $this->get('EmbLinkCnts');
        $this->rellinkcnts = $this->get('RelLinkCnts');
        $this->scodecnts = $this->get('ScodeCnts');
        
        $this->xmldata = Installer::parseXMLInstallFile(JPATH_COMPONENT_ADMINISTRATOR . '/xbarticleman.xml');
        $this->client = $this->get('Client');
             
        $this->state = $this->get('State');
        
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors), 500);
        }
                
        $this->savedata = $params->get('savedata',0);
        switch ($params->get('extlinkhint', 0)) {
            case 1:
                $this->extlinkhint = Text::_('XBARTMAN_SITE_ADMIN');
                break;
            case 2:
                $this->extlinkhint = Text::_('XBARTMAN_SITE_ONLY');
                break;
            case 3:
                $this->extlinkhint = Text::_('XBARTMAN_ADMIN_ONLY');
                break;
            default:
                $this->extlinkhint = Text::_('XB_DISABLE');
                break;
        }
        
        $this->addToolbar();
        XbarticlemanHelper::addSubmenu('dashboard');
        $this->sidebar = JHtmlSidebar::render();
        
        parent::display($tpl);
        
        $this->setDocument();
        
    }
    
    protected function addToolbar() {
        $canDo = XbarticlemanHelper::getActions();
        
        ToolbarHelper::title(Text::_( 'XBARTMAN_ADMIN_DASHBOARD_TITLE' ), 'info-2' );
        
        if ($canDo->get('core.create') > 0) {
            ToolbarHelper::addNew('server.add','New Server');
        }
        
        if ($canDo->get('core.admin')) {
            ToolbarHelper::preferences('com_xbarticleman');
        }
        ToolbarHelper::help( '', false,'https://crosborne.uk/xbarticleman/doc?tmpl=component#admin-dashboard' );
    }
    
    protected function setDocument() {
        $document = Factory::getDocument();
        $document->setTitle(Text::_('XBARTMAN_ADMIN_DASHBOARD_TITLE'));
    }
    
    
}
