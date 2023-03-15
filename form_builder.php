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

class PlgTZ_Portfolio_PlusContentForm_Builder extends TZ_Portfolio_PlusPlugin
{
    protected $autoloadLanguage = true;

    public function onContentAfterSave($context, $data, $isnew){
        if($context == 'com_tz_portfolio_plus.article' || $context == 'com_tz_portfolio_plus.form') {
            if($model  = $this -> getModel($this -> _name, 'TZ_Portfolio_Plus_Addon_'.$this -> _name.'Model')) {
                if(method_exists($model,'save')) {
                    if(isset($this -> _myFormDataBeforeSave) && !empty($this -> _myFormDataBeforeSave)){

                        $addon      = TZ_Portfolio_PlusPluginHelper::getPlugin($this -> _type, $this -> _name);
                        $mydata     = array('value' => $this -> _myFormDataBeforeSave);

                        if(!empty($addon) && isset($addon -> id)){
                            $mydata['extension_id'] = $addon -> id;
                            if(!empty($data) && isset($data -> id)){
                                $mydata['content_id']   = $data -> id;
                            }
                            $mydata['published']    = 1;

                            $model->save( $mydata);
                        }
                    }
                }
            }
        }
    }
    public function onAfterDisplayAdditionInfo($context, &$article, $params, $page = 0, $layout = 'default'
        , $module = null){

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
        }elseif(in_array($context, array('com_tz_portfolio_plus.portfolio', 'com_tz_portfolio_plus.date'
        , 'com_tz_portfolio_plus.featured', 'com_tz_portfolio_plus.tags', 'com_tz_portfolio_plus.users'))){
            if($html = $this -> _getViewHtml($context,$item, $params)){
                return $html;
            }
        }
    }


    /*
     * Register form with position to article form
     * @article: the article data.
     * Return form with position:
     *  -title: title of form to display in article form
     *  -html: html of form to display in article form
     *  -position: position (description, before_description or after_description) to display in article form
     * */
//    public function onAddFormBeforeArticleDescription($article = null){
//        $position   = $this -> __addFormToPosition($article);
//        return $position;
//    }
//    public function onAddFormAfterArticleDescription($article = null){
//        $position   = $this -> __addFormToPosition($article);
//        return $position;
//    }
    public function onAddFormToArticleDescription($article = null){
        $position   = $this -> __addFormToPosition($article);
        return $position;
    }
}