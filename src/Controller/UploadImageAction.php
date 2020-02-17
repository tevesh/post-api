<?php


    namespace App\Controller;

    use ApiPlatform\Core\Validator\Exception\ValidationException;
    use App\Entity\Image;
    use App\Form\ImageType;
    use Doctrine\ORM\EntityManagerInterface;
    use Exception;
    use Symfony\Component\Form\FormFactoryInterface;
    use Symfony\Component\HttpFoundation\Request;
    use ApiPlatform\Core\Validator\ValidatorInterface;

    /**
     * Class UploadImageAction
     * @package App\Controller
     */
    class UploadImageAction
    {
        /**
         * @var FormFactoryInterface
         */
        private $formFactory;
        /**
         * @var EntityManagerInterface
         */
        private $entityManager;
        /**
         * @var ValidatorInterface
         */
        private $validator;

        /**
         * UploadImageAction constructor.
         *
         * @param FormFactoryInterface $formFactory
         * @param EntityManagerInterface $entityManager
         * @param ValidatorInterface $validator
         */
        public function __construct(FormFactoryInterface $formFactory, EntityManagerInterface $entityManager, ValidatorInterface $validator)
        {
            $this->formFactory = $formFactory;
            $this->entityManager = $entityManager;
            $this->validator = $validator;
        }

        /**
         * Whole class is like a single method that should be called by creating a new instance
         *
         * @param Request $request
         *
         * @return Image
         * @throws Exception
         */
        public function __invoke(Request $request): Image
        {
            $image = new Image();
            $form = $this->formFactory->create(ImageType::class, $image);
            $form->handleRequest($request);
            // Validation is defined into entity class and form type
            if ($form->isSubmitted() && $form->isValid()) {
                $this->entityManager->persist($image);
                $this->entityManager->flush();

                // Need this to cleanup the virtual property that contain the binary of the image uploaded
                // to not produce a massive response contain a useless binary content
                $image->setFile(null);

                return $image;
            }

            // To avoid that validation always fail because the file property is set to null is needed to add this validator
            // to not leave the validation to Api Platform
            // Also throw and exception in case of form was invalid
            throw new ValidationException($this->validator->validate($image));
        }
    }