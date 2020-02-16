<?php


    namespace App\EventSubscriber;


    use ApiPlatform\Core\EventListener\EventPriorities;
    use App\Entity\PublishedDateEntityInterface;
    use DateTime;
    use Exception;
    use Symfony\Component\EventDispatcher\EventSubscriberInterface;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpKernel\Event\ViewEvent;
    use Symfony\Component\HttpKernel\KernelEvents;

    /**
     * Class PublishDateCommentSubscriber
     * @package App\EventSubscriber
     */
    class PublishDateSubscriber implements EventSubscriberInterface
    {
        /**
         * @return array|void
         */
        public static function getSubscribedEvents()
        {
            return [
                KernelEvents::VIEW => ['setPublishDate', EventPriorities::PRE_WRITE],
            ];
        }

        /**
         * @param ViewEvent $event
         *
         * @throws Exception
         */
        public function setPublishDate(ViewEvent $event): void
        {
            /** @var PublishedDateEntityInterface $entity */
            $entity = $event->getControllerResult();

            $method = $event->getRequest()->getMethod();
            if (!$entity instanceof PublishedDateEntityInterface || Request::METHOD_POST !== $method) {
                return;
            }

            if ($entity->getPublished()) {
                return;
            }

            $entity->setPublished(new DateTime());
        }

    }