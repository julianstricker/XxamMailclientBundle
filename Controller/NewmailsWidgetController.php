<?php

namespace Xxam\MailclientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


class NewmailsWidgetController extends Controller
{
    private $templating;
    private $securityTokenStorage;
    public function __construct($templating,$securityTokenStorage)
    {
        $this->templating = $templating;
        $this->securityTokenStorage = $securityTokenStorage;
    }

    //this function is required for every portalwidget:
    public function getWidgetTemplateAction(){
        return 'XxamMailclientBundle:NewmailsWidget:index.js.twig';
    }


    //this function is required for every portalwidget:
    public function getDefinitionAction()
    {
        
        $user = $this->securityTokenStorage->getToken()->getUser();
        $mailaccountusers=$this->getDoctrine()->getManager()->getRepository('XxamMailclientBundle:Mailaccountuser')->findByUserId($user->getId());
        $mailaccounts=Array();
        foreach($mailaccountusers as $mau){
            $ma=$mau->getMailaccount();
            $mailaccounts[]=Array($ma->getId(),$ma->getAccountname());
        }
        return Array(
            'title'=>'Emails',
            'description'=>'Displays Emails',
            'icon'=> '/bundles/xxammailclient/icons/32x32/email.png',
            //'code' => 'feed',
            
            'settings'=>Array(
                Array(
                  'fieldLabel'=> 'Account',
                  'name'=> 'account',
                  'xtype' =>  'combo',
                  'store' => $mailaccounts,
                  'allowBlank' => false
                )
            )
            
        );
    }
    
    public function loadfeedAction($url){
        $response = new Response(str_replace(array("dc:creator","content:encoded"), array('author','content'), file_get_contents(urldecode($url))));
        $response->headers->set('Content-Type', 'text/xml; charset=UTF-8');
        return $response;
    }
}
