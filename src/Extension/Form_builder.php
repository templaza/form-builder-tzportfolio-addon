<?php
/*------------------------------------------------------------------------

# TZ Portfolio Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2024 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - https://www.tzportfolio.com/help/forum.html

# Family website: http://www.templaza.com

# Family Support: Forum - https://www.templaza.com/Forums.html

-------------------------------------------------------------------------*/

namespace TemPlaza\Component\TZ_Portfolio\AddOn\Content\Form_builder\Extension;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\AddOn\AddOn;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\Helper\AddonHelper;

defined('_JEXEC') or die;

/**
 * Field Vote Add-On
 */
class Form_builder extends AddOn
{
    public function onAfterDisplayAdditionInfo($context, &$article, $params, $page = 0, $layout = 'default', $module = null)
    {
        list($extension, $vName) = explode('.', $context);
        $item = $article;

        if ($extension == 'module' || $extension == 'modules') {
            if ($path = $this->getModuleLayout($this->_type, $this->_name, $extension, $vName, $layout)) {
                // Display html
                ob_start();
                include $path;
                $html = ob_get_contents();
                ob_end_clean();
                $html = trim($html);
                return $html;
            }
        } elseif (in_array($context, array('com_tz_portfolio.portfolio', 'com_tz_portfolio.date'
        , 'com_tz_portfolio.featured', 'com_tz_portfolio.tags', 'com_tz_portfolio.users'))){
            if($html = $this -> _getViewHtml($context,$item, $params)){
                return $html;
            }
        }
    }

    public function onAddFormToArticleDescription($article = null){
        $position   = $this -> __addFormToPosition($article);
        return $position;
    }
}