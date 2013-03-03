<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Application\Model\Post;
use Zend\Session\Container;

class PostController extends AbstractActionController
{
    protected $userTable;
    protected $postTable;
        
    public function saveAction() {
        $post = new Post();
        
        $session = new Container('user');
        $post->user_id = $session->user_id;    
        $post->body = $this->getRequest()->getPost('body');
        
        $this->getPostTable()->savePost($post);
            
        return new JsonModel(array(
            'success'=>true,
            'body'=>$post->body
        ));
    }
            
    public function getPostTable()
    {
        if (!$this->postTable) {
            $sm = $this->getServiceLocator();
            $this->postTable = $sm->get('Application\Model\PostTable');
        }
        return $this->postTable;
    } 
}
