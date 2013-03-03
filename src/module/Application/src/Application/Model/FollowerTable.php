<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class FollowerTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAllFollowers(User $user) {
        $resultSet = $this->tableGateway->select(array('user_id' => $user->id));
        return $resultSet;
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

    public function deleteFollower($user_id, $follower_user_id) {
        $this->tableGateway->delete(array('user_id' => $user_id, 'follower_user_id', $follower_user_id));
    }
}