<?php


namespace AppBundle\Form\DataTransformer;

use AppBundle\Entity\Tag;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Convert Tag collection to array and vice versa
 *
 * Class TagCollectionToArray
 */
class TagCollectionToArray implements DataTransformerInterface
{
    /**
     * Entity manager.
     *
     * @var ObjectManager
     */
    protected $entityManager;

    /**
     * TagCollectionToArray constructor.
     *
     * @param ObjectManager $entityManager
     */
    public function __construct(ObjectManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Transforms PersistentCollection to array.
     *
     * @param PersistentCollection $collection
     *
     * @return ArrayCollection
     */
    public function transform($collection)
    {
        $newCollection = [];

        if (!($newCollection instanceof PersistentCollection)) {
            return new ArrayCollection();
        }

        /** @var Tag $tagEntity */
        foreach ($collection as $tagEntity) {
            $newCollection[] = $tagEntity;
        }

        return new ArrayCollection($newCollection);
    }

    /**
     * Transforms array to PersistentCollection.
     *
     * @param ArrayCollection|null $collection
     *
     * @return PersistentCollection|ArrayCollection
     */
    public function reverseTransform($collection)
    {
        if (null === $collection) {
            return new ArrayCollection();
        }

        $newCollection = [];

        /** @var Tag $newTagEntity */
        foreach ($collection as $index => $newTagEntity) {

            $existingTag = $this->entityManager
                        ->getRepository(Tag::class)
                        ->findOneBy(['name' => $newTagEntity->getName()])
            ;

            if (null !== $existingTag) {
                // reuse existing tag
                $newCollection[$index] = $existingTag;
            } else {
                // create new tag
                $newCollection[$index] = $newTagEntity;
            }
        }

        return new PersistentCollection($this->entityManager, Tag::class, new ArrayCollection($newCollection));
    }
}
