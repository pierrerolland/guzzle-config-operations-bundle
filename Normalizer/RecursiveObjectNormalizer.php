<?php

namespace Guzzle\ConfigOperationsBundle\Normalizer;

use Doctrine\Common\Annotations\AnnotationReader;
use Guzzle\ConfigOperationsBundle\Normalizer\Annotation\Type;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
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
     * @var \ReflectionClass
     */
    private $reflectionClass;

    /**
     * @param ClassMetadataFactoryInterface|null $classMetadataFactory
     * @param NameConverterInterface|null $nameConverter
     * @param PropertyAccessorInterface|null $propertyAccessor
     * @param PropertyTypeExtractorInterface|null $propertyTypeExtractor
     */
    public function __construct(
        ClassMetadataFactoryInterface $classMetadataFactory = null,
        NameConverterInterface $nameConverter = null,
        PropertyAccessorInterface $propertyAccessor = null,
        PropertyTypeExtractorInterface $propertyTypeExtractor = null
    ) {
        parent::__construct($classMetadataFactory, $nameConverter, $propertyTypeExtractor);
        $this->annotationReader = new AnnotationReader();
    }

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
        $this->reflectionClass = $reflectionClass;
        return parent::instantiateObject($data, $class, $context, $reflectionClass, $allowedAttributes);
    }

    /**
     * {@inheritdoc}
     */
    protected function setAttributeValue($object, $attribute, $value, $format = null, array $context = [])
    {
        try {
            if ($this->reflectionClass->hasProperty($attribute)) {
                $reflectionProperty = $this->reflectionClass->getProperty($attribute);
                if ($type = $this->annotationReader->getPropertyAnnotation($reflectionProperty, Type::class)) {
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
}
