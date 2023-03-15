<?php

/**
 * Created by PhpStorm.
 * User: Ngoc Tu
 * Date: 5/16/2016
 * Time: 11:57 AM
 */

// No direct access
defined('_JEXEC') or die;

class PlgTZ_Portfolio_PlusContentModelForm_Builder extends JModelList
{

    protected function populateState($ordering = null, $direction = null)
    {
        $app = JFactory::getApplication();
        $input = $app->input;

        $this->setState('filter.catid', null);
        $this->setState('filter.content_id', null);
//        $this->setState('list.music_order', 'rdate');

        parent::populateState($ordering, $direction);

    }
    protected function getStoreId($id = '')
    {
        // Add the list state to the store id.
        $id .= ':' . $this->getState('list.start');
        $id .= ':' . $this->getState('list.limit');
        $id .= ':' . $this->getState('filter.content_id');
        $id .= ':' . serialize($this->getState('filter.catid'));
//        $id .= ':' . $this->getState('list.music_order');
        $id .= ':' . $this->getState('list.ordering');
        $id .= ':' . $this->getState('list.direction');

        return md5($this->context . ':' . $id);
    }

    protected function getListQuery()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('DISTINCT d.*');
        $query->from($db->quoteName('#__tz_portfolio_plus_addon_data').' AS d');
//        $query -> join('INNER', '#__tz_portfolio_plus_content AS c ON FIND_IN_SET(c.id, substring_index(substring_index(d.value, '
//            .$db -> quote('"contentid":"').', -1), '.$db -> quote('"').',1)'.')');
        $query -> join('INNER', '#__tz_portfolio_plus_content AS c ON c.id = d.content_id');
        $query ->join('INNER', '#__tz_portfolio_plus_content_category_map AS cm ON cm.contentid = c.id');
        $query ->join('INNER', '#__tz_portfolio_plus_categories AS cc ON cc.id = cm.catid');
        $query -> join('INNER', '#__tz_portfolio_plus_extensions AS e ON e.id = d.extension_id');

        if($addon = TZ_Portfolio_PlusPluginHelper::getPlugin('content', 'form_builder')) {
            $query->where('d.extension_id =' .(int) $addon -> id);
        }
        $query -> where('d.element ='.$db -> quote('form_builder'));

//        $query->where('FIND_IN_SET( ' . $this->getState('filter.contentid')
//            . ', substring_index( substring_index( d.value, '
//            . $db->quote('"contentid":"') . ', -1 ) , '
//            . $db->quote('","') . ', 1 ) ) >0');
        if($content_id = $this -> getState('filter.content_id')){
            $query -> where('d.content_id = '.$content_id);
        }
        $query -> where('d.published = 1');

        if($catid = $this -> getState('filter.catid', null)) {
            if(is_array($catid)){
                $query -> where('cc.id IN('.implode(',', $catid).')');
            }else{
                $query -> where('cc.id = '.(int) $catid);
            }
        }
        return $query;
    }

    public function getItems()
    {
        if ($items = parent::getItems()) {
            $data = array();
            foreach ($items as &$item) {
                if ($item->value && is_string($item->value)) {
                    $value = json_decode($item->value);
//                    if (isset($value->file_names) && $value->file_names) {
//                        $value->file_names = explode('|', $value->file_names);
//                    }
                    $item->value = $value;
                }
            }
            return $items;
        }
        return false;
    }
}