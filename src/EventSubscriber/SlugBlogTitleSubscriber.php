<?php


    namespace App\EventSubscriber;


    use ApiPlatform\Core\EventListener\EventPriorities;
    use App\Entity\BlogPost;
    use App\Service\TextManipulationService;
    use Symfony\Component\EventDispatcher\EventSubscriberInterface;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpKernel\Event\ViewEvent;
    use Symfony\Component\HttpKernel\KernelEvents;

    /**
     * Class SlugBlogTitleSubscriber
     * @package App\EventSubscriber
     */
    class SlugBlogTitleSubscriber implements EventSubscriberInterface
    {
        /** @var TextManipulationService $textManipulation */
        private $textManipulation;

        public function __construct(TextManipulationService $textManipulation)
        {
            $this->textManipulation = $textManipulation;
        }

        /**
         * @return array|void
         */
        public static function getSubscribedEvents()
        {
            return [
                KernelEvents::VIEW => ['createSlugFromText', EventPriorities::PRE_WRITE],
            ];
        }

        /**
         * @param ViewEvent $event
         */
        public function createSlugFromText(ViewEvent $event): void
        {
            /** @var BlogPost $blogPost */
            $blogPost = $event->getControllerResult();

            $method = $event->getRequest()->getMethod();
            if (!$blogPost instanceof BlogPost || Request::METHOD_POST !== $method) {
                return;
            }

            $blogPost->setSlug($this->textManipulation->slugify($blogPost->getTitle()));
        }

    }