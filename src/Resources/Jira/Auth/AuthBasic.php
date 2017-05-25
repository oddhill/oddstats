<?php

namespace OddStats\Resources\Jira\Auth;

class AuthBasic extends Auth
{
 public function getCredential()
 {
   return base64_encode( $this->userId . ':' . $this->password);
 }
}