<?php
namespace Application\Model;

class Follower
{
    public $user_id;
    public $follower_user_id;
    
    public function exchangeArray($data)
    {
        $this->user_id  = (isset($data['user_id'])) ? $data['user_id'] : null;
        $this->follower_user_id  = (isset($data['follower_user_id'])) ? $data['follower_user_id'] : null;
    }
}
