<?php

namespace src\helpers;

use \core\Database;
use \src\Config;
use \src\models\User;
use \src\helpers\PostHandler;

class UserRelationsInsert {
    public static function follow($from, $to) {
        $pdo = Database::getInstance();
        $sql = $pdo->prepare("INSERT INTO userrelations (id, user_from, user_to) VALUES (null, :user_from, :user_to)");
        $sql->bindValue(':user_from', $from);
        $sql->bindValue(':user_to', $to);
        $sql->execute();
    }

    public static function unfollow($from, $to) {
        $pdo = Database::getInstance();
        $sql = $pdo->prepare("DELETE FROM userrelations WHERE `user_from` = $from AND `user_to` = $to");
        $sql->execute();
        
    }

    public static function isFollowing($from, $to) {
        $pdo = Database::getInstance();
        
        $data = $pdo->query("SELECT * FROM `userrelations` WHERE `user_from` = $from AND `user_to` = $to");
        
        if($data->rowCount() != 0) {
            return true;
        }

        return false;
    }

    public function getUser($id, $full = false) {
        $pdo = Database::getInstance();

        $data = [];
        $sql = $pdo->query("SELECT * FROM users WHERE id = $id");
        if($sql->rowCount() != 0) {
            $data = $sql->fetchAll(\PDO::FETCH_ASSOC);
            $user = new User();
            $user->id = $data[0]['id'];
            $user->name = $data[0]['name'];
            $user->birthdate = $data[0]['birthdate'];
            $user->city = $data[0]['city'];
            $user->work = $data[0]['work'];
            $user->avatar = $data[0]['avatar'];
            $user->cover = $data[0]['cover'];

            if($full) {
                $user->followers = [];
                $user->following = [];
                $user->photos = [];


                //followers
                $followers = [];
                $sql = $pdo->query("SELECT * FROM userrelations WHERE user_to = $id");
                $followers = $sql->fetchAll(\PDO::FETCH_ASSOC);

                foreach($followers as $follower) {
                    $userData = [];
                    
                    $idUser = $follower['user_from'];
                    $sql = $pdo->query("SELECT * FROM users WHERE id = ".$follower['user_from']);
                    $userData = $sql->fetchAll(\PDO::FETCH_ASSOC);

                    $newUser = new User();
                    $newUser->id = $userData[0]['id'];
                    $newUser->name = $userData[0]['name'];
                    $newUser->avatar = $userData[0]['avatar'];

                    $user->followers[] = $newUser;
                }


                //following
                $followings = [];
                $sql = $pdo->query("SELECT * FROM userrelations WHERE user_from = $id");
                $followings = $sql->fetchAll(\PDO::FETCH_ASSOC);
                
                foreach($followings as $following) {
                    $userData = [];
                    $sql = $pdo->query("SELECT * FROM users WHERE id = ".$following['user_to']);
                    $userData = $sql->fetchAll(\PDO::FETCH_ASSOC);

                    $newUser = new User();
                    $newUser->id = $userData[0]['id'];
                    $newUser->name = $userData[0]['name'];
                    $newUser->avatar = $userData[0]['avatar'];

                    $user->following[] = $newUser;

                }

                //photos
                $user->photos = PostHandler::getPhotosFrom($id);
                
            }

            return $user;
        }

        return false;
    }
}