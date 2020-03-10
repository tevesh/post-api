<?php
    
    namespace App\Tests\EventSubscriber;
    
    use ApiPlatform\Core\EventListener\EventPriorities;
    use App\EventSubscriber\AuthoredEntitySubscriber;
    use App\EventSubscriber\EmptyBodySubscriber;
    use App\Exception\EmptyBodyException;
    use PHPUnit\Framework\MockObject\MockObject;
    use PHPUnit\Framework\TestCase;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpKernel\Event\RequestEvent;
    use Symfony\Component\HttpKernel\Event\ViewEvent;
    use Symfony\Component\HttpKernel\KernelEvents;
    
    class EmptyBodySubscriberTest extends TestCase
    {
        public function testConfiguration(): void
        {
            $result = EmptyBodySubscriber::getSubscribedEvents();
            
            $this->assertArrayHasKey(KernelEvents::REQUEST, $result);
            $this->assertEquals(['handleEmptyBody', EventPriorities::POST_DESERIALIZE], $result[KernelEvents::REQUEST]);
        }
    
//        /**
//         * @param string $method
//         * @param string $contentType
//         *
//         * @return MockObject|ViewEvent
//         */
//        public function getRequestEventMock(string $method, string $contentType): MockObject
//        {
//            // Get mock instance of request
//            $requestMock = $this->getMockBuilder(Request::class)
//                ->getMock();
//            // Check mock instance is called just once and the method getMethod return exact the method required
//            $requestMock
//                ->expects($this->once())
//                ->method('getMethod')
//                ->willReturn($method);
//            // Check mock instance is called just once and the method getContentType return the exact content type required
//            $requestMock
//                ->expects($this->once())
//                ->method('getContentType')
//                ->willReturn($contentType);
//
//            $requestEventMock = $this->getMockBuilder(RequestEvent::class)
//                ->disableOriginalConstructor()
//                ->getMock();
//
//            $requestEventMock
//                ->expects($this->once())
//                ->method('getRequest')
//                ->willReturn($requestMock);
//
//            return $requestEventMock;
//        }
//
//        /**
//         * @param string $method
//         * @param string $contentType
//         * @param bool $shouldCallSetAuthorMethod
//         *
//         * @throws EmptyBodyException
//         */
//        public function handleEmptyBodyTest(string $method, string $contentType, bool $shouldCallSetAuthorMethod): void
//        {
//            $requestEventMock = $this->getRequestEventMock($method, $contentType);
//
//            (new EmptyBodySubscriber())->handleEmptyBody($requestEventMock);
//        }
        
    }