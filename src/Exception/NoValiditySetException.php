<?php

namespace App\Exception;

use Symfony\Component\Uid\Ulid;

class NoValiditySetException extends \RuntimeException
{
    public function __construct(?Ulid $entityId, string $entityClass)
    {
        $msg = 'No validity set found for "'.$entityClass.'"';
        if ($entityId instanceof Ulid) {
            $msg .= ' ID "'.$entityId.'"';
        } else {
            $msg .= ' (new entity)';
        }
        parent::__construct($msg);
    }
}
