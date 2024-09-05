<?php

namespace App\Exception;

class NonRemoveAbleEntity extends \LogicException
{
    public function __construct(object $entity)
    {
        $message = sprintf('Entity of type "%s" does non-removeable.', get_class($entity));
        parent::__construct($message);
    }
}
