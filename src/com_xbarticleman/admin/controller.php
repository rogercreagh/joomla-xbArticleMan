<?php
/*******
 * @package xbArticleManager
 * file administrator/components/com_xbarticleman/controller.php
 * @version 2.0.1.0 4th November 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2019
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
 defined('_JEXEC') or die();

class XbarticlemanController extends JControllerLegacy {

    protected $default_view = 'arttags';
	
	public function __construct($config = array())
	{	    
	    parent::__construct($config);	    
	}
	
	public function display ($cachable = false, $urlparms = false){
//		require_once JPATH_COMPONENT.'/helpers/xbarticleman.php';
		
		$view 	= $this->input->get('view', 'arttags');
		$layout	= $this->input->get('layout', 'default');
		$id 	= $this->input->getInt('id');
		if ($view == 'article' && $layout == 'edit' && !$this->checkEditId('com_xbarticleman.edit.article', $id))
		{
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_xbarticleman&view=arttags', false));

			return false;
		}

		return parent::display();
	}
}

