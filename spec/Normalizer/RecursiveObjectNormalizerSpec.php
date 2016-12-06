<?php

namespace spec\Guzzle\ConfigOperationsBundle\Normalizer;

use Doctrine\Common\Annotations\AnnotationReader;
use Guzzle\ConfigOperationsBundle\Normalizer\Annotation\Type;
use Guzzle\ConfigOperationsBundle\Normalizer\RecursiveObjectNormalizer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class RecursiveObjectNormalizerSpec extends ObjectBehavior
{
    function let(
        ClassMetadataFactoryInterface $classMetadataFactory,
        NameConverterInterface $nameConverter,
        PropertyAccessorInterface $propertyAccessor,
        PropertyTypeExtractorInterface $propertyTypeExtractor,
        AnnotationReader $annotationReader
    ) {
        $this->beConstructedWith($classMetadataFactory, $nameConverter, $propertyAccessor, $propertyTypeExtractor);
        $this->setAnnotationReader($annotationReader);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RecursiveObjectNormalizer::class);
    }

    function it_has_an_annotation_reader_mutator(AnnotationReader $annotationReader2)
    {
        $this->setAnnotationReader($annotationReader2);
        $this->getAnnotationReader()->shouldReturn($annotationReader2);
    }

    function its_denormalize_should_handle_type_annotation(
        $nameConverter,
        $propertyAccessor,
        $annotationReader,
        Type $tagsType,
        Type $categoryType
    ) {
        $tagsType->name = Tag::class . '[]';
        $categoryType->name = Category::class;
        /* @var NameConverterInterface $nameConverter*/
        $nameConverter->denormalize('category')->willReturn('category');
        $nameConverter->denormalize('tags')->willReturn('tags');
        $nameConverter->denormalize('name')->willReturn('name');
        $nameConverter->denormalize(0)->willReturn(0);
        $nameConverter->denormalize(1)->willReturn(1);
        /* @var AnnotationReader $annotationReader */
        $annotationReader
            ->getPropertyAnnotation(Argument::type(\ReflectionProperty::class), Type::class)
            ->willReturn($categoryType, null, $tagsType, null, null)
            ->shouldBeCalledTimes(5);

        $propertyAccessor
            ->setValue(Argument::type(Article::class), 'category', Argument::type(Category::class))
            ->shouldBeCalled()
        ;
        $propertyAccessor
            ->setValue(Argument::type(Category::class), 'name', 'Category')
            ->shouldBeCalled()
        ;
        $propertyAccessor
            ->setValue(Argument::type(Tag::class), 'name', 'Tag 1')
            ->shouldBeCalled()
        ;
        $propertyAccessor
            ->setValue(Argument::type(Tag::class), 'name', 'Tag 2')
            ->shouldBeCalled()
        ;
        $propertyAccessor
            ->setValue(Argument::type(Article::class), 'tags', Argument::allOf(
                Argument::type('array'),
                Argument::containing(Argument::type(Tag::class)),
                Argument::size(2)
            ))
            ->shouldBeCalled()
        ;

        $this->denormalize([
            'category' => [
                'name' => 'Category'
            ],
            'tags' => [
                [
                    'name' => 'Tag 1'
                ],
                [
                    'name' => 'Tag 2'
                ]
            ]
        ], Article::class);
    }
}

class Article
{
    /**
     * @var Category
     *
     * @Type(name="spec\Guzzle\ConfigOperationsBundle\Normalizer\Category")
     */
    private $category;

    /**
     * @var Tag[]
     *
     * @Type(name="spec\Guzzle\ConfigOperationsBundle\Normalizer\Tag[]")
     */
    private $tags;

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return Tag[]
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param Tag[] $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }
}

class Category
{
    /**
     * @var string
     */
    private $name;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}

class Tag
{
    /**
     * @var string
     */
    private $name;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}
