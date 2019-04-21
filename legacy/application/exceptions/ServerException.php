<?php
namespace Application\Exceptions;

/**
 * Server-side error, programming error and other errors
 * that weren't caused by a client.
 */
class ServerException extends BaseHttpException
{
    protected $status = 500;
}