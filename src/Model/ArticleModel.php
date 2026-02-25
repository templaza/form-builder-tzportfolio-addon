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

namespace TemPlaza\Component\TZ_Portfolio\AddOn\Content\Form_builder\Site\Model;

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Mail\MailerFactoryInterface;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\IpHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\AddOn\AddOnItemModel;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\Helper\AddonHelper;

class ArticleModel extends AddOnItemModel {
    protected function populateState($ordering = null, $direction = null)
    {
        $app = Factory::getApplication();
        $input = $app->input;

        $this->setState('filter.catid', null);
        $this->setState('filter.content_id', null);

        parent::populateState($ordering, $direction);

    }
    protected function getStoreId($id = '')
    {
        // Add the list state to the store id.
        $id .= ':' . $this->getState('list.start');
        $id .= ':' . $this->getState('list.limit');
        $id .= ':' . $this->getState('filter.content_id');
        $id .= ':' . serialize($this->getState('filter.catid'));
        $id .= ':' . $this->getState('list.ordering');
        $id .= ':' . $this->getState('list.direction');

        return md5($this->context . ':' . $id);
    }

    protected function getListQuery()
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        $query->select('DISTINCT d.*');
        $query->from($db->quoteName('#__tz_portfolio_plus_addon_data').' AS d');
        $query -> join('INNER', '#__tz_portfolio_plus_content AS c ON c.id = d.content_id');
        $query ->join('INNER', '#__tz_portfolio_plus_content_category_map AS cm ON cm.contentid = c.id');
        $query ->join('INNER', '#__tz_portfolio_plus_categories AS cc ON cc.id = cm.catid');
        $query -> join('INNER', '#__tz_portfolio_plus_extensions AS e ON e.id = d.extension_id');

        if($addon = AddonHelper::getPlugin('content', 'form_builder')) {
            $query->where('d.extension_id =' .(int) $addon -> id);
        }
        $query -> where('d.element ='.$db -> quote('form_builder'));

        if($content_id = $this -> getState('filter.content_id')){
            $query -> where('d.content_id = '.$content_id);
        }
        $query -> where('d.published = 1');

        if($catid = $this -> getState('filter.catid', null)) {
            if(is_array($catid)){
                $query -> where('cc.id IN('.implode(',', $catid).')');
            }else{
                $query -> where('cc.id = '.(int) $catid);
            }
        }
        return $query;
    }

    public function getAddonData()
    {
        $query = $this->getListQuery();
        $db = $this->getDatabase();
        $db->setQuery($query);
        return $db->loadObject();
    }

    public function getForm()
    {
        $params = $this -> getState('params');
        $this->setState('params', $params);
        $this->setState('filter.content_id', $this->article->id);
        $addon  =   $this -> getAddonData();
        $mainframe =Factory::getApplication();
        $tzformbuilder = $mainframe->input->get('tzportfolio-form-builder-', array(), 'RAW');
        if (!empty(($tzformbuilder)) && !empty($addon->value)) {
            $this->_FormBuilder_Ajax($tzformbuilder, json_decode($addon->value));
        }

        return $addon;
    }

    private function _FormBuilder_Ajax($tzformbuilder, $items) {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        $return = array();
        try {
            // Check for request forgeries.
            // if cache isn't enable
            if( !Factory::getApplication()->getConfig()->get('caching') && !PluginHelper::getPlugin('system', 'cache') ) {
                // Check CSRF
                if (!Session::checkToken()) {
                    throw new \Exception(Text::_('TZPORTFOLIO_FORMBUILDER_AJAX_ERROR'));
                }
            }
            $mail = Factory::getContainer()->get(MailerFactoryInterface::class)->createMailer();
            $message = $items->email_body;
            $gcaptcha= '';

            foreach ($tzformbuilder as $field => $value) {
                if (is_array($value)) {
                    $value = implode(', ', $value);
                }
                $message        =   str_replace('{{'.$field.'}}', $value, $message);
                $items->email_headers  =   str_replace('{{'.$field.'}}', $value, $items->email_headers);
                if ($field == 'g-recaptcha-response') {
                    $gcaptcha = $value;
                }
            }

            $replyToMail = $replyToName = '';

            if (intval($items->enable_captcha)) {
                if ($items->captcha_type == 'recaptcha' || $items->captcha_type == 'recaptcha_invisible') {
                    if($gcaptcha == ''){
                        throw new \Exception(Text::_('TZPORTFOLIO_FORMBUILDER_AJAX_ERROR_INVALID_CAPTCHA'));
                    } else {
                        if($items->captcha_type == 'recaptcha_invisible') {
                            PluginHelper::importPlugin('captcha', 'recaptcha_invisible');
                        } else {
                            PluginHelper::importPlugin('captcha', 'recaptcha');
                        }
                        $res = Factory::getApplication()->triggerEvent('onCheckAnswer', [$gcaptcha]);

                        if (!$res[0]) {
                            throw new \Exception(Text::_('TZPORTFOLIO_FORMBUILDER_AJAX_ERROR_INVALID_CAPTCHA'));
                        }
                    }
                } else {
                    $mainframe =Factory::getApplication();
                    $value1 =   intval($mainframe->getUserState( "tzportfolio-formbuilder-recaptcha.value1" ));
                    $value2 =   intval($mainframe->getUserState( "tzportfolio-formbuilder-recaptcha.value2" ));
                    $value_result = intval($mainframe->input->get('tzportfolio-form-captcha', 0));
                    if ( $value1 + $value2 != $value_result) {
                        throw new \Exception(Text::_('TZPORTFOLIO_FORMBUILDER_AJAX_ERROR_INVALID_CAPTCHA'));
                    }
                }
            }

            //get sender UP
            $senderip       = IpHelper::getIp();
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
            $mail_body .= '<p><strong>' . Text::_('TZPORTFOLIO_FORMBUILDER_SENDER_IP'). '</strong>: ' . $senderip .'</p>';

            $config = Factory::getApplication()->getConfig();

            $sender = array( $config->get( 'mailfrom' ), $config->get( 'fromname' ) );
            $recipient = $config->get( 'mailfrom' );

            // $sender = array( $email, $name );
            if (!empty($items->from_email)) {
                $mail->addReplyTo($items->from_email);
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
                            $replyToMail =  isset($_header[1]) ?  trim($_header[1]) : (!empty($items->from_email) ? $items->from_email : '');
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
                        $sender = array($replyToMail, $replyToName);
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

            $message_success    =   isset($items->success_message) && $items->success_message ? $items->success_message : Text::_('TZPORTFOLIO_FORMBUILDER_SENT_SUCCESSFULLY');
            $message_failed     =   isset($items->failed_message) && $items->failed_message ? $items->failed_message : Text::_('TZPORTFOLIO_FORMBUILDER_SENT_MAIL_FAILED');

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