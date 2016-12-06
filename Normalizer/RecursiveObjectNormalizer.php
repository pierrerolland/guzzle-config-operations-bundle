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
        $allowedAttributes
    ) {
        $this->reflectionClasses[$class] = $reflectionClass;
        return parent::instantiateObject($data, $class, $context, $reflectionClass, $allowedAttributes);
    }

    /**
     * {@inheritdoc}
     */
    protected function setAttributeValue($object, $attribute, $value, $format = null, array $context = [])
    {
        try {
            if ($this->reflectionClasses[get_class($object)]->hasProperty($attribute)) {
                $reflectionProperty = $this->reflectionClasses[get_class($object)]->getProperty($attribute);
                if ($type = $this->getAnnotationReader()->getPropertyAnnotation($reflectionProperty, Type::class)) {
                    if (substr($type->name, -2) === '[]' && is_array($value)) {
                        $denormalized = [];
                        $class = substr($type->name, 0, -2);
                        foreach ($value as $item) {
                            $denormalized[] = $this->denormalize($item, $class, $format, $context);
                        }
                        $this
                            ->propertyAccessor
                            ->setValue($object, $attribute, $denormalized);
                        return;
                    }
                    $denormalized = $this->denormalize($value, $type->name, $format, $context);
                    $this
                        ->propertyAccessor
                        ->setValue($object, $attribute, $denormalized);
                    return;
                }
            }
            $this->propertyAccessor->setValue($object, $attribute, $value);
        } catch (NoSuchPropertyException $exception) {
            // Properties not found are ignored
        }
    }

    /**
     * @return AnnotationReader
     */
    public function getAnnotationReader()
    {
        if (null === $this->annotationReader) {
            $this->annotationReader = new AnnotationReader();
        }

        return $this->annotationReader;
    }

    /**
     * @param AnnotationReader $annotationReader
     */
    public function setAnnotationReader(AnnotationReader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }
}
