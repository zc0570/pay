<?php

declare(strict_types=1);

namespace Yansongda\Pay\Plugin\Wechat;

use Closure;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\ServerRequestInterface;
use Yansongda\Pay\Contract\PluginInterface;
use Yansongda\Pay\Exception\InvalidParamsException;
use Yansongda\Pay\Logger;
use Yansongda\Pay\Parser\NoHttpRequestParser;
use Yansongda\Pay\Rocket;
use Yansongda\Supports\Collection;

class CallbackPlugin implements PluginInterface
{
    /**
     * @throws \Yansongda\Pay\Exception\ContainerDependencyException
     * @throws \Yansongda\Pay\Exception\ContainerException
     * @throws \Yansongda\Pay\Exception\InvalidConfigException
     * @throws \Yansongda\Pay\Exception\ServiceNotFoundException
     * @throws \Yansongda\Pay\Exception\InvalidResponseException
     * @throws \Yansongda\Pay\Exception\InvalidParamsException
     */
    public function assembly(Rocket $rocket, Closure $next): Rocket
    {
        Logger::info('[wechat][CallbackPlugin] 插件开始装载', ['rocket' => $rocket]);

        $this->assertRequestAndParams($rocket);

        /* @phpstan-ignore-next-line */
        verify_wechat_sign($rocket->getDestinationOrigin(), $rocket->getParams());

        $body = json_decode($rocket->getDestination()->getBody()->getContents(), true);

        $rocket->setDirection(NoHttpRequestParser::class)->setPayload($body);

        $body['resource'] = decrypt_wechat_resource($body['resource'] ?? [], $rocket->getParams());

        $rocket->setDestination(new Collection($body));

        Logger::info('[wechat][CallbackPlugin] 插件装载完毕', ['rocket' => $rocket]);

        return $next($rocket);
    }

    /**
     * @throws \Yansongda\Pay\Exception\InvalidParamsException
     */
    protected function assertRequestAndParams(Rocket $rocket): void
    {
        $request = $rocket->getParams()['request'] ?? null;

        if (is_null($request) || !($request instanceof ServerRequestInterface)) {
            throw new InvalidParamsException(InvalidParamsException::REQUEST_NULL_ERROR);
        }

        $contents = $request->getBody()->getContents();

        $rocket->setDestination($request->withBody(Utils::streamFor($contents)))
            ->setDestinationOrigin($request->withBody(Utils::streamFor($contents)))
            ->setParams($rocket->getParams()['params'] ?? []);
    }
}
