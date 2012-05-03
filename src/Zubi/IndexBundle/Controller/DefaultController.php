<?php

namespace Zubi\IndexBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Zubi\DeviceBundle\Entity\Measurement;
use Zubi\DeviceBundle\Entity\Station;
use Symfony\Component\Security\Core\SecurityContext;
class DefaultController extends Controller
{
    
    public function indexAction() {

        $viewVars = array();       

        if($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $this->get('security.context')->getToken()->getUser();

            

            $viewVars['user']       = $user;
            $viewVars['stations']   = $user->getStations();
        
        }

        return $this->render('ZubiIndexBundle:Default:index.html.twig', $viewVars);
    }
}
