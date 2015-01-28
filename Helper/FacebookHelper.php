<?php
/**
 * Created by PhpStorm.
 * User: siklol
 * Date: 1/28/15
 * Time: 5:55 PM
 */

namespace SikIndustries\Bundles\FacebookBundle\Helper;


use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\GraphObject;
use SikIndustries\Bundles\TrobaUserBundle\Entity\User;
use SikIndustries\Bundles\TrobaUserBundle\Manager\UserManager;
use SikIndustries\Bundles\TrobaUserBundle\Salt\UserSalter;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class FacebookHelper
{
    /**
     * @var FacebookRedirectLoginHelper
     */
    private $redirectLoginHelper;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var UserProviderInterface
     */
    private $userProvider;

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var SecurityContextInterface
     */
    protected $context;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    public function __construct($loginCallbackPath, Router $router, UserProviderInterface $userProvider, UserManager $userManager,
                                SecurityContextInterface $context, Request $request, EventDispatcherInterface $eventDispatcher)
    {
        $this->redirectLoginHelper = new FacebookRedirectLoginHelper($router->generate($loginCallbackPath, [], true));
        $this->router = $router;
        $this->userProvider = $userProvider;
        $this->userManager = $userManager;
        $this->context = $context;
        $this->request = $request;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return FacebookRedirectLoginHelper
     */
    public function getRedirectLoginHelper()
    {
        return $this->redirectLoginHelper;
    }

    /**
     * @param string $url
     * @return mixed
     * @throws \Facebook\FacebookRequestException
     */
    public function getGraphObject($url = '/me')
    {
        $request = $this->getFacebookRequest('GET', $url);
        $response = $request->execute();
        return $response->getGraphObject();
    }

    /**
     * @param $method
     * @param $url
     * @return FacebookRequest
     */
    public function getFacebookRequest($method, $url)
    {
        $facebookSession = $this->getRedirectLoginHelper()->getSessionFromRedirect();
        return new FacebookRequest($facebookSession, $method, $url);
    }

    /**
     * @param GraphObject $graphObject
     * @return User
     */
    public function createUser(GraphObject $graphObject)
    {
        $username = 'user_'.$graphObject->getProperty('id');
        $user = User::findBy('username', $username)->one();

        if (!$user instanceof User) {
            $password = substr(md5(UserSalter::getSalt().uniqid().time()), 0, rand(8, 32));
            $user = $this->userManager->createUser();
            $user->setUsername($username);
            $user->setSalt(UserSalter::getSalt());
            $user->setPassword($password);
            $user->setPassword($this->userManager->password($user));
            $user->setEmail($graphObject->getProperty('email'));

            $user->save();
        }

        return $user;
    }

    /**
     * @param User $user
     */
    public function loginUser(User $user)
    {
        $user = $this->userProvider->loadUserByUsername($user->getUsername());
        $token = new UsernamePasswordToken($user, null, "sik_industries.user_provider", $user->getRoles());
        $this->context->setToken($token); //now the user is logged in

        //now dispatch the login event
        $event = new InteractiveLoginEvent($this->request, $token);
        $this->eventDispatcher->dispatch("security.interactive_login", $event);
    }
}