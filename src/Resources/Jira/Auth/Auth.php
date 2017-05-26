<?php

namespace OddStats\Resources\Jira\Auth;

class Auth
{
  protected $userId;

  protected $password;

  /**
   * Auth constructor.
   *
   * @param string $user_id
   * @param string $password
   */
  public function __construct($user_id, $password)
  {
    $this->userId = $user_id;
    $this->password = $password;
  }

  /**
   * Return user id.
   *
   * @return string
   */
  public function getUserId()
  {
    return $this->userId;
  }

  /**
   * Return password.
   *
   * @return string
   */
  public function getUserPass()
  {
    return $this->password;
  }
}