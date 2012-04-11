<?php

namespace Zubi\FaqBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Symfony\Component\Validator\ValidatorFactory;

use Zubi\FaqBundle\Entity\Faq;


class FaqTest extends WebTestCase {

    
        /**
        * @var \Doctrine\ORM\EntityManager
        */
        private $_em;

        public function setUp()
        {
                $kernel = static::createKernel();
                $kernel->boot();
                $this->_em = $kernel->getContainer()
                ->get('doctrine.orm.entity_manager');
        }
        
        public function getFaq($full = false) {
		if($full) {
			// TODO: return validated measurement
		} else
                    return new Faq();
	}
        
        public function testFaqId() {
            $faq = $this->getFaq();
            $this->assertNull($faq->getId());
            $faq->setId(12);
            $this->assertEquals(12, $faq->getId());
        }
        
        public function testFaqTresc() {
            $faq = $this->getFaq();
            $this->assertNull($faq->getTresc());
            $faq->setTresc('Test test test');
            $this->assertEquals('Test test test', $faq->getTresc());
        }
        
        public function testFaqOdpowiedz() {
            $faq = $this->getFaq();
            $this->assertNull($faq->getOdpowiedz());
            $faq->setOdpowiedz('odp raz dwa trzy');
            $this->assertEquals('odp raz dwa trzy', $faq->getOdpowiedz());
        }
        
        public function testFaqIdStatusu() {
            $faq = $this->getFaq();
            $this->assertNull($faq->getIdStatusu());
            $faq->setIdStatusu(2);
            $this->assertEquals(2 , $faq->getIdStatusu());
        }
            
        
        public function testFaqById()
        {
            $results = $this->_em->getRepository('ZubiFaqBundle:Faq')
                ->findOneById("1");
            $this->assertTrue($results != false);
        }
}

?>
