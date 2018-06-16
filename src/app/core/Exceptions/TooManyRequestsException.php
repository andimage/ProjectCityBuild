<?php
namespace App\Core\Exceptions;

/**
 * Too many requests sent in a period of time
 */
class TooManyRequestsException extends BaseHttpException {

    protected $status = 429;

}