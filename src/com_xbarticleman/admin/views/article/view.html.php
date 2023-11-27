<?php
/*******
 * @package xbArticleManager
 * file administrator/components/com_xbarticleman/views/article/view.html.php
 * @version 1.0.0.0 22nd January 2019
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2019
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;

class XbarticlemanViewArticle extends HtmlView
{
    protected $form;
    
    protected $item;
    
    protected $state;
    
    protected $canDo;
    
    public function display($tpl = null)
    {        
        
        $this->form  = $this->get('Form');
        $this->item  = $this->get('Item');
        $this->state = $this->get('State');
        $this->canDo = XbarticlemanHelper::getActions();
        
        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            throw new Exception(implode("\n", $errors), 500);
        }
                
        $this->addToolbar();
        
        return parent::display($tpl);
        
    }
    
    protected function addToolbar()
    {
        Factory::getApplication()->input->set('hidemainmenu', true);
        $user       = Factory::getUser();
        $userId     = $user->id;
        $isNew      = ($this->item->id == 0);
        $checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
        
        // Built the actions for new and existing records.
        $canDo = $this->canDo;
        
        ToolbarHelper::title(
            Text::_('XBARTMAN_PAGE_' . ($checkedOut ? 'VIEW_ARTICLE' : 'EDIT_ARTICLE')),
            'pencil-2 article-add'
            );
        
        $itemEditable = $canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by == $userId);
        
        // Can't save the record if it's checked out and editable
        if (!$checkedOut && $itemEditable)
        {
            ToolbarHelper::apply('article.apply');
            ToolbarHelper::save('article.save');          
        }
                
        ToolbarHelper::cancel('article.cancel', 'JTOOLBAR_CLOSE');
                
        ToolbarHelper::divider();
        ToolbarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER_EDIT');
    }
    
}
