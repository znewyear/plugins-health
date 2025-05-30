<?php

namespace Health\Bridge\Laminas;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Laminas\Db\Adapter\Adapter;
use Carbon\Carbon;

class HealthLogMiddleware implements MiddlewareInterface
{
    protected $db;
    protected $table;

    public function __construct(Adapter $db, $table = 'health_call_logs')
    {
        $this->db = $db;
        $this->table = $table;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $start = microtime(true);
        $response = $handler->handle($request);
        $latency = round((microtime(true) - $start) * 1000, 2);

        $uri = $request->getUri()->getPath();
        $method = $request->getMethod();
        $params = $request->getParsedBody() ?: [];
        $status = method_exists($response, 'getStatusCode') ? $response->getStatusCode() : 0;
        $user = null; // 可集成 RBAC
        $ip = $request->getServerParams()['REMOTE_ADDR'] ?? '';

        $sql = sprintf(
            "INSERT INTO %s (service, status, latency, message, user, ip, checked_at) VALUES (?, ?, ?, ?, ?, ?, ?)",
            $this->table
        );
        $this->db->query($sql, [
            $uri,
            $status,
            $latency,
            json_encode(['method' => $method, 'params' => $params]),
            $user,
            $ip,
            Carbon::now()->toDateTimeString(),
        ]);

        return $response;
    }
}
