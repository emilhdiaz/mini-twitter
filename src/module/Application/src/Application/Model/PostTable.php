<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

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
            $select->order('id DESC')->limit(10);
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
            $this->tableGateway->insert($data);
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