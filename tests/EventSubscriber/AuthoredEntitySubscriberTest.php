<?php
    
    namespace App\Tests\EventSubscriber;
    
    use \ApiPlatform\Core\EventListener\EventPriorities;
    use \App\Entity\BlogPost;
    use \App\Entity\User;
    use \App\EventSubscriber\AuthoredEntitySubscriber;
    use \Exception;
    use \PHPUnit\Framework\MockObject\MockObject;
    use \PHPUnit\Framework\TestCase;
    use \Symfony\Component\HttpKernel\Event\ViewEvent;
    use \Symfony\Component\HttpKernel\KernelEvents;
    use \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
    use \Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
    
    class AuthoredEntitySubscriberTest extends TestCase
    {
        public function testConfiguration(): void
        {
            $result = AuthoredEntitySubscriber::getSubscribedEvents();
            
            $this->assertArrayHasKey(KernelEvents::VIEW, $result);
            $this->assertEquals(['getAuthenticateUser', EventPriorities::PRE_WRITE], $result[KernelEvents::VIEW]);
        }
        
        /**
         * @return MockObject|TokenStorageInterface
         * @throws Exception
         */
        public function getMockTokenInterface(): MockObject
        {
            $tokenMock = $this->getMockBuilder(TokenInterface::class)->getMockForAbstractClass();
            
            $tokenMock
                ->expects($this->once())
                ->method('getUser')
                ->willReturn(new User());
            
            $tokenStorageMock = $this->getMockBuilder(TokenStorageInterface::class)->getMockForAbstractClass();
            
            $tokenStorageMock
                ->expects($this->once())
                ->method('getToken')
                ->willReturn($tokenMock);
            
            return $tokenStorageMock;
        }
    
        /**
         * @return MockObject|ViewEvent
         * @throws Exception
         */
        public function getMockViewEvent(): MockObject
        {
            $viewEventMock = $this->getMockBuilder(ViewEvent::class)
                ->disableOriginalConstructor()
                ->getMock();
            
            $viewEventMock
                ->expects($this->once())
                ->method('getControllerResult')
                ->willReturn(new BlogPost());
            
            return $viewEventMock;
        }
        
        /**
         * @throws Exception
         */
        public function testSetAuthorCall(): void
        {
            $tokenStorageMock = $this->getMockTokenInterface();
            
            $viewEventMock = $this->getMockViewEvent();
            
            (new AuthoredEntitySubscriber($tokenStorageMock))->getAuthenticateUser($viewEventMock);
        }
        
    }