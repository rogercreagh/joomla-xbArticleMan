<?php
/*******
 * @package xbarticleman
 * file administrator/components/com_xbarticleman/views/artlinks/tmpl/default_rel_link.php
 * @version 2.0.6.4 15th November 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2019
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

$url = $this->rellink->url;
$colour = 'blue';
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
} 
?>
<details>
	<summary>
		<i><?php echo $this->rellink->label; ?></i>: <?php if ($url_info['scheme'] == 'mailto') echo '<span class="icon-mail"></span> '; ?>
		<a class="hasTooltip"  data-toggle="modal" title="<?php echo Text::_('XBARTMAN_MODAL_PREVIEW'); ?>" href="#pvModal"
			onClick="window.pvuri=<?php echo "'".$url."'"; ?>" style="color:<?php echo $colour; ?>">
		  	<?php echo ($this->rellink->text == '') ? $url : $this->rellink->text; ?> <span class="icon-eye"></span></a>
	</summary>
		<i>Host</i>: <?php echo ($local) ? 'local' : $url_info['scheme'].'://'.$url_info['host']; ?><br />
		<i>Path</i>: <?php if (isset($url_info['path'])) echo $url_info['path']; ?>/<br/>
		<i>Target</i>: <?php echo ($this->rellink->target === '') ? '(use global)' : $targets[$this->rellink->target]; ?>
</details>
