<?php

namespace Guzzle\ConfigOperationsBundle\Normalizer\Annotation;

/**
 * Type of the property should be denormalized to
 *
 * @author Pierre Rolland <roll.pierre@gmail.com>
 *
 * @Annotation
 * @Target("PROPERTY")
 */
class Type
{
    /**
     * Type name
     *
     * @var string
     */
    public $name;
}
