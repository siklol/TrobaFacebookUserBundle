<?php
namespace SikIndustries\Bundles\FacebookBundle\Listener;

use Facebook\FacebookSession;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class FacebookSessionListener
{
    public function __construct($appId, $appSecret)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
    }
    public function onKernelRequest(GetResponseEvent $event)
    {
        FacebookSession::setDefaultApplication($this->appId, $this->appSecret);
    }
}