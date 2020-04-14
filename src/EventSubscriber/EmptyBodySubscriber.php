<?php

    namespace App\EventSubscriber;

    use ApiPlatform\Core\EventListener\EventPriorities;
    use Symfony\Component\EventDispatcher\EventSubscriberInterface;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpKernel\Event\RequestEvent;
    use Symfony\Component\HttpKernel\KernelEvents;
    use App\Exception\EmptyBodyException;

    /**
     * Class EmptyBodySubscriber
     * @package App\EventSubscriber
     */
    class EmptyBodySubscriber implements EventSubscriberInterface
    {
        public const ADMISSIBLE_CONTENT_TYPES = [
            'html',
            'form',
        ];

        /**
         * Returns an array of event names this subscriber wants to listen to.
         *
         * The array keys are event names and the value can be:
         *
         *  * The method name to call (priority defaults to 0)
         *  * An array composed of the method name to call and the priority
         *  * An array of arrays composed of the method names to call and respective
         *    priorities, or 0 if unset
         *
         * For instance:
         *
         *  * ['eventName' => 'methodName']
         *  * ['eventName' => ['methodName', $priority]]
         *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
         *
         * @return array The event names to listen to
         */
        public static function getSubscribedEvents(): array
        {
            return [
                KernelEvents::REQUEST => ['handleEmptyBody', EventPriorities::POST_DESERIALIZE],
            ];
        }

        /**
         * @param RequestEvent $event
         *
         * @throws EmptyBodyException
         */
        public function handleEmptyBody(RequestEvent $event): void
        {
            $request = $event->getRequest();
            $method = $request->getMethod();
            $routeName = $request->get('_route');

            // Check the route name not begin with 'api'
            // To avoid to throw an exception with file upload add check to the content type of the header begin with multipart/form-data
            // Check that method is not post or put
            // Check that content type of request is http of form
            if (strpos($routeName, 'api') !== 0 ||
                !in_array($method, [Request::METHOD_POST, Request::METHOD_PUT], true) ||
                strpos($request->headers->get('Content-Type'), 'multipart/form-data') === 0 ||
                in_array($request->getContentType(), self::ADMISSIBLE_CONTENT_TYPES, true)) {
                
                return;
            }
            $data = $request->get('data');
            if (null === $data) {
                throw new EmptyBodyException('Request have empty body');
            }
        }

    }