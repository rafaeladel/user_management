<?php
namespace Tsk\UserBundle\Tests\Unit\Entity;

use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class ModelTestCase extends WebTestCase
{
    protected $em;

    public function setUp()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $this->em = $container->get('doctrine')->getManager();
        $schemaTool = new SchemaTool($this->em);

        $mdf = $this->em->getMetadataFactory();
        $classes = $mdf->getAllMetadata();

        $schemaTool->dropDatabase();
        $schemaTool->createSchema($classes);

//        //Load all fixtures
        $kernel = $client->getKernel();
        $application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
        $application->setAutoExit(false);
        $options = array('command' => 'doctrine:fixtures:load', "--no-interaction" => true, "--quiet" => true);
        $application->run(new \Symfony\Component\Console\Input\ArrayInput($options));
    }

    public function tearDown()
    {
        parent::tearDown();
    }
}
?>