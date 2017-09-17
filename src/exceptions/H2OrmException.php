<?php

namespace H2Orm\exceptions;

/**
 * General Exception raised by H2Orm app
 *
 * @author mateusz
 */
class H2OrmException extends \Exception
{

    public function __construct(string $message = '', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
