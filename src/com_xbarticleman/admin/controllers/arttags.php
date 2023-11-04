<?php
/*******
 * @package xbArticleManager
 * file administrator/components/com_xbarticleman/controllers/arttags.php
 * @version 2.0.1.0 4th November 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2019
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;

class XbarticlemanControllerArttags extends JControllerAdmin
{
	public function __construct($config = array())
	{
		parent::__construct($config);
	}
    /**
     * disallow new article here and redirect to com-content new article form
     */
	public function newArticle() {
	    $this->setRedirect(JRoute::_('index.php?option=com_content&view=article&layout=edit', false));
	}
	
	/**
	 * action for 'FullEdit' button redirects to com_content
	 */
	public function fullEdit() {
	    // Get the input and the first selected id
	    $input = JFactory::getApplication()->input;
	    $pks = $input->post->get('cid', array(), 'array');
	    ArrayHelper::toInteger($pks);
	    $fid = $pks[0];
	    $this->setRedirect(JRoute::_('index.php?option=com_content&&task=article.edit&id='.$fid, false));
	}
	
	public function getModel($name = 'Arttags', $prefix = 'XbarticlemanModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}
}
