<?php

namespace Yansongda\Pay\Exception;

use Throwable;

class GatewayServiceException extends ServiceException
{
    /**
     * Bootstrap.
     *
     * @param string          $message
     * @param int             $code
     * @param array           $raw
     * @param \Throwable|null $previous
     */
    public function __construct($message = 'Gateway Service Exception!', $code = self::GATEWAY_SERVICE, $raw = [], Throwable $previous = null)
    {
        parent::__construct($message, $code, $raw, $previous);
    }
}