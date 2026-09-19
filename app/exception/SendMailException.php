<?php

namespace App\exception;
use Exception;
use Throwable;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Custom Exception class
 */
class SendMailException extends Exception
{
    public function __construct( $message, $code = 0, Throwable $previous = null ) {
        parent::__construct( $message, $code, $previous );
    }
}