<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2015 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// No direct access.
defined('_JEXEC') or die;

$form   = $this -> form;
$group  = 'addon.'.$this -> _name;
//$group  = 'attribs';
$document   =   JFactory::getDocument();
//$document->addStyleSheet(JUri::root().'components/com_tz_portfolio_plus/addons/content/form_builder/admin/css/style.css');
if($form){
    $fieldsets = $form -> getFieldsets($group);
    if(count($fieldsets)){
        foreach($fieldsets as $name => $fieldset){
            $fields = $form -> getFieldset($name);
            if(count($fields)){
                ?>
                <fieldset>
                    <?php
                    foreach($fields as $field){
                        echo $field -> renderField();
                    }
                    ?>
                </fieldset>
                <?php
            }
        }
    }
}
?>