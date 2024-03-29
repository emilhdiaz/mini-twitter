<?php
namespace Application\Model;

class User
{
    public $id;
    public $email;
    public $password;
    public $gravatarID;
    
    public function exchangeArray($data)
    {
        $this->id       = (isset($data['id'])) ? $data['id'] : null;
        $this->email    = (isset($data['email'])) ? $data['email'] : null;
        $this->password = (isset($data['password'])) ? $data['password'] : null;
        $this->gravatarID = md5( strtolower( trim( $this->email ) ) );
    }
}
