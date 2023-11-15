<?php
/*******
 * @package xbarticleman
 * file administrator/components/com_xbarticleman/views/artlinks/tmpl/default_emb_links.php
 * @version 2.0.6.4 15th November 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2019
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

$links = $this->emblinks;
foreach ($links as $a) : ?>
	<?php $colour = 'blue';
	$url = $a->getAttribute('href');
	$url_info = parse_url($url); 
	if (!key_exists('scheme',$url_info)) $url_info['scheme'] = '';
	if (!key_exists('host',$url_info)) $url_info['host'] = '';
	$local = XbarticlemanHelper::isLocalLink($url);
	if ($local) {
	   if ($this->checkint) {
	       $colour = (!XbarticlemanHelper::check_url($url)) ? 'red' : 'green';
	   }
	   if (!isset($url_info['host'])) $url = Uri::root().$url;
	} else {
	   if ($this->checkext) {
	       $colour = (!XbarticlemanHelper::check_url($url)) ? 'red' : 'green';
	   }									    
	} ?>
    <details>
    	<summary><?php if (key_exists('scheme',$url_info) && ($url_info['scheme'] == 'mailto')) echo '<span class="icon-mail"></span> '; ?>
			<a class="hasTooltip"  data-toggle="modal" title="<?php echo Text::_('XBARTMAN_MODAL_PREVIEW'); ?>" href="#pvModal"
			onClick="window.pvuri=<?php echo "'".$url."'"; ?>" style="color:<?php echo $colour; ?>">
		  	<?php echo $a->textContent; ?> <span class="icon-eye"></span></a>
    	</summary>    							    	
		<i>Host</i>: <?php echo ($local) ? 'local' : $url_info['scheme'].'://'.$url_info['host']; ?><br />
		<i>Path</i>: <?php if (isset($url_info['path'])) echo $url_info['path']; ?>/<br/>
		<?php if (key_exists('fragment',$url_info)) ?> <i>hash</i>: #<?php echo $url_info['fragment']; ?>/<br/>
		<?php if (key_exists('query',$url_info)) ?> <i>Query</i>: <?php echo $url_info['query']; ?>/<br/>
		<?php if ($a->getAttribute('target') != '') ?><i>Target</i>: <?php echo $a->getAttribute('target'); ?>
		<?php if ($a->getAttribute('class') != '') ?><i>Class</i>: <?php echo $a->getAttribute('class'); ?>
    </details>
<?php endforeach; ?>
