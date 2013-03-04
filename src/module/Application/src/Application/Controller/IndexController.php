<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\SessionManager;
use Zend\Session\Container; 

class IndexController extends AbstractActionController
{
    protected $postTable;
    protected $userTable;
    protected $followerTable;
    
    public function indexAction()
    {
        $session = new Container('user');
        
        if(!$session->user_id) {
            $this->redirect()->toRoute('login');
        }
        
        $user = $this->getUserTable()->getUser($session->user_id);
        $posts = array();
        $following = array();
        $notFollowing = array();
        
        foreach($this->getPostTable()->fetchAllByUser($user) as $post) {
            $post->user = $user;
            $posts[$post->id] = $post;
        }        
        
        foreach($this->getPostTable()->fetchAllFromFollowedUsers($user) as $post) {
            $post->user = $this->getUserTable()->getUser($post->user_id);
            $posts[$post->id] = $post;
        }
        
        arsort($posts);
        $posts = array_slice($posts, 0, 5);
        $session->last_post_retrived = current($posts) ? current($posts)->id : 0;
                      
        return new ViewModel(array(
            'posts'         => $posts,
            'following'     => $this->getUserTable()->fetchAllFollowedBy($user),
            'notFollowing'  => $this->getUserTable()->fetchAllNotFollowedBy($user),
            'username'      => $user->email,
            'gravatar'      => $user->gravatarID
        ));
    }
    
    public function loginAction() 
    {
        $email = $this->getRequest()->getPost('email');
        $password = $this->getRequest()->getPost('password');
                
        if(!$email) {
            return new ViewModel();
        } else {
            $user = $this->getUserTable()->getUserByEmail($email);
            if( $user->password != md5($password) ){
                return new ViewModel(array(
                   'error' => 'Invalid login, please try again' 
                ));
            }
            $session = new Container('user');
            $session->user_id = $user->id;
            $this->redirect()->toRoute('application');
        }
    }
    
    public function logoutAction() 
    {
            $session = new SessionManager();    
            $session->destroy();
            $this->redirect()->toRoute('login');
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
    
    public function getFollowerTable()
    {
        if (!$this->followerTable) {
            $sm = $this->getServiceLocator();
            $this->followerTable = $sm->get('Application\Model\FollowerTable');
        }
        return $this->followerTable;
    }    
}
