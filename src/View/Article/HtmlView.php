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

namespace TemPlaza\Component\TZ_Portfolio\AddOn\Content\Form_builder\Site\View\Article;

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\Filesystem\File;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\WebAsset\WebAssetManager;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\TZ_PortfolioUri;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\ArticleHelper;

/**
 * Categories view class for the Category package.
 */
class HtmlView extends BaseHtmlView
{
    protected $item     = null;
    protected $params   = null;
    protected $head     = array();
    protected $form_builder          = null;

    public function display($tpl = null){
        $model = $this->getModel();
        $this -> items          = $model -> getForm();
        $this -> params         = $model -> getState('params');
        $this->form_builder  =   json_decode($this -> items-> value);
        $this->styleInit($this->form_builder);

        parent::display($tpl);
    }

    protected function styleInit($item) {
        $addon_id = '#tz-portfolio-plus-form_builder';
        $title_margin_top = (isset($item->title_margin_top) && $item->title_margin_top) ? $item->title_margin_top : '';
        $title_margin_bottom	= (isset($item->title_margin_bottom) && $item->title_margin_bottom) ? $item->title_margin_bottom : '';
        $title_color	= (isset($item->title_color) && $item->title_color) ? $item->title_color : '';
        //Css start
        $css = '';

        $title_style    =   '';
        if (isset($item->title_font) && $item->title_font) {
            $title_style     .=      ArticleHelper::font_style($item->title_font);
        }
        if ($title_margin_top) {
            $title_style    .=  'margin-top:'.$title_margin_top.'px !important;';
        }
        if ($title_margin_bottom) {
            $title_style    .=  'margin-bottom:'.$title_margin_bottom.'px !important;';
        }
        if ($title_color) {
            $title_style    .=  'color:'.$title_color.';';
        }

        if($title_style) {
            $css .= $addon_id . ' .tz-form_builder-title {';
            $css .= $title_style;
            $css .= '}';
        }

        if(isset($item->title_color_hover) && $item->title_color_hover) {
            $css .= $addon_id . ' .tz-form_builder-title{';
            $css .= 'transition:.3s;';
            $css .='}';
            $css .= $addon_id . ':hover .tz-form_builder-title {';
            $css .= 'color:'.$item->title_color_hover.';';
            $css .='}';
        };

        $addon_margin = '';
        $addon_margin_sm = "";
        $addon_margin_xs = "";
        if (isset($item->form_builder_margin) && trim($item->form_builder_margin)) {
            $margin             =   ArticleHelper::padding_margin($item->form_builder_margin, 'margin');
            if ($margin) {
                $addon_margin       .=  $margin->md;
                $addon_margin_sm    .=  $margin->sm;
                $addon_margin_xs    .=  $margin->xs;
                if ($addon_margin) {
                    $css .= $addon_id . '{';
                    $css .= $addon_margin;
                    $css .='}';
                }

                if ($addon_margin_sm) {
                    $css .= '@media (min-width: 768px) and (max-width: 991px) {';
                    $css .= $addon_id . '{';
                    $css .= $addon_margin_sm;
                    $css .='}';
                    $css .='}';
                }

                if ($addon_margin_xs) {
                    $css .= '@media (max-width: 767px) {';
                    $css .= $addon_id . '{';
                    $css .= $addon_margin_xs;
                    $css .='}';
                    $css .='}';
                }
            }
        }

        $button_padding = '';
        $button_padding_sm = '';
        $button_padding_xs = '';

        if (isset($item->form_builder_submit_padding) && trim($item->form_builder_submit_padding)) {
            $padding      =   ArticleHelper::padding_margin($item->form_builder_submit_padding, 'padding');
            if ($padding) {
                $button_padding       .=  $padding->md;
                $button_padding_sm    .=  $padding->sm;
                $button_padding_xs    .=  $padding->xs;
                if ($button_padding) {
                    $css .= $addon_id . ' .tzportfolio-form-builer-submit {';
                    $css .= $button_padding;
                    $css .='}';
                }

                if ($button_padding_sm) {
                    $css .= '@media (min-width: 768px) and (max-width: 991px) {';
                    $css .= $addon_id . ' .tzportfolio-form-builer-submit {';
                    $css .= $button_padding_sm;
                    $css .='}';
                    $css .='}';
                }

                if ($button_padding_xs) {
                    $css .= '@media (max-width: 767px) {';
                    $css .= $addon_id . ' .tzportfolio-form-builer-submit {';
                    $css .= $button_padding_xs;
                    $css .='}';
                    $css .='}';
                }
            }
        }
        $doc = Factory::getApplication()->getDocument()->getWebAssetManager();
        $doc->registerAndUseStyle('tzportfolio.content.form_builder', 'components/com_tz_portfolio/add-ons/content/form_builder/css/style.css');
        if ($css) {
            $doc->addInlineStyle($css);
        }
        $doc->registerAndUseScript('tzportfolio.content.form_builder', 'components/com_tz_portfolio/add-ons/content/form_builder/js/script.js');

    }
}
