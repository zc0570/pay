<?php

declare(strict_types=1);

namespace Yansongda\Pay\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Yansongda\Pay\Event\ApiRequested;
use Yansongda\Pay\Event\ApiRequesting;
use Yansongda\Pay\Event\MethodCalled;
use Yansongda\Pay\Event\PayStarted;
use Yansongda\Pay\Event\PayStarting;
use Yansongda\Pay\Event\RequestReceived;
use Yansongda\Pay\Event\SignFailed;
use Yansongda\Pay\Logger;

class KernelLogSubscriber implements EventSubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            PayStarting::class => ['writePayStartingLog', 256],
            PayStarted::class => ['writePayStartedLog', 256],
            ApiRequesting::class => ['writeApiRequestingLog', 256],
            ApiRequested::class => ['writeApiRequestedLog', 256],
            SignFailed::class => ['writeSignFailedLog', 256],
            RequestReceived::class => ['writeRequestReceivedLog', 256],
            MethodCalled::class => ['writeMethodCalledLog', 256],
        ];
    }

    /**
     * writePayStartingLog.
     *
     * @author yansongda <me@yansongda.cn>
     */
    public function writePayStartingLog(PayStarting $event)
    {
        Logger::debug("Starting To $event->driver", [$event->gateway, $event->params]);
    }

    /**
     * writePayStartedLog.
     *
     * @author yansongda <me@yansongda.cn>
     */
    public function writePayStartedLog(PayStarted $event)
    {
        Logger::info(
            "$event->driver $event->gateway Has Started",
            [$event->endpoint, $event->payload]
        );
    }

    /**
     * writeApiRequestingLog.
     *
     * @author yansongda <me@yansongda.cn>
     */
    public function writeApiRequestingLog(ApiRequesting $event)
    {
        Logger::debug("Requesting To $event->driver Api", [$event->endpoint, $event->payload]);
    }

    /**
     * writeApiRequestedLog.
     *
     * @author yansongda <me@yansongda.cn>
     */
    public function writeApiRequestedLog(ApiRequested $event)
    {
        Logger::debug("Result Of $event->driver Api", $event->result);
    }

    /**
     * writeSignFailedLog.
     *
     * @author yansongda <me@yansongda.cn>
     */
    public function writeSignFailedLog(SignFailed $event)
    {
        Logger::warning("$event->driver Sign Verify FAILED", $event->data);
    }

    /**
     * writeRequestReceivedLog.
     *
     * @author yansongda <me@yansongda.cn>
     */
    public function writeRequestReceivedLog(RequestReceived $event)
    {
        Logger::info("Received $event->driver Request", $event->data);
    }

    /**
     * writeMethodCalledLog.
     *
     * @author yansongda <me@yansongda.cn>
     */
    public function writeMethodCalledLog(MethodCalled $event)
    {
        Logger::info("$event->driver $event->gateway Method Has Called", [$event->endpoint, $event->payload]);
    }
}
