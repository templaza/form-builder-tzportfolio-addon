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
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
$params = $this->params;
if ($params->get('form_builder_show',1)) {
    if (isset($this->form_builder) && $form_builder = $this->form_builder) {
        if (isset($form_builder->form_builder_items) && is_object($form_builder->form_builder_items))  {
            //Title
            $title = (isset($form_builder->title) && $form_builder->title) ? $form_builder->title : '';
            $heading_selector = (isset($form_builder->title_element) && $form_builder->title_element) ? $form_builder->title_element : 'h3';

            //Custom Class
            $custom_class = (isset($form_builder->custom_class) && trim($form_builder->custom_class)) ? $form_builder->custom_class : '';

            echo '<div id="tz-portfolio-plus-form_builder" class="'.$custom_class.'">';
            if($title) {
                echo '<'.$heading_selector.' class="tz-form_builder-title uk-margin-bottom">';
                echo $title;
                echo '</'.$heading_selector.'>';
            }
            echo '<div class="vendor-container">';

            echo '<div class="row gx-4 align-items-center mb-4">';
            echo '<div class="vendor-avatar col-auto"><img class="rounded-pill" src="'.$form_builder->recipient_image.'" alt="'.$form_builder->recipient_name.'" /></div>';
            echo '<div class="vendor-info col">';
            echo '<h3 class="vendor-name mb-1">'.$form_builder->recipient_name.'</h3>';
            echo '<div class="vendor-description">'.$form_builder->recipient_description.'</div>';
            echo '</div>';
            echo '</div>';

            if (isset($form_builder->recipient_contact_options) && is_object($form_builder->recipient_contact_options)) {
                foreach ($form_builder->recipient_contact_options as $key => $recipient_contact_option) {
                    echo '<div class="recipient_contact_option">';
                    echo '<div class="row gx-3 align-items-center">';
                    echo '<div class="recipient-icon col-auto"><i class="'.$recipient_contact_option->contact_icon.'"></i></div>';
                    echo '<div class="recipient-text col">'.$recipient_contact_option->contact_text.'</div>';
                    echo '</div>';
                    echo '</div>';
                }
            }

            echo '</div>';

            $gutter =   isset($form_builder->gutter) && $form_builder->gutter ? ' '. $form_builder->gutter : '';
            echo '<form class="tzportfolio-form-builder mt-4" method="post">';
            echo '<div class="row'.$gutter.'">';
            foreach ($form_builder->form_builder_items as $key => $form_builder_item) :
                //Options
                $column     =   [];
                $column[]   =   (isset($form_builder_item->column_lg) && $form_builder_item->column_lg) ? 'col-lg-' . 12/$form_builder_item->column_lg : 'col-lg-6';
                $column[]   =   (isset($form_builder_item->column_md) && $form_builder_item->column_md) ? 'col-md-' . 12/$form_builder_item->column_md : 'col-md-6';
                $column[]   =   (isset($form_builder_item->column_sm) && $form_builder_item->column_sm) ? 'col-sm-' . 12/$form_builder_item->column_sm : 'col-sm-12';
                $column[]   =   (isset($form_builder_item->column) && $form_builder_item->column) ? 'col-' . 12/$form_builder_item->column : 'col-12';

                echo '<div class="'.implode(' ', $column).' mb-3">';
                if ($form_builder_item->field_label) {
                    echo '<label class="form-label uk-form-label" for="tzportfolio-form-builder-'.$form_builder_item->field_name.'">'.$form_builder_item->field_label.($form_builder_item->field_required == 1 ? ' <span class="text-danger">*</span>' : '').'</label>';
                }
                $required   =   $form_builder_item->field_required == 1 ? ' required' : '';
                switch ($form_builder_item->type) {
                    case 'text':
                        echo '<input id="tzportfolio-form-builder-'.$form_builder_item->field_name.'" class="w-100 form-control" type="text" name="tzportfolio-form-builder-['.$form_builder_item->field_name.']" placeholder="'.$form_builder_item->field_placeholder.'"'.$required.' />';
                        break;
                    case 'email':
                        echo '<input id="tzportfolio-form-builder-'.$form_builder_item->field_name.'" class="w-100 form-control" type="email" name="tzportfolio-form-builder-['.$form_builder_item->field_name.']" placeholder="'.$form_builder_item->field_placeholder.'"'.$required.' />';
                        break;
                    case 'phone':
                        echo '<input id="tzportfolio-form-builder-'.$form_builder_item->field_name.'" class="w-100 form-control" type="phone" name="tzportfolio-form-builder-['.$form_builder_item->field_name.']" placeholder="'.$form_builder_item->field_placeholder.'"'.$required.' />';
                        break;
                    case 'textarea':
                        echo '<textarea id="tzportfolio-form-builder-'.$form_builder_item->field_name.'" class="w-100 form-control" name="tzportfolio-form-builder-['.$form_builder_item->field_name.']" placeholder="'.$form_builder_item->field_placeholder.'" rows="5"'.$required.'></textarea>';
                        break;
                    case 'select':
                        echo '<select id="tzportfolio-form-builder-'.$form_builder_item->field_name.'" class="w-100 form-select" name="tzportfolio-form-builder-['.$form_builder_item->field_name.']"'.$required.'>';
                        foreach ($form_builder_item->field_options as $key_opt => $field_option) {
                            $selected   =   $field_option->opt_required == 1 ? ' selected' : '';
                            echo '<option value="'.$field_option->opt_value.'"'.$selected.'>'.$field_option->opt_text.'</option>';
                        }
                        echo '</select>';
                        break;
                    case 'checkbox':
                        foreach ($form_builder_item->field_options as $key_opt => $field_option) {
                            $checked   =   $field_option->opt_required == 1 ? ' checked' : '';
                            echo '<div class="form-check">';
                            echo '<input class="form-check-input" type="checkbox" value="'.$field_option->opt_value.'" name="tzportfolio-form-builder-['.$form_builder_item->field_name.']" id="tzportfolio-form-builder-checkbox-'.$form_builder_item->field_name.'-'.$key_opt.'"'.$checked.'>';
                            echo '<label class="form-check-label" for="tzportfolio-form-builder-checkbox-'.$form_builder_item->field_name.'-'.$key_opt.'">'.$field_option->opt_text.'</label>';
                            echo '</div>';
                        }
                        break;
                    case 'radio':
                        foreach ($form_builder_item->field_options as $key_opt => $field_option) {
                            $checked   =   $field_option->opt_required == 1 ? ' checked' : '';
                            echo '<div class="form-check">';
                            echo '<input class="form-check-input" type="radio" value="'.$field_option->opt_value.'" name="tzportfolio-form-builder-['.$form_builder_item->field_name.']" id="tzportfolio-form-builder-checkbox-'.$form_builder_item->field_name.'-'.$key_opt.'"'.$checked.'>';
                            echo '<label class="form-check-label" for="tzportfolio-form-builder-checkbox-'.$form_builder_item->field_name.'-'.$key_opt.'">'.$field_option->opt_text.'</label>';
                            echo '</div>';
                        }
                        break;
                    case 'date':
                        echo '<input id="tzportfolio-form-builder-'.$form_builder_item->field_name.'" class="w-100 form-control" type="date" name="tzportfolio-form-builder-['.$form_builder_item->field_name.']"'.$required.' />';
                        break;
                    case 'range':
                        echo '<input id="tzportfolio-form-builder-'.$form_builder_item->field_name.'" class="w-100 form-range" type="range" name="tzportfolio-form-builder-['.$form_builder_item->field_name.']" min="'.$form_builder_item->field_min.'" max="'.$form_builder_item->field_max.'" step="'.$form_builder_item->field_step.'"'.$required.' />';
                        break;
                    case 'number':
                        echo '<input id="tzportfolio-form-builder-'.$form_builder_item->field_name.'" class="w-100 form-control" type="number" name="tzportfolio-form-builder-['.$form_builder_item->field_name.']" min="'.$form_builder_item->field_min.'" max="'.$form_builder_item->field_max.'" step="'.$form_builder_item->field_step.'"'.$required.' />';
                        break;
                }
                echo '</div>';
            endforeach;
            echo '</div>';
            if ($form_builder->enable_captcha == 1) :
                echo '<div class="uk-margin">';
                if ($form_builder->captcha_type == 'recaptcha') {
                    PluginHelper::importPlugin('captcha', 'recaptcha');
                    $recaptcha = Factory::getApplication()->triggerEvent('onDisplay', array(null, 'tzportfolio_form_builder_recaptcha' , 'tzportfolio-form-builder-recaptcha'));
                    echo (isset($recaptcha[0])) ? $recaptcha[0] : '<p class="uk-alert-danger">' . JText::_('TZPORTFOLIO_RECAPTCHA_NOT_INSTALLED') . '</p>';
                } elseif ($form_builder->captcha_type == 'invisible-recaptcha') {
                    PluginHelper::importPlugin('captcha', 'recaptcha_invisible');
                    $recaptcha = Factory::getApplication()->triggerEvent('onDisplay', array(null, 'tzportfolio_form_builder_invisible_recaptcha' , 'tzportfolio-form-builder-invisible-recaptcha'));
                    echo (isset($recaptcha[0])) ? $recaptcha[0] : '<p class="uk-alert-danger">' . JText::_('TZPORTFOLIO_RECAPTCHA_NOT_INSTALLED') . '</p>';
                } else {
                    $value1 =   rand(1,100);
                    $value2 =   rand(1,100);
                    $mainframe =JFactory::getApplication();
                    $mainframe->setUserState( "tzportfolio-formbuilder-recaptcha.value1", $value1 );
                    $mainframe->setUserState( "tzportfolio-formbuilder-recaptcha.value2", $value2 );
                    echo '<div class="tzportfolio-formbuilder-recaptcha">'.($value1 . ' + ' . $value2 .' = ?').'</div>';
                    echo '<div class="tzportfolio-formbuilder-recaptcha-result"><input type="text" name="tzportfolio-form-captcha" class="form-control" placeholder="'.($value1 . ' + ' . $value2 .' = ?').'"></div>';
                }
                echo '<input type="hidden" name="captcha_type" value="'.$form_builder->captcha_type.'">';
                echo '</div>';
            endif;
            echo '<input type="hidden" class="token" name="'.\JSession::getFormToken().'" value="1">';
            echo '<button type="submit" class="tzportfolio-form-builer-submit uk-margin-top '.$form_builder->submit_class.'">Submit</button>';
            echo '<div class="tzportfolio-formbuilder-status uk-margin"></div>';
            echo '</form>';
            echo '</div>';
        }
    }
}