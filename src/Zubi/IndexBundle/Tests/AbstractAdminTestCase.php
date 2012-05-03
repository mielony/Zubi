<?php

namespace Zubi\IndexBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Zubi\UserBundle\Entity\User;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Role\Role;
use JMS\SecurityExtraBundle\Security\Authentication\Token\RunAsUserToken;  

abstract class AbstractAdminTestCase extends WebTestCase
{
    /**
     * Benjamin declare this method as abstract and says that 
     * it must return an instance of UserInterface but 'string' also works.
     * Note that roles in this case must be defined manually
     * as array ($user->getRoles() won't work)
     */
    protected function getCurrentUser() {
        
        $user = new User();
        $user->setId(1);
        $user->setEmail('nostrzak@gmail.com');

        return $user;
    }


    /**
     * User with auth.
     *
     * @param $firewallName
     * @param array $options
     * @param array $server
     *
     * @return Symfony\Bundle\FrameworkBundle\Test\Client|Symfony\Component\BrowserKit\Client
     */
    protected function createClientWithAuthentication($firewallName, array $options = array(), array $server = array())
    {
        /* @var $client Symfony\Component\BrowserKit\Client */
        $client = $this->createClient($options, $server);
        // has to be set otherwise "hasPreviousSession" in Request returns false.
        $client->getCookieJar()->set(new \Symfony\Component\BrowserKit\Cookie(session_name(), true));

        $user = $this->getCurrentUser();

        $token = new UsernamePasswordToken($user, null, $firewallName, array('ROLE_ADMIN'));
        self::$kernel->getContainer()->get('session')->set('_security_' . $firewallName, serialize($token));

        return $client;
    }
} 