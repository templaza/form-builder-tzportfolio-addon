<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2020 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;

use Joomla\Filesystem\File;
use Joomla\Registry\Registry;
use TZ_Portfolio_Plus\Image\TppImageWaterMark;

jimport('joomla.filesytem.file');
JLoader::register('TZ_Portfolio_PlusFrontHelper', JPATH_SITE
    .'/components/com_tz_portfolio_plus/helpers/tz_portfolio_plus.php');

$component_path = JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components';
// Import addon_data model
JLoader::import('com_tz_portfolio_plus.models.addon_data',$component_path);

class TZ_Portfolio_Plus_Addon_Form_BuilderModelForm_Builder extends TZ_Portfolio_PlusModelAddon_Data{

    protected $addon_element    = 'form_builder';

    protected function prepareTable($table){
        parent::prepareTable($table);

        $table -> element   = $this -> addon_element;

        if(!empty($table -> extension_id) && !empty($table -> content_id)){
            // Get addon data id
            if($newtable = $this -> getTable()){
                $newtable -> load(array('content_id' => $table -> content_id, 'extension_id' => $table -> extension_id));
                $table -> set('id', $newtable -> get('id'));
            }
        }
    }

    protected function loadFormData()
    {
        $data   = null;
        $_data  = parent::loadFormData();
        if(!empty($_data)){
            $data   = new stdClass();
            $data -> addon  = new stdClass();
            $data -> addon -> {$this -> addon_element}  = $_data -> value;
        }
        return $data;
    }
}