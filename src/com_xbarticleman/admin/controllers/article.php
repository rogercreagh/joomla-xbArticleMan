<?php
/*******
 * @package xbArticleManager
 * file administrator/components/com_xbarticleman/controllers/article.php
 * @version 2.0.3.3 7th November 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2019
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\MVC\Controller\FormController;

class XbarticlemanControllerArticle extends FormController
{    
    public function __construct($config = array())
    {
        parent::__construct($config);
        
        //article edit view can be called from articles, artlinks, or artimgs. 
        //override default by calling with retview set to the desired view name
        $ret = $this->input->get('retview');
        if ($ret)
        {
            $this->view_list = $ret;
            $this->view_item = 'article&retview='.$ret;
        }      
    }
    
    protected function allowEdit($data = array(), $key = 'id')
    {
        $recordId = (int) isset($data[$key]) ? $data[$key] : 0;
        $user = Factory::getUser();
        // Zero record (id:0), return FALSE as we don't allow new articles here
        if (!$recordId)
        {
            return false;
        }
        
        // Check edit on the record asset (explicit or inherited)
        if ($user->authorise('core.edit', 'com_xbarticleman.article.' . $recordId))
        {
            return true;
        }
        
        // Check edit own on the record asset (explicit or inherited)
        if ($user->authorise('core.edit.own', 'com_xbarticleman.article.' . $recordId))
        {
            // Existing record already has an owner, get it
            $record = $this->getModel()->getItem($recordId);
            
            if (empty($record))
            {
                return false;
            }
            
            // Grant if current user is owner of the record
            return $user->id == $record->created_by;
        }
        
        return false;
    }
    
    public function batch($model = null)
    {
        $this->checkToken();
        
        // Set the model
        $model = $this->getModel('Article', '', array());
        
        // Preset the default redirect
        $this->setRedirect((string)Uri::getInstance());
        
        return parent::batch($model);
    }
        
}