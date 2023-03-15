<?php
/*------------------------------------------------------------------------

# Form_Builder Addon

# ------------------------------------------------------------------------

# author    Sonny

# copyright Copyright (C) 2021 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.tzportfolio.com

# Technical Support:  Forum - https://www.tzportfolio.com/help/forum.html

-------------------------------------------------------------------------*/

// No direct access.
defined('_JEXEC') or die;
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

class PlgTZ_Portfolio_PlusContentForm_BuilderViewArticle extends JViewLegacy{

    protected $item             = null;
    protected $params           = null;
    protected $form_builder          = null;
    protected $head             = false;

    public function display($tpl = null){
        $this -> item   = $this -> get('Item');
        $this -> items   = $this -> get('Form_BuilderItems');
        $state          = $this -> get('State');
        $params         = $state -> get('params');
        $this -> params = $params;
        if(isset($this -> items) && $this -> items) {
            foreach ($this->items as $_item) {
                $this->form_builder  =   $_item -> value;
                $this->styleInit($_item -> value);
            }
        }
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
            $title_style     .=      TZ_Portfolio_PlusContentHelper::font_style($item->title_font);
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
            $margin             =   TZ_Portfolio_PlusContentHelper::padding_margin($item->form_builder_margin, 'margin');
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
            $padding      =   TZ_Portfolio_PlusContentHelper::padding_margin($item->form_builder_submit_padding, 'padding');
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

        $doc = JFactory::getDocument();
        $doc->addStyleSheet('components/com_tz_portfolio_plus/addons/content/form_builder/css/style.css');
        $doc->addStyleDeclaration($css);
        $doc->addScript('components/com_tz_portfolio_plus/addons/content/form_builder/js/script.js');
    }

}