<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Predicate\Operator;

class UserTable
{
    protected $tableGateway;
    protected $cache = array();

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
    
    public function fetchAllFollowersOf(User $user) {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($user) {
            $subquery = new Select();
            $subquery->from('follower');
            $subquery->columns(array('follower_user_id'));
            $subquery->where(array('user_id'=>$user->id));
            
            $select->join(
                array('f'=>$subquery),
                'user.id = f.follower_user_id',
                array()
            );
        });
        return $resultSet;
    }
    
    public function fetchAllFollowedBy(User $user) {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($user) {
            $subquery = new Select();
            $subquery->from('follower');
            $subquery->columns(array('user_id'));
            $subquery->where(array('follower_user_id'=>$user->id));
            
            $select->join(
                array('f'=>$subquery), 
                'user.id = f.user_id',
                array()
            );
        });
        return $resultSet;
    }    
    
    public function fetchAllNotFollowedBy(User $user) {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($user) {
            $subquery = new Select();
            $subquery->from('follower');
            $subquery->columns(array('user_id'));
            $subquery->where(array('follower_user_id'=>$user->id));
            
            $select->join(
                array('f'=>$subquery), 
                'user.id = f.user_id',
                array(),
                $select::JOIN_LEFT
            );
            
            $select->where(function (Where $where) use($user) {
                $where->isNull('f.user_id');
                $where->addPredicate(new Operator('id', Operator::OPERATOR_NOT_EQUAL_TO, $user->id));
            });
            $select->limit(10);
        });            
        return $resultSet;
    }        

    public function getUser($id)
    {
        $id  = (int) $id;
        if( !array_key_exists($id, $this->cache) ) {
            $rowset = $this->tableGateway->select(array('id' => $id));
            $row = $rowset->current();
            if (!$row) {
                throw new \Exception("Could not find row $id");
            }
            $this->cache[$id] = $row;
        }
        return $this->cache[$id];
    }
    
    public function getUserByEmail($email)
    {
        $email  = (string) $email;
        $rowset = $this->tableGateway->select(array('email' => $email));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $email");
        }
        return $row;
    }    

    public function saveUser(User $user)
    {
        $data = array(
            'email' => $user->email,
            'password'  => $user->password,
        );

        $id = (int)$user->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getUser($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('User id does not exist');
            }
        }
    }

    public function deleteUser($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }
}