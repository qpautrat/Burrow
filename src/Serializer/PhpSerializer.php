<?php

namespace Burrow\Serializer;

use Assert\Assertion;
use Burrow\Serializer;

class PhpSerializer implements Serializer
{
    /**
     * @param mixed $message
     *
     * @return string
     */
    public function serialize($message)
    {
        return serialize($message);
    }

    /**
     * @param string $message
     *
     * @return mixed
     */
    public function deserialize($message)
    {
        if ($message === null) {
            return null;
        }

        Assertion::string($message, 'The message to deserialize must be a valid string');

        return unserialize($message);
    }
}
