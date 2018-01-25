<?php
/**
 * @package Helix3 Framework
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2015 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined ('_JEXEC') or die ('resticted aceess');

class HelixUltimateMenu
{
    protected $_items = array();

    protected $active = 0;

    protected $active_tree = array();

    protected $menu = '';

    public $_params = null;

    public $menuname = 'mainmenu';

    function __construct($class = '', $name = '')
    {
        $this->app = JFactory::getApplication();
        $this->template = $this->app->getTemplate(true);
        $this->_params = $this->template->params;
        $this->extraclass = $class;
        if($name)
        {
            $this->menuname = $name;
        }
        else
        {
            $this->menuname = $this->_params->get('menu');
        }

        $this->initMenu();
        $this->render();
    }

    public function initMenu()
    {
        $menu  	= $this->app->getMenu('site');

        $attributes 	= array('menutype');
        $menu_name     	= array($this->menuname);
        $items 			= $menu->getItems($attributes, $menu_name);
        $active_item 	= ($menu->getActive()) ? $menu->getActive() : $menu->getDefault();

        $this->active   	= $active_item ? $active_item->id : 0;
        $this->active_tree 	= $active_item->tree;

        foreach ( $items as &$item )
        {
            if($item->level >= 2 && !isset($this->_items[$item->parent_id]))
            {
                continue;
            }

            $parent                           = isset($this->children[$item->parent_id]) ? $this->children[$item->parent_id] : array();
            $parent[]                         = $item;
            $this->children[$item->parent_id] = $parent;
            $this->_items[$item->id]          = $item;
        }

        foreach ($items as &$item)
        {
            $class = '';
            if ($item->id == $this->active)
            {
                $class .= ' current-item';
            }

            if (in_array($item->id, $this->active_tree))
            {
                $class .= ' active';
            }
            elseif ($item->type == 'alias')
            {
                $aliasToId = $item->params->get('aliasoptions');
                if (count($this->active_tree) > 0 && $aliasToId == $this->active_tree[count($this->active_tree) - 1])
                {
                    $class .= ' active';
                }
                elseif (in_array($aliasToId, $this->active_tree))
                {
                    $class .= ' alias-parent-active';
                }
            }

            $item->class   = $class;
            $item->dropdown =0;
            if (isset($this->children[$item->id]))
            {
                $item->dropdown = 1;
            }
            $item->megamenu = ($item->params->get('megamenu')) ? $item->params->get('megamenu') : 0;
            $item->flink 		= $item->link;

            switch ($item->type) {
                case 'separator':
                case 'heading':
                    continue;

                case 'url':
                    if ((strpos($item->link, 'index.php?') === 0) && (strpos($item->link, 'Itemid=') === false))
                    {
                        $item->flink = $item->link . '&Itemid=' . $item->id;
                    }
                    break;

                case 'alias':
                    $item->flink = 'index.php?Itemid=' . $item->params->get('aliasoptions');
                    break;

                default:
                    $router = JSite::getRouter();
                    if ($router->getMode() == JROUTER_MODE_SEF)
                    {
                        $item->flink = 'index.php?Itemid=' . $item->id;
                    }
                    else
                    {
                        $item->flink .= '&Itemid=' . $item->id;
                    }
                    break;
            }

            if (strcasecmp(substr($item->flink, 0, 4), 'http') && (strpos($item->flink, 'index.php?') !== false))
            {
                $item->flink = JRoute::_($item->flink, true, $item->params->get('secure'));
            }
            else
            {
                $item->flink = JRoute::_($item->flink);
            }

            $item->title = htmlspecialchars($item->title, ENT_COMPAT, 'UTF-8', false);
            $item->anchor_css   = htmlspecialchars($item->params->get('menu-anchor_css', ''), ENT_COMPAT, 'UTF-8', false);
            $item->anchor_title = htmlspecialchars($item->params->get('menu-anchor_title', ''), ENT_COMPAT, 'UTF-8', false);
            $item->menu_image   = $item->params->get('menu_image', '') ? htmlspecialchars($item->params->get('menu_image', ''), ENT_COMPAT, 'UTF-8', false) : '';
        }
    }

    public function render()
    {
        $this->menu = '';
        $keys = array_keys($this->_items);

        if (count($keys))
        {
            $this->navigation(null,$keys[0]);
        }

        return $this->menu;
    }

    public function navigation($pitem, $start = 0, $end = 0, $class = '')
    {
        if ( $start > 0 )
        {
            if (!isset($this->_items[$start])) return;

            $pid     = $this->_items[$start]->parent_id;
            $items   = array();
            $started = false;

            foreach ($this->children[$pid] as $item)
            {
                if ($started)
                {
                    if ($item->id == $end) break;
                    $items[] = $item;
                }
                else
                {
                    if ($item->id == $start)
                    {
                        $started = true;
                        $items[] = $item;
                    }
                }
            }
            if (!count($items)) return;
        }
        else if ($start === 0)
        {
            $pid = $pitem->id;
            if (!isset($this->children[$pid])) return;
            $items = $this->children[$pid];
        }
        else
        {
            return;
        }

        //Parent class
        if($pid==1) 
        {
            if($this->_params->get('menu_animation') != 'none')
            {
                $animation = ' ' . $this->_params->get('menu_animation');
            }
            else
            {
                $animation = '';
            }
            $class = 'sp-megamenu-parent' . $animation;
            if($this->extraclass) $class = $class . ' ' . $this->extraclass;
            $this->menu .= $this->start_lvl($class);
        }
        else
        {
            $this->menu .= $this->start_lvl($class);
        }

        foreach ($items as $item)
        {
            $this->getItem($item);
        }
        $this->menu .= $this->end_lvl();
    }

    private function getItem($item)
    {
        $this->menu .= $this->start_el(array('item' => $item));
        $this->menu .= $this->item($item);

        $menulayout = json_decode($item->params->get('menulayout'));

        if (isset($menulayout->megamenu) && $menulayout->megamenu)
        {
            $this->mega($item);
        }
        else if ($item->dropdown)
        {
            $this->dropdown( $item );
        }
        $this->menu .= $this->end_el();
    }

    private function dropdown($item)
    {
        $items     = isset($this->children[$item->id]) ? $this->children[$item->id] : array();
        $firstitem = count($items) ? $items[0]->id : 0;
        $class = ($item->level==1) ? 'sp-dropdown sp-dropdown-main' : 'sp-dropdown sp-dropdown-sub';

        $dropdown_width = 240;
        $dropdown_alignment = 'right';
        $dropdown_style = 'width: '. $dropdown_width .'px;';
        $layout = json_decode($this->_items[$item->id]->params->get('menulayout'));

        if (isset($layout->dropdown) && $layout->dropdown == 'left')
        {
            if ($item->parent_id !== '1')
            {
                $dropdown_style .= 'left: -' . $dropdown_width . 'px;';
            }
            $dropdown_alignment = 'left';
        }

        $this->menu .= '<div class="' . $class . ' sp-menu-'. $dropdown_alignment .'" style="' . $dropdown_style . '">';
        $this->menu .= '<div class="sp-dropdown-inner">';
        $this->navigation($item, $firstitem, 0,  'sp-dropdown-items');

        $this->menu .= '</div>';
        $this->menu .= '</div>';
    }

    private function mega($item)
    {
        $items     = isset($this->children[$item->id]) ? $this->children[$item->id] : array();
        $firstitem = count($items) ? $items[0]->id : 0;

        $mega = json_decode($item->params->get('menulayout'));
        $layout = $mega->layout;

        $mega_style = 'width: '. $mega->width .'px;';
        if($mega->menualign=='center')
        {
            $mega_style .= 'left: -' . ($mega->width/2) . 'px;';
        }

        if($mega->menualign == 'full')
        {
            $mega_style = '';
            $mega->menualign = $mega->menualign . ' container';
        }

        $this->menu .='<div class="sp-dropdown sp-dropdown-main sp-dropdown-mega sp-menu-'. $mega->menualign .'" style="' . $mega_style . '">';
        $this->menu .='<div class="sp-dropdown-inner">';
        foreach ($layout as $row)
        {
            $this->menu .='<div class="row">';
            foreach ($row->attr as $col)
            {
                $this->menu .='<div class="col-sm-'.$col->colGrid.'">';

                if (!empty($col->items))
                {
                    $this->menu .= $this->start_lvl('sp-mega-group');

                    foreach ($col->items as $builder_item)
                    {
                        $li_head = '';
                        if($builder_item->type === 'menu_item')
                        {
                            $li_head = 'item-header';
                        }
                        $item_class = array(
                            'item-'.$builder_item->item_id,
                            $builder_item->type,
                            $li_head
                        );

                        $this->menu .= '<li class="'.implode(' ',$item_class).'">';

                        if ($builder_item->type === 'module')
                        {
                            $this->menu .= $this->load_module($builder_item->item_id);
                        }
                        elseif ($builder_item->type === 'menu_item')
                        {
                            if (!empty($this->_items[$builder_item->item_id]))
                            {
                                $item 	= $this->_items[$builder_item->item_id];
                                $items  = isset($this->children[$builder_item->item_id]) ? $this->children[$builder_item->item_id] : array();

                                $firstitem = count($items) ? $items[0]->id : 0;
                                if(isset($this->children[$item->item_id]))
                                {
                                    $this->menu .= $this->item($item, 'sp-group-title');
                                }
                                else
                                {
                                    $this->menu .= $this->item($item);
                                }

                                if ($firstitem)
                                {
                                    $this->navigation(null, $firstitem, 0, 'sp-mega-group-child sp-dropdown-items');
                                }
                            }
                        }
                        $this->menu .= $this->end_el();
                    }
                    $this->menu .= $this->end_lvl();
                }
                $this->menu .='</div>';
            }
            $this->menu .='</div>';
        }
        $this->menu .='</div>';
        $this->menu .='</div>';
    }

    private function start_lvl($cls = '')
    {
        $class = trim($cls);
        return '<ul class="'.$class.'">';
    }

    private function end_lvl()
    {
        return '</ul>';
    }

    private function start_el($args = array())
    {
        $item 	= $args['item'];
        $class 	= 'sp-menu-item';

        $layout = json_decode($item->params->get('menulayout'));

        if (!empty($this->children[$item->id]))
        {
            $class .= ' sp-has-child';
        }
        else if (isset($layout->megamenu) && ($layout->megamenu))
        {
            $class .= ' sp-has-child';
        }

        if (isset($layout->customclass) && ($layout->customclass))
        {
            $class .= ' ' . $layout->customclass;
        }

        $class .= $item->class;
        return '<li class="'.$class.'">';
    }

    private function end_el()
    {
        return '</li>';
    }

    private function item($item, $extra_class='')
    {
        $title = $item->anchor_title ? 'title="' . $item->anchor_title . '" ' : '';

        $class = $extra_class;
        $class .= ($item->anchor_css && $class) ? ' ' . $item->anchor_css : $item->anchor_css;
        $class = ($class) ? 'class="' . $class . '"' : '';

        if ($item->menu_image)
        {
            $item->params->get('menu_text', 1) ?
                $linktitle = '<img src="' . $item->menu_image . '" alt="' . $item->title . '" /><span class="image-title">' . $item->title . '</span> ' :
                $linktitle = '<img src="' . $item->menu_image . '" alt="' . $item->title . '" />';
        }
        else
        {
            $linktitle = $item->title;
        }

        //Hide Link Title
        if(!$showmenutitle = $item->params->get('showmenutitle', 1))
        {
            $linktitle = '';
        }

        //Add Menu Icon
        if($icon = $item->params->get('icon'))
        {
            if($showmenutitle)
            {
                $linktitle = '<i class="fa ' . $icon . '"></i> ' . $linktitle;
            }
            else
            {
                $linktitle = '<i class="fa ' . $icon . '"></i>';
            }
        }
        $flink = $item->flink;
        $flink = str_replace('&amp;', '&', JFilterOutput::ampReplace(htmlspecialchars($flink)));

        $output = '';
        $options ='';
        if ($item->params->get('menu_show', 1) != 0)
        {
            switch ($item->browserNav)
            {
                default:
                case 0:
                    $output .= '<a '.$class.' href="'. $flink .'" '.$title.'>'.$linktitle.'</a>';
                    break;
                case 1:
                    $output .= '<a '. $class .' href="'. $flink .'" target="_blank" '. $title .'>'. $linktitle .'</a>';
                    break;
                case 2:
                    $options .= 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,' . $item->params->get('window_open');
                    $output .= '<a '. $class .' href="'. $flink .'" onclick="window.open(this.href,\'targetWindow\',\''. $options. '\');return false;" '. $title .'>'. $linktitle .'</a>';
                    break;
            }
        }
        return $output;
    }

    private function load_module($mod)
    {
        if (!is_numeric($mod)) return null;

        $groups		= implode(',', JFactory::getUser()->getAuthorisedViewLevels());
        $lang 		= JFactory::getLanguage()->getTag();
        $clientId 	= (int) $this->app->getClientId();

        $db	= JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id, title, module, position, content, showtitle, params');
        $query->from('#__modules AS m');
        $query->where('m.published = 1');
        $query->where('m.id = ' . $mod);

        $date = JFactory::getDate();
        $now = $date->toSql();
        $nullDate = $db->getNullDate();

        $query->where('(m.publish_up = '.$db->Quote($nullDate).' OR m.publish_up <= '.$db->Quote($now).')');
        $query->where('(m.publish_down = '.$db->Quote($nullDate).' OR m.publish_down >= '.$db->Quote($now).')');
        $query->where('m.access IN ('.$groups.')');
        $query->where('m.client_id = '. $clientId);
        
        if ($this->app->isSite() && $this->app->getLanguageFilter())
        {
            $query->where('m.language IN (' . $db->Quote($lang) . ',' . $db->Quote('*') . ')');
        }

        $query->order('position, ordering');
        $db->setQuery($query);
        $module = $db->loadObject();
        if (!$module) return null;

        $options = array('style' => 'sp_xhtml');

        $file				= $module->module;
        $custom				= substr($file, 0, 4) == 'mod_' ?  0 : 1;
        $module->user		= $custom;
        $module->name		= $custom ? $module->title : substr($file, 4);
        $module->style		= null;
        $module->client_id  = 1;
        $module->position	= strtolower($module->position);
        $clean[$module->id]	= $module;
        $output = JModuleHelper::renderModule($module, $options);
        
        return $output;
    }
}
