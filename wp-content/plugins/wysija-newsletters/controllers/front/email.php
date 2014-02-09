<?php
defined('WYSIJA') or die('Restricted access');

class WYSIJA_control_front_email extends WYSIJA_control_front{
    var $model='email';
    var $view='email';

    function WYSIJA_control_front_email(){
        parent::WYSIJA_control_front();
    }

    function view(){

        $data=array();

        header('Content-type:text/html; charset=utf-8');

        // Get email model as object.
        $emailM = WYSIJA::get('email','model');
        $emailM->getFormat = OBJECT;
        // Get config model
        $configM = WYSIJA::get('config','model');

        // Helpers
        $emailH = WYSIJA::get('email','helper');
        $mailerH = WYSIJA::get('mailer','helper');

        $email_id = (int)$_REQUEST['email_id'];
        // Get current email object.
        $current_email = $emailM->getOne($email_id);

        if($current_email->type==2){

            $emailM->reset();
            $autonewsHelper = WYSIJA::get('autonews','helper');
            $autonewsHelper->refresh_automatic_content(array($email_id));
            $emailM->getFormat = OBJECT;
            $current_email = $emailM->getOne($email_id);
        }




        // Get current user object if possible
        $current_user = null;
        if(isset($_REQUEST['user_id'])){
            // Get User Model
            $userM = WYSIJA::get('user','model');
            $userM->getFormat = OBJECT;
            $current_user = $userM->getOne((int)$_REQUEST['user_id']);
        }

        // Parse and replace user tags.
        $mailerH->parseUserTags($current_email);
        $mailerH->parseSubjectUserTags($current_email);
        $mailerH->replaceusertags($current_email, $current_user);

        // Set Body
        $email_render = $current_email->body;

        // Parse old shortcodes that we are parsing in the queue.
        $find = array('[unsubscribe_linklabel]');
        $replace = array($configM->getValue('unsubscribe_linkname'));
        if (isset($current_email->params['autonl']['articles']['first_subject'])){
            $find[] = '[post_title]';
            $replace[] = $current_email->params['autonl']['articles']['first_subject'];
        }
        if (isset($current_email->params['autonl']['articles']['total'])){
            $find[] = '[total]';
            $replace[] = $current_email->params['autonl']['articles']['total'];
        }
        if (isset($current_email->params['autonl']['articles']['ids'])){
            $find[] = '[number]';
            $replace[] = $current_email->params['autonl']['articles']['ids'];
        }

        $email_render = str_replace($find, $replace, $email_render);

        // Strip unsubscribe links.
        $email_render = $emailH->stripPersonalLinks($email_render);

        echo apply_filters('wysija_preview',$email_render);

        exit;
    }

}