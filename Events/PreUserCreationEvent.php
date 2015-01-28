<?php
/**
 * Created by PhpStorm.
 * User: siklol
 * Date: 1/28/15
 * Time: 11:28 PM
 */

namespace SikIndustries\Bundles\FacebookBundle\Events;


use Facebook\GraphObject;
use SikIndustries\Bundles\TrobaUserBundle\Entity\User;
use Symfony\Component\EventDispatcher\Event;

class PreUserCreationEvent extends Event
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var GraphObject
     */
    protected $graphObject;

    public function __construct(User $user, GraphObject $graphObject)
    {
        $this->graphObject = $graphObject;
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return GraphObject
     */
    public function getGraphObject()
    {
        return $this->graphObject;
    }
}