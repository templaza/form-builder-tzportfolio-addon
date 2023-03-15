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

class PlgTZ_Portfolio_PlusContentForm_BuilderModelArticle extends TZ_Portfolio_PlusPluginModelItem
{
    public function getForm_BuilderItems(){
        if($model  = JModelLegacy::getInstance('Form_Builder','PlgTZ_Portfolio_PlusContentModel',
            array('ignore_request' => true))) {

            $params = $this -> getState('params');
            $model->setState('params', $params);
            $model->setState('filter.content_id', $this->article->id);
            $items  =   $model -> getItems();
            $mainframe =JFactory::getApplication();
            $tzformbuilder = $mainframe->input->get('tzportfolio-form-builder-', array(), 'RAW');
            if (!empty(($tzformbuilder)) && isset($items[0]->value)) {
                $this->_FormBuilder_Ajax($tzformbuilder, $items[0]->value);
            }

            return $items;
        }
        return false;
    }

    private function _FormBuilder_Ajax($tzformbuilder, $items) {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        $return = array();
        try {
            // Check for request forgeries.
            // if cache isn't enable
            if( !\JFactory::getConfig()->get('caching') && !JPluginHelper::getPlugin('system', 'cache') ) {
                // Check CSRF
                if (!\JSession::checkToken()) {
                    throw new \Exception(\JText::_('TZPORTFOLIO_FORMBUILDER_AJAX_ERROR'));
                }
            }
            $mail = JFactory::getMailer();
            $message = $items->email_body;
            $gcaptcha= '';

            foreach ($tzformbuilder as $field => $value) {
                $message    =   str_replace('{{'.$field.'}}', $value, $message);

                if ($field == 'g-recaptcha-response') {
                    $gcaptcha = $value;
                }
            }

            $replyToMail = $replyToName = '';

            if (intval($items->enable_captcha)) {
                if ($items->captcha_type == 'recaptcha' || $items->captcha_type == 'recaptcha_invisible') {
                    if($gcaptcha == ''){
                        throw new \Exception(\JText::_('TZPORTFOLIO_FORMBUILDER_AJAX_ERROR_INVALID_CAPTCHA'));
                    } else {
                        if($items->captcha_type == 'recaptcha_invisible') {
                            JPluginHelper::importPlugin('captcha', 'recaptcha_invisible');
                        } else {
                            JPluginHelper::importPlugin('captcha', 'recaptcha');
                        }
                        $dispatcher = JEventDispatcher::getInstance();
                        $res = $dispatcher->trigger('onCheckAnswer', $gcaptcha);

                        if (!$res[0]) {
                            throw new \Exception(\JText::_('TZPORTFOLIO_FORMBUILDER_AJAX_ERROR_INVALID_CAPTCHA'));
                        }
                    }
                } else {
                    $mainframe =JFactory::getApplication();
                    $value1 =   intval($mainframe->getUserState( "tzportfolio-formbuilder-recaptcha.value1" ));
                    $value2 =   intval($mainframe->getUserState( "tzportfolio-formbuilder-recaptcha.value2" ));
                    $value_result = intval($mainframe->input->get('tzportfolio-form-captcha', 0));
                    if ( $value1 + $value2 != $value_result) {
                        throw new \Exception(\JText::_('TZPORTFOLIO_FORMBUILDER_AJAX_ERROR_INVALID_CAPTCHA'));
                    }
                }
            }

            //get sender UP
            $senderip       = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
            // Subject Structure
            $site_name 	    = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';
            $mail_subject   = $items->email_subject;
            $mail_subject = preg_replace_callback('/\{\{(\S+?)\}\}/siU', function ($matches) use (&$tzformbuilder, &$site_name) {
                if (isset($tzformbuilder[$matches[1]])) {
                    return $tzformbuilder[$matches[1]];
                } elseif ($matches[1] == 'site-name') {
                    return $site_name;
                }
                return $matches[0];
            }, $mail_subject);
            // Message structure
            $mail_body =  $message;
            $mail_body .= '<p><strong>' . JText::_('TZPORTFOLIO_FORMBUILDER_SENDER_IP'). '</strong>: ' . $senderip .'</p>';

            $config = JFactory::getConfig();

            $sender = array( $config->get( 'mailfrom' ), $config->get( 'fromname' ) );
            $recipient = $config->get( 'mailfrom' );

            // $sender = array( $email, $name );

            if (!empty($items->from_email)) {
                $sender = array($items->from_email, $items->from_email);
                $mail->addReplyTo($items->from_email, $items->from_email);
            }

            if (!empty($items->recipient_email)) {
                $recipient  =   $items->recipient_email;
            }

            if (!empty($items->email_headers)) {
                $additional_header_ajax = explode("\n", $items->email_headers);
                foreach ($additional_header_ajax as $_header)
                {
                    $_header = explode(':', $_header);
                    if (count($_header) > 0)
                    {
                        if (strtolower($_header[0]) == 'reply-to')
                        {
                            $replyToMail =  isset($_header[1]) ?  trim($_header[1]) : '';
                        }
                        if (strtolower($_header[0])  == 'reply-name')
                        {
                            $replyToName =  isset($_header[1]) ?  trim($_header[1]) : '';
                        }
                        if (strtolower($_header[0]) == 'cc' && isset($_header[1]))
                        {
                            $mail->addCc(trim($_header[1]));
                        }
                        if (strtolower($_header[0]) == 'bcc' && isset($_header[1]))
                        {
                            $mail->addCc(trim($_header[1]));
                        }
                    }
                }
                if (!empty($replyToMail)) {
                    if (!empty($replyToName)) {
                        $mail->addReplyTo($replyToMail, $replyToName);
                    } else {
                        $mail->addReplyTo($replyToMail);
                    }
                }
            }

            $mail->setSender($sender);
            $mail->addRecipient($recipient);
            $mail->setSubject($mail_subject);
            $mail->isHTML(true);
            $mail->Encoding = 'base64';
            $mail->setBody($mail_body);

            $message_success    =   isset($items->success_message) && $items->success_message ? $items->success_message : \JText::_('TZPORTFOLIO_FORMBUILDER_SENT_SUCCESSFULLY');
            $message_failed     =   isset($items->failed_message) && $items->failed_message ? $items->failed_message : \JText::_('TZPORTFOLIO_FORMBUILDER_SENT_MAIL_FAILED');

            if ($mail->Send()) {
                $return["status"]   =   'success';
                $return["message"]  =   $message_success;
                $return["code"]     =   200;
            } else {
                throw new \Exception($message_failed);
            }
        } catch (\Exception $e) {
            $return["status"] = "error";
            $return["code"] = $e->getCode();
            $return["message"] = $e->getMessage();
        }
        echo \json_encode($return);
        die();
    }
}