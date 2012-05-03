<?php
namespace Zubi\DeviceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Tools\Pagination\Paginator;

use Zubi\DeviceBundle\Entity\Measurement;
use Zubi\DeviceBundle\Entity\Station;
use Zubi\DeviceBundle\Form\StationType;

class MeasurementController extends Controller {
    
    public function indexAction($stationId, $pageNum) {

        $em = $this->getDoctrine()->getEntityManager();

        $user = $this->get('security.context')->getToken()->getUser();

        $stations = $user->getStations();

        if(count($stations) == 0) {
            return $this->redirect($this->generateUrl('ZubiDeviceBundle_addStation'));
        }

        if(!$stationId)
            $currentStation = $stations[0];
        else {
            foreach($stations as $st) {
                if($st->getId() == $stationId)
                    $currentStation = $st;
                    break;
            }
        }

        $measurementsPerPage = 20;

        $dql = "SELECT m FROM ZubiDeviceBundle:Measurement m WHERE m.station = :station ORDER BY m.timestamp DESC";
        $query = $em->createQuery($dql)
                ->setParameter('station', $currentStation)
                ->setFirstResult($measurementsPerPage * $pageNum)
                ->setMaxResults($measurementsPerPage);

        $measurements = new Paginator($query, false);

        $viewVars['stations'] = $stations;
        $viewVars['measurements'] = $measurements;
        $viewVars['currentStation'] = $currentStation;
        $viewVars['paginationStats'] = array('total' => count($measurements), 
                                            'pages' => ceil(count($measurements)/$measurementsPerPage), 
                                            'perPage' => $measurementsPerPage,
                                            'currentPage' => $pageNum
                                        );
        
        return $this->render('ZubiDeviceBundle:Measurement:index.html.twig', $viewVars);
    }
}
