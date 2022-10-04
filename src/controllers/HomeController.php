<?php
namespace src\controllers;

use \core\Controller;
use \src\helpers\UserHandler;
use \src\helpers\PostHandler;

class HomeController extends Controller {
    private $loggedUser;

    public function __construct() {
        $this->loggedUser = UserHandler::checkLogin();
        if( $this->loggedUser === false) {
            $this->redirect('/login');
        }    
    }

    public function index() {
        $page = intval(filter_input(INPUT_GET, 'page'));

        // if();

        $feed = PostHandler::getHomeFeed(
            $this->loggedUser->id,
            $page
        );

        $this->render('home',[
            'loggedUser' => $this->loggedUser,
            'feed' => $feed,
        ]
    );
    }

}