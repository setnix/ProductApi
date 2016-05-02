<?php

namespace AppBundle\Form;

use AppBundle\Form\DataTransformer\TagCollectionToArray;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Article form type
 *
 * Class ArticleType
 */
class ArticleType extends AbstractType
{
    /**
     * Entity manager.
     *
     * @var ObjectManager
     */
    protected $entityManager;

    /**
     * ArticleType constructor.
     *
     * @param ObjectManager $entityManager
     */
    public function __construct(ObjectManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $dataTransformer = new TagCollectionToArray($this->entityManager);

        $builder
            ->add('title', Type\TextType::class)
            ->add('body', Type\TextareaType::class)
            ->add(
                'tags',
                Type\CollectionType::class,
                [
                    'entry_type'   => TagType::class,
                    'by_reference' => false,
                    'allow_add'    => true,
                    'allow_delete' => true,
                ]
            );

        $builder->get('tags')->addModelTransformer($dataTransformer);
    }

    /**
     * {@inheritdoc}
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'csrf_protection'    => false,
                'data_class'         => 'AppBundle\Entity\Article',
                'allow_extra_fields' => true,
            ]
        );
    }
}
