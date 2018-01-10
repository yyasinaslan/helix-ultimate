<?php
/**
* @package Helix3 Framework
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2017 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/
defined('_JEXEC') or die('Restricted Access');

//

//helper & model
$menu_class   = JPATH_ROOT . '/plugins/system/helix3/core/classes/helix3.php';

if (file_exists($menu_class)) {
    require_once($menu_class);
}

$layout_path  = JPATH_ROOT .'/plugins/system/helixultimate/layouts';

$data = $displayData;

$output ='';

$output .= '<' . $data['sematic'] . ' id="' . $data['id'] . '"' . $data['row_class'] . '>';

if ($data['componentArea']){
    if (!$data['pagebuilder']){
        $output .= '<div class="container">';
    }
}
else{
    if (!$data['fluidrow']){
        $output .= '<div class="container">';
    }
}


$getLayout = new JLayoutFile('frontend.rows', $layout_path );

$output .= $getLayout->render($data);


if ($data['componentArea']){
    if (!$data['pagebuilder']){
        $output .= '</div>';
    }
}
else{
    if (!$data['fluidrow']){
        $output .= '</div>';
    }
}

$output .= '</' . $data['sematic'] . '>';


echo $output;
