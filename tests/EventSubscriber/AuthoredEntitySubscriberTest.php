<?php
    
    namespace App\Tests\EventSubscriber;
    
    use ApiPlatform\Core\EventListener\EventPriorities;
    use App\Entity\BlogPost;
    use App\Entity\Comment;
    use App\Entity\User;
    use App\EventSubscriber\AuthoredEntitySubscriber;
    use Exception;
    use PHPUnit\Framework\MockObject\MockObject;
    use PHPUnit\Framework\TestCase;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpKernel\Event\ViewEvent;
    use Symfony\Component\HttpKernel\KernelEvents;
    use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
    use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
    
    class AuthoredEntitySubscriberTest extends TestCase
    {
        public function testConfiguration(): void
        {
            $result = AuthoredEntitySubscriber::getSubscribedEvents();
            
            $this->assertArrayHasKey(KernelEvents::VIEW, $result);
            $this->assertEquals(['getAuthenticateUser', EventPriorities::PRE_WRITE], $result[KernelEvents::VIEW]);
        }
    
        /**
         * @param bool $hasToken
         *
         * @return MockObject|TokenStorageInterface
         * @throws Exception
         */
        public function getTokenStorageInterfaceMock(bool $hasToken = true): MockObject
        {
            $tokenMock = $this->getMockBuilder(TokenInterface::class)->getMockForAbstractClass();
            
            $tokenMock
                ->expects($hasToken ? $this->once() : $this->never())
                ->method('getUser')
                ->willReturn(new User());
            
            $tokenStorageInterfaceMock = $this->getMockBuilder(TokenStorageInterface::class)->getMockForAbstractClass();
            
            $tokenStorageInterfaceMock
                ->expects($this->once())
                ->method('getToken')
                ->willReturn($hasToken ? $tokenMock : null);
            
            return $tokenStorageInterfaceMock;
        }
        
        /**
         * @param string $method
         * @param $controllerResult
         *
         * @return MockObject|ViewEvent
         */
        public function getViewEventMock(string $method, $controllerResult): MockObject
        {
            $requestMock = $this->getMockBuilder(Request::class)
                ->getMock();
            $requestMock
                ->expects($this->once())
                ->method('getMethod')
                ->willReturn($method);
            
            $viewEventMock = $this->getMockBuilder(ViewEvent::class)
                ->disableOriginalConstructor()
                ->getMock();
            
            $viewEventMock
                ->expects($this->once())
                ->method('getControllerResult')
                ->willReturn($controllerResult);
            
            $viewEventMock
                ->expects($this->once())
                ->method('getRequest')
                ->willReturn($requestMock);
            
            return $viewEventMock;
        }
        
        /**
         * @param string $concreteEntityClassName
         * @param bool $shouldCallSetAuthorMethod
         *
         * @return MockObject
         */
        public function getEntityMock(string $concreteEntityClassName, bool $shouldCallSetAuthorMethod): MockObject
        {
            $entityMock = $this->getMockBuilder($concreteEntityClassName)
                ->setMethods(['setAuthor'])
                ->getMock();
            $entityMock
                ->expects($shouldCallSetAuthorMethod ? $this->once() : $this->never())
                ->method('setAuthor');
            
            return $entityMock;
        }
    
        /**
         * @throws Exception
         */
        public function testNoTokenPresent(): void
        {
            $tokenStorageInterfaceMock = $this->getTokenStorageInterfaceMock(false);
    
            $viewEventMock = $this->getViewEventMock(Request::METHOD_POST, new class {});
    
            (new AuthoredEntitySubscriber($tokenStorageInterfaceMock))->getAuthenticateUser($viewEventMock);
        }
        
        /**
         * @dataProvider providerSetAuthorCall
         *
         * @param string $concreteEntityClassName
         * @param string $method
         *
         * @param bool $shouldCallSetAuthorMethod
         *
         * @throws Exception
         */
        public function testSetAuthorCall(string $concreteEntityClassName, string $method, bool $shouldCallSetAuthorMethod): void
        {
            $entityMock = $this->getEntityMock($concreteEntityClassName, $shouldCallSetAuthorMethod);
            
            $tokenStorageInterfaceMock = $this->getTokenStorageInterfaceMock();
            
            $viewEventMock = $this->getViewEventMock($method, $entityMock);
            
            (new AuthoredEntitySubscriber($tokenStorageInterfaceMock))->getAuthenticateUser($viewEventMock);
        }
    
        /**
         * Set up the arguments to pass to testSetAuthorCall and execute test for different scenarios
         *
         * @return array
         */
        public function providerSetAuthorCall(): array
        {
            return [
                [BlogPost::class, Request::METHOD_POST, true],
                [BlogPost::class, Request::METHOD_GET, false],
                [Comment::class, Request::METHOD_POST, true],
                [Comment::class, Request::METHOD_GET, false],
                ['NonExisting', Request::METHOD_GET, false],
            ];
        }
    }