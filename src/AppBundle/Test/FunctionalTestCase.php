<?php

namespace AppBundle\Test;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base test class for functional tests.
 *
 * Class FunctionalTestCase
 */
class FunctionalTestCase extends WebTestCase
{
    /**
     * Test client.
     *
     * @var Client
     */
    protected $client;

    /**
     * Dependency injection container.
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->client    = static::createClient();
        $this->container = $this->client->getKernel()->getContainer();

        $this->setUpDatabase();
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function tearDown()
    {
        $this->dropDatabase();
    }

    /**
     * Populate database with sample data.
     *
     * @param array $entities
     */
    protected function populateDataBaseWithSampleData(array $entities)
    {
        $entityManager = $this->container->get('doctrine.orm.entity_manager');

        foreach ($entities as $entity) {
            if (is_object($entity)) {
                $entityManager->persist($entity);
                $entityManager->flush($entity);
            }
        }
    }

    /**
     * Set up MySQL database.
     *
     * @return void
     * @throws \Doctrine\DBAL\DBALException
     */
    public function setUpDatabase()
    {
        AnnotationRegistry::registerFile(
            getcwd() . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php'
        );

        $connection = $this->getConnection();
        $params     = $connection->getParams();
        $name       = $connection->getParams()['dbname'];
        unset($params['dbname']);

        $tmpConnection = DriverManager::getConnection($params);
        $name          = $tmpConnection->getDatabasePlatform()->quoteSingleIdentifier($name);

        try {
            $tmpConnection->getSchemaManager()->dropDatabase($name);
        } catch (\Exception $ex) {
        }

        $tmpConnection->getSchemaManager()->createDatabase($name);
        $tmpConnection->close();

        $this->createSchema();
    }

    /**
     * Drop MySQL database.
     *
     * @return void
     */
    public function dropDatabase()
    {
        $connection = $this->getConnection();
        $name       = $connection->getParams()['dbname'];
        $name       = $connection->getSchemaManager()->getDatabasePlatform()->quoteSingleIdentifier($name);
        $connection->getSchemaManager()->dropDatabase($name);
    }

    /**
     * Get database connection from DI container.
     *
     * @return Connection
     */
    public function getConnection()
    {
        return $this->container->get('doctrine')->getConnection();
    }

    /**
     * Create schema.
     *
     * @return void
     */
    public function createSchema()
    {
        $entityManager = $this->getEntityManager();
        $metadata      = $entityManager->getMetadataFactory()->getAllMetadata();

        if (!empty($metadata)) {
            $schemaTool = new SchemaTool($entityManager);
            $schemaTool->createSchema($metadata);
        }
    }

    /**
     * Get entity manager from DI container.
     *
     * @return EntityManagerInterface
     */
    public function getEntityManager()
    {
        return $this->container->get('doctrine')->getManager();
    }
}
