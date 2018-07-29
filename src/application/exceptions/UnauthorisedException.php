<?php
namespace Application\Exceptions;

/**
 * Authorisation required but failed and/or has not
 * been provided.
 */
class UnauthorisedException extends BaseHttpException
{
    protected $status = 401;
}