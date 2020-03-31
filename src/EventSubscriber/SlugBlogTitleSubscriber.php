<?php


    namespace App\EventSubscriber;


    use ApiPlatform\Core\EventListener\EventPriorities;
    use App\Entity\BlogPost;
    use App\Service\TextManipulationService;
    use Doctrine\ORM\EntityManagerInterface;
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
        /**
         * @var TextManipulationService $textManipulation
         */
        private $textManipulation;
        
        /**
         * @var EntityManagerInterface
         */
        private $em;
    
        /**
         * SlugBlogTitleSubscriber constructor.
         *
         * @param TextManipulationService $textManipulation
         * @param EntityManagerInterface $em
         */
        public function __construct(TextManipulationService $textManipulation, EntityManagerInterface $em)
        {
            $this->textManipulation = $textManipulation;
            $this->em = $em;
        }

        /**
         * @return array
         */
        public static function getSubscribedEvents(): array
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
    
            $slug = $slug = $this->textManipulation->slugify($blogPost->getTitle());
    
            $slugAlreadyPresent = $this->em->getRepository('App:BlogPost')->findOneBy(['slug' => $slug]);
            
            if($slugAlreadyPresent) {
                $slug .= '-' . $blogPost->getId();
            }
    
            $blogPost->setSlug($slug);
        }

    }