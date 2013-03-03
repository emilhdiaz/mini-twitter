<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class UserController extends AbstractActionController
{
    protected $userTable;
    protected $followerTable;
            
    public function followAction() {
        $session = new Container('user'); 
        $user = $this->getUserTable()->getUser($session->user_id);
        
        $followed_user_id = $this->getParam('followed_user_id');
        $followed_user = $this->getUserTable()->getUser($followed_user_id);
        
        $this->getFollowerTable()->addFollower($followed_user, $user);
        
        return true;
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
