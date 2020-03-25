<?php
    
    use App\DataFixtures\AppFixtures;
    use Behatch\Context\RestContext;
    use Behatch\HttpCall\Request;
    use Coduo\PHPMatcher\Factory\MatcherFactory;
    use Coduo\PHPMatcher\Matcher;
    use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
    use Doctrine\Common\DataFixtures\Purger\ORMPurger;
    use Doctrine\ORM\EntityManagerInterface;
    use Doctrine\ORM\Tools\SchemaTool;
    use Doctrine\ORM\Tools\ToolsException;
    
    /**
     * This context class contains the definitions of the steps used by the demo
     * feature file. Learn how to get started with Behat and BDD on Behat's website.
     *
     * @see http://behat.org/en/latest/quick_start.html
     */
    class FeatureContext extends RestContext
    {
        protected CONST USERS = [
            'admin' => 'Test123!',
        ];
        
        protected CONST AUTH_URL = '/api/login_check';
        
        protected CONST AUTH_JSON = '
            {
                "username":"%s",
                "password":"%s"
            }
        ';
        /**
         * @var AppFixtures
         */
        private $fixtures;
        /**
         * @var Matcher $matcher
         */
        private $matcher;
        /**
         * @var EntityManagerInterface $em
         */
        private $em;
        
        /**
         * FeatureContext constructor.
         *
         * @param Request $request
         * @param AppFixtures $fixtures
         * @param EntityManagerInterface $em
         */
        public function __construct(Request $request, AppFixtures $fixtures, EntityManagerInterface $em)
        {
            parent::__construct($request);
            $this->fixtures = $fixtures;
            $this->matcher = (new MatcherFactory)->createMatcher();
            $this->em = $em;
        }
        
        /**
         * @Given I am authenticated as :user
         *
         * @param $user
         */
        public function imAuthenticatedAs($user): void
        {
            $this->request->setHttpHeader('Content-Type', 'application/ld+json');
            $this->request->send(
                'POST',
                $this->locatePath(self::AUTH_URL),
                [],
                [],
                sprintf(self::AUTH_JSON, $user, self::USERS[$user])
            );
            $json = json_decode($this->request->getContent(), true);
            // Assert that a token is created
            $this->assertTrue(isset($json['token']));
            
            $token = $json['token'];
            $this->request->setHttpHeader('Authorization', 'Bearer ' . $token);
        }
        
        /**
         * @BeforeScenario @createSchema
         *
         * @throws ToolsException
         */
        public function createSchema(): void
        {
            // Get all entity metadata
            $classes = $this->em->getMetadataFactory()->getAllMetadata();
            
            // Drop and create schema
            $schemaTool = new SchemaTool($this->em);
            $schemaTool->dropDatabase();
            $schemaTool->createSchema($classes);
            
            // Load and execute fixtures
            $purger = new ORMPurger($this->em);
            $fixtureExecutor = new ORMExecutor($this->em, $purger);
            $fixtureExecutor->execute([$this->fixtures]);
        }
    }
