<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class FollowerTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function addFollower(User $user, User $follower) {
        $data = array(
            'user_id' => $user->id,
            'follower_user_id'  => $follower->id,
        );

        $rowset = $this->tableGateway->select(array(
            'user_id' => $user->id,
            'follower_user_id'  => $follower->id)
        );
        $row = $rowset->current();
        
        if (!$row) {
            $this->tableGateway->insert($data);
        } 
    }

    public function deleteFollower(User $user, User $follower) {
        $this->tableGateway->delete(array('user_id' => $user->id, 'follower_user_id', $follower->id));
    }
}