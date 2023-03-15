<?php
/*------------------------------------------------------------------------

# Music Addon

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2016 tzportfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - http://tzportfolio.com/forum

# Family website: http://www.templaza.com

-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;

class PlgTZ_Portfolio_PlusContentForm_BuilderModelPortfolio extends TZ_Portfolio_PlusPluginModelItem
{
    public function getFeatureItems(){
        if($model  = JModelLegacy::getInstance('Form_Builder','PlgTZ_Portfolio_PlusContentModel',
            array('ignore_request' => true))) {

            $params = $this -> getState('params');
            $model->setState('params', $params);
            $model->setState('filter.content_id', $this->article->id);
            return $model -> getItems();
        }
        return false;
    }
}