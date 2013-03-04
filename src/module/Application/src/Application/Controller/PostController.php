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
        $post->user = $this->getUserTable()->getUser($session->user_id);
        
        $this->getPostTable()->savePost($post);
            
        return new JsonModel(array(
            'success'   =>true, 
            'post'      =>$post
        ));
    }
    
    public function pollAction() {
        $posts = array();
        $session = new Container('user');
        $user = $this->getUserTable()->getUser($session->user_id);
        
        foreach($this->getPostTable()->fetchAllFromFollowedUsers($user, $session->last_post_retrived) as $post) {
            $post->user = $this->getUserTable()->getUser($post->user_id);
            $posts[$post->id] = $post;
        }
        
        arsort($posts);
        $posts = array_slice($posts, 0, 5);
        $session->last_post_retrived = current($posts) ? current($posts)->id : $session->last_post_retrived;
          
        return new JsonModel(array(
            'posts'     => $posts
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
    
    public function getUserTable()
    {
        if (!$this->userTable) {
            $sm = $this->getServiceLocator();
            $this->userTable = $sm->get('Application\Model\UserTable');
        }
        return $this->userTable;
    }    
}
