<?php
    
    declare(strict_types=1);
    
    namespace App\EventListener;
    
    use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
    use ApiPlatform\Core\Exception\ItemNotFoundException;
    use ApiPlatform\Core\Util\RequestAttributesExtractor;
    use Exception;
    use Symfony\Component\EventDispatcher\EventDispatcherInterface;
    use Symfony\Component\HttpKernel\Event\ExceptionEvent;
    use Symfony\Component\HttpKernel\EventListener\ErrorListener as BaseErrorListener;
    use Symfony\Component\Serializer\Exception\UnexpectedValueException;
    use Symfony\Component\Validator\ConstraintViolation;
    use Symfony\Component\Validator\ConstraintViolationList;
    
    class ErrorListener extends BaseErrorListener
    {
        /**
         * @param ExceptionEvent $event
         * @param string|null $eventName
         * @param EventDispatcherInterface|null $eventDispatcher
         *
         * @throws Exception
         */
        public function onKernelException(ExceptionEvent $event, string $eventName = null, EventDispatcherInterface $eventDispatcher = null): void
        {
            $request = $event->getRequest();
            // Normalize exceptions only for routes managed by API Platform
            if (
                'html' === $request->getRequestFormat('') ||
                !((RequestAttributesExtractor::extractAttributes($request)['respond'] ?? $request->attributes->getBoolean('_api_respond', false)) || $request->attributes->getBoolean('_graphql', false))
            ) {
                return;
            }
            
            $throwable = $event->getThrowable();
            
            if ($throwable instanceof UnexpectedValueException && $throwable->getPrevious() instanceof ItemNotFoundException) {
                // Cleanup the message returned as error to the API response
                $violation = new ConstraintViolationList([
                    new ConstraintViolation($throwable->getMessage(), null, [], '', '', ''),
                ]);
                // Change current throwable with a simple ValidationException with an object not found
                $throwable = new ValidationException($violation);
                $event->setThrowable($throwable);
                
                return;
            }
        }
    }