<?php
namespace src\controllers;

use \core\Controller;
use \src\helpers\UserHandler;
use \src\helpers\PostHandler;
use \src\helpers\UserRelationsInsert;
use \src\models\User;



class ProfileController extends Controller {
    private $loggedUser;

    public function __construct() {
        $this->loggedUser = UserHandler::checkLogin();
        if( $this->loggedUser === false) {
            $this->redirect('/login');
        }    
    }

    public function index($atts = []) {
        $page = intval(filter_input(INPUT_GET, 'page'));

        $id = $this->loggedUser->id;
        if(!empty($atts['id'])) {
            $id = $atts['id'];
        }

        $user = UserRelationsInsert::getUser($id, true);
        if(!$user) {
            $this->redirect('/');
        }

        $dataFrom = new \DateTime($user->birthdate);
        $dataTo = new \DateTime('today');
        $user->ageYears = $dataFrom->diff($dataTo)->y;

        $feed = PostHandler::getUserFeed($id, $page, $this->loggedUser->id);

        $isFollowing = false;
        if($user->id != $this->loggedUser->id) {
            $isFollowing = UserRelationsInsert::isFollowing($this->loggedUser->id, $user->id);
        }

       $this->render('profile', [
        'loggedUser' => $this->loggedUser,
        'user' => $user,
        'feed' => $feed,
        'isFollowing' => $isFollowing
       ]);
    }

    public function follow($atts) {
        $to = intval($atts['id']);        

        echo "$to and {$this->loggedUser->id}";
        
        if(UserHandler::idExists($to)) {
            
            if(UserRelationsInsert::isFollowing($this->loggedUser->id, $to)) {
                echo 'entrei no if';
            
            UserRelationsInsert::unfollow($this->loggedUser->id, $to);
            } else {
                echo 'entrei no else';
            UserRelationsInsert::follow($this->loggedUser->id, $to);
            }
        }
 
        $this->redirect("/perfil/$to");
 
     }

     public function friends($atts = []) {
        $id = $this->loggedUser->id;
        if(!empty($atts['id'])) {
            $id = $atts['id'];
        }

        $user = UserRelationsInsert::getUser($id, true);
        if(!$user) {
            $this->redirect('/');
        }

        $dataFrom = new \DateTime($user->birthdate);
        $dataTo = new \DateTime('today');
        $user->ageYears = $dataFrom->diff($dataTo)->y;

        $isFollowing = false;
        if($user->id != $this->loggedUser->id) {
            $isFollowing = UserRelationsInsert::isFollowing($this->loggedUser->id, $user->id);
        }

        $this->render('profile_friends', [
            'loggedUser' => $this->loggedUser,
            'user' => $user,
            'isFollowing' => $isFollowing
           ]);
     }

     public function teste() {
        $this->render('amigos');
     }

}