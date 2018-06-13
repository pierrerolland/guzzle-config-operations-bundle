<?php

namespace Guzzle\ConfigOperationsBundle\Normalizer;

use Doctrine\Common\Annotations\AnnotationReader;
use Guzzle\ConfigOperationsBundle\Normalizer\Annotation\Type;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * @author Pierre Rolland <roll.pierre@gmail.com>
 */
class RecursiveObjectNormalizer extends ObjectNormalizer
{
    /**
     * @var AnnotationReader
     */
    private $annotationReader;

    /**
     * @var \ReflectionClass[]
     */
    private $reflectionClasses;

    /**
     * {@inheritdoc}
     */
    protected function instantiateObject(
        array &$data,
        $class,
        array &$context,
        \ReflectionClass $reflectionClass,
        $allowedAttributes,
        string $format = null
    ) {
        $this->reflectionClasses[$class] = $reflectionClass;
        return parent::instantiateObject($data, $class, $context, $reflectionClass, $allowedAttributes, $format);
    }

    /**
     * {@inheritdoc}
     */
    protected function setAttributeValue($object, $attribute, $value, $format = null, array $context = [])
    {
        try {
            if ($this->hasProperty($object, $attribute) && ($type = $this->getTypeAnnotation($object, $attribute))) {
                $value = $this->getDenormalizedValue($type, $value, $format, $context);
            }
            $this->propertyAccessor->setValue($object, $attribute, $value);
        } catch (NoSuchPropertyException $exception) {
            // Properties not found are ignored
        }
    }

    protected function getDenormalizedValue(Type $type, $value, ?string $format, array $context)
    {
        if (substr($type->name, -2) === '[]' && is_array($value)) {
            $denormalized = [];
            $class = substr($type->name, 0, -2);
            foreach ($value as $item) {
                $denormalized[] = $this->denormalize($item, $class, $format, $context);
            }

            return $denormalized;
        }

        return $this->denormalize($value, $type->name, $format, $context);
    }

    protected function getTypeAnnotation($object, string $attribute): ?Type
    {
        return $this->getAnnotationReader()->getPropertyAnnotation(
            $this->reflectionClasses[get_class($object)]->getProperty($attribute),
            Type::class
        );
    }

    protected function hasProperty($object, string $attribute): bool
    {
        return $this->reflectionClasses[get_class($object)]->hasProperty($attribute);
    }

    public function getAnnotationReader(): AnnotationReader
    {
        if (null === $this->annotationReader) {
            $this->annotationReader = new AnnotationReader();
        }

        return $this->annotationReader;
    }

    public function setAnnotationReader(AnnotationReader $annotationReader): void
    {
        $this->annotationReader = $annotationReader;
    }
}
