<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('JPATH_BASE') or die();

use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Uri\Uri;

$params = $displayData->params;
$attribs = json_decode($displayData->attribs);

$template = HelixUltimate\Framework\Platform\Helper::loadTemplateData();
$tplParams = $template->params;
$og = isset($displayData->enableOpenGraph) ? $displayData->enableOpenGraph : $tplParams->get('og', 0);
$blog_image = $tplParams->get('blog_details_image', 'large');
$full_image = '';

if(isset($attribs->helix_ultimate_image) && $attribs->helix_ultimate_image != '')
{
	if($blog_image == 'default')
	{
		$full_image = $attribs->helix_ultimate_image;
	}
	else
	{
		$full_image = $attribs->helix_ultimate_image;
		$basename = basename($full_image);
		$details_image = JPATH_ROOT . '/' . dirname($full_image) . '/' . File::stripExt($basename) . '_'. $blog_image .'.' . File::getExt($basename);

		if(File::exists($details_image))
		{
			$full_image = Uri::root(true) . '/' . dirname($full_image) . '/' . File::stripExt($basename) . '_'. $blog_image .'.' . File::getExt($basename);
		}
	}
}


?>
<?php if($full_image) : ?>
	<div class="article-full-image">
		<img src="<?php echo $full_image; ?>" alt="<?php echo htmlspecialchars(isset($attribs->helix_ultimate_image_alt_txt) ? $attribs->helix_ultimate_image_alt_txt : $displayData->title, ENT_COMPAT, 'UTF-8'); ?>" itemprop="image">
	</div>
<?php else: ?>
	<?php $images = json_decode($displayData->images); ?>
	<?php if (isset($images->image_fulltext) && !empty($images->image_fulltext)) : ?>
		<?php $imgfloat = empty($images->float_fulltext) ? $params->get('float_fulltext') : $images->float_fulltext; ?>
		<div class="article-full-image float-<?php echo htmlspecialchars($imgfloat); ?>">
			<img <?php if ($images->image_fulltext_caption) :
				echo 'class="caption"' . ' title="' . htmlspecialchars($images->image_fulltext_caption) . '"';
				endif; ?>
				src="<?php echo htmlspecialchars($images->image_fulltext); ?>" alt="<?php echo htmlspecialchars(isset($attribs->helix_ultimate_image_alt_txt) ? $attribs->helix_ultimate_image_alt_txt : $images->image_fulltext_alt); ?>" itemprop="image"> 
		</div>
	<?php endif; ?>
<?php endif; ?>

<?php if($og) : ?>
	<?php echo LayoutHelper::render('joomla.content.open_graph', array('image'=>$full_image, 'title'=>$displayData->title, 'fb_app_id'=>$tplParams->get('og_fb_id'), 'twitter_site'=>$tplParams->get('og_twitter_site'), 'content'=>$displayData->introtext)); ?>
<?php endif; ?>
