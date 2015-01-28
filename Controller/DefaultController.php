<?php

namespace SikIndustries\Bundles\FacebookBundle\Controller;

use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookRequestException;
use SikIndustries\Bundles\FacebookBundle\Helper\FacebookHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class DefaultController
 * @package SikIndustries\Bundles\FacebookBundle\Controller
 *
 * @Route("/facebook")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/callback", name="facebook_callback")
     * @Template()
     */
    public function loginCallbackAction()
    {
        /** @var FacebookHelper $helper */
        $helper = $this->get('facebook.helper');
        try {
            $graphObject = $helper->getGraphObject();

            $user = $helper->createUser($graphObject);
            $helper->loginUser($user);
        } catch( \Exception $e ) {
            print_r($e->getMessage());
            throw new HttpException(403, $this->get('translator')->trans('Could not login with facebook'));
        }
        return $this->redirect('/');
    }
}
