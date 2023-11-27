<?php
/*******
 * @package xbArticleMan Component
 * @filesource admin/controllers/dashboard.php
 * @version 2.1.0.0 17th November 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;

class XbarticlemanControllerDashboard extends AdminController {
    
    public function getModel($name = 'Dashboard', $prefix = 'XbarticlemanModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config );
        return $model;
    }
    
}
