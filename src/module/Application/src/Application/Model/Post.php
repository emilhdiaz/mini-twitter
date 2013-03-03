<?php
namespace Application\Model;

class Post
{
    public $id;
    public $user_id;
    public $body;
    
    public function exchangeArray($data)
    {
        $this->id       = (isset($data['id'])) ? $data['id'] : null;
        $this->user_id  = (isset($data['user_id'])) ? $data['user_id'] : null;
        $this->body     = (isset($data['body'])) ? $data['body'] : null;
    }
}
