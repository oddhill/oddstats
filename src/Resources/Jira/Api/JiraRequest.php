<?php
namespace OddStats\Resources\Jira\Api;

use OddStats\Resources\Jira\Auth\Auth;
use OddStats\Resources\Jira\Auth\AuthBasic;

class JiraRequest implements RequestInterface
{
  /**
   * Jira Request.
   *
   * @param   string $endpoint  Endpoint
   * @param   string $url   URL
   * @param   object|\OddStats\Resources\Jira\Auth\Auth   $auth   Authentication object
   * @param   string  $method Method
   * @param   array $data Data
   * @return  mixed
   */
  public function jRequest($endpoint, $url, Auth $auth, $method, $data = array())
  {
    if (!($auth instanceof AuthBasic)) {
      throw new \InvalidArgumentException(sprintf( 'JiraRequest södjer inte %s autentisering.', get_class($auth)));
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $endpoint . $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERPWD, sprintf('%s:%s', $auth->getUserId(), $auth->getUserPass()));
    $response = curl_exec( $ch);
    return $response;
  }
}