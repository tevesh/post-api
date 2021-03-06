<?php

    namespace App\Form;

    use App\Entity\Image;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\FileType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;

    /**
     * Class ImageType
     * @package App\Form
     */
    class ImageType extends AbstractType
    {
        /**
         * @param FormBuilderInterface $builder
         * @param array $options
         */
        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder->add('file', FileType::class, [
                'label'    => 'form.label.file',
                'required' => false,
            ]);
        }

        /**
         * @param OptionsResolver $resolver
         */
        public function configureOptions(OptionsResolver $resolver): void
        {
            $resolver->setDefaults([
                'data_class'      => Image::class,
                'csrf_protection' => false,
            ]);
        }

        /**
         * @return string
         */
        public function getBlockPrefix(): string
        {
            return '';
        }

    }