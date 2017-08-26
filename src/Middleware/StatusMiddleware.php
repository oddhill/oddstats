<?php

namespace OddStats\Middleware;
use Psr\Http\Message\ServerRequestInterface;
class StatusMiddleware extends Middleware
{
  public function __invoke($req, $res, $next)
  {
    if ($res->getStatusCode() !== 200) {
      return $res->withRedirect('www.google.se');
    }
    $res = $next($req, $res);
    return $res;
  }
}