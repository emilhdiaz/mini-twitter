<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;

class PostTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAllByUser(User $user) {
        $resultSet = $this->tableGateway->select(function (Select $select) use($user) {
            $select->where(array('user_id' => $user->id));
            $select->order('id DESC')->limit(5);
        });
        return $resultSet;
    }
    
    public function fetchAllFromFollowedUsers(User $user, $since = 0) {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($user, $since) {
            $select->join('follower', 'follower.user_id = post.user_id');
            
            $select->where(function (Where $where) use($user, $since) {
                $where->equalTo('follower.follower_user_id', $user->id);
                $where->greaterThan('id',$since);
            });
            $select->order('id DESC')->limit(5);
        });
        
        return $resultSet;
    }    

    public function getPost($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function savePost(Post $post)
    {
        $data = array(
            'user_id' => $post->user_id,
            'body'  => $post->body,
        );

        $id = (int)$post->id;
        if ($id == 0) {
            $post->id = $this->tableGateway->insert($data);
        } else {
            if ($this->getPost($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Post id does not exist');
            }
        }
    }

    public function deletePost($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }
}