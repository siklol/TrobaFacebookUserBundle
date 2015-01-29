<?php
namespace SikIndustries\Bundles\FacebookBundle\Listener;

use Facebook\FacebookSession;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class FacebookSessionListener
{
    protected $appId;
    protected $appSecret;

    /**
     * @var Session
     */
    protected $session;
    public function __construct($appId, $appSecret, Session $session)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->session = $session;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $this->session->start();
        FacebookSession::setDefaultApplication($this->appId, $this->appSecret);
    }
}