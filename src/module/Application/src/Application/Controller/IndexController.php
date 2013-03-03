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
use Zend\Session\Container; 

class IndexController extends AbstractActionController
{
    protected $postTable;
    protected $userTable;
    
    public function indexAction()
    {
        $session = new Container('user');
        
        if(!$session->user_id) {
            $this->redirect()->toRoute('login');
        }
        
        $user = $this->getUserTable()->getUser($session->user_id);
        
        return new ViewModel(array(
            'posts' => $this->getPostTable()->fetchAllByUser($user),
            'username' => $user->email,
            'gravatar' => md5( strtolower( trim( $user->email ) ) )
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
            if( $user->password != $password ){
                return new ViewModel(array(
                   'error' => 'Invalid login, please try again' 
                ));
            }
            $session = new Container('user');
            $session->user_id = $user->id;
            $this->redirect()->toRoute('application');
        }
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
