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

namespace TemPlaza\Component\TZ_Portfolio\AddOn\Content\Form_builder\Administrator\Model;


// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormFactoryInterface;
use Joomla\CMS\Image\Image;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\Filesystem\Path;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\Helper\AddonHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\CategoriesHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\AddOn\AddOnAdminModel;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\TZ_PortfolioHelper as TZ_PortfolioFrontHelper;

class Form_builderModel extends AddOnAdminModel {
}