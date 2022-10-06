<?php
namespace src\helpers;
use \src\models\User;
use \src\models\UserRelations;
use \src\helpers\PostHandler;

class UserHandler {
    
    public static function checkLogin() {
        if(!empty($_SESSION['token'])) {
            $token = $_SESSION['token'];

            $data = User::select()->where('token', $token)->one();
            if($data !== "" || null) {

                $loggedUser = new User();
                $loggedUser->id = $data['id'];
                $loggedUser->name = $data['name'];
                $loggedUser->avatar = $data['avatar'];

                return $loggedUser;
            }
        }

        return false;
    }

    public static function verifyLogin($email, $password) {
        $User = User::select()->where('email', $email)->one();

        if($User) {
            if(password_verify($password, $User['password'])) {
                $token = md5(time().rand(0,9999).time());

                User::update()->set('token', $token)->where('email', $email)->execute();
                return $token;
            }
        }

        return false;
    }

    public function idExists($id) {
        $User = User::select()->where('id', $id)->one();
        return $User ? true : false;
    }

    public function emailExists($email) {
        $User = User::select()->where('email', $email)->one();
        return $User ? true : false;
    }

    public function getUser($id, $full = false) {
        $data = User::select()->where('id', $id)->one();

        if($data) {
            $user = new User();
            $user->id = $data['id'];
            $user->name = $data['name'];
            $user->birthdate = $data['birthdate'];
            $user->city = $data['city'];
            $user->work = $data['work'];
            $user->avatar = $data['avatar'];
            $user->cover = $data['cover'];

            if($full) {
                $user->followers = [];
                $user->following = [];
                $user->photos = [];


                //followers
                $followers = UserRelations::select()->where('user_to', $id)->get();
                foreach($followers as $follower) {
                    $userData = User::select()->where('id',$follower['user_from'])->one();
                    $newUser = new User();
                    $newUser->id = $userData['id'];
                    $newUser->name = $userData['name'];
                    $newUser->avatar = $userData['avatar'];

                    $user->followers[] = $newUser;

                }

                //following
                $followings = UserRelations::select()->where('user_from', $id)->get();
                foreach($followings as $following) {
                    $userData = User::select()->where('id',$following['user_to'])->one();
                    $newUser = new User();
                    $newUser->id = $userData['id'];
                    $newUser->name = $userData['name'];
                    $newUser->avatar = $userData['avatar'];

                    $user->following[] = $newUser;

                }

                //photos

                $user->photos = PostHandler::getPhotosFrom($id);
                
            }

            return $user;
        }

        return false;
    }

    public function addUser($name, $email, $password, $birthdate) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $token =  md5(time().rand(0,9999).time());

        User::insert([
            'email' => $email,
            'password' => $hash,
            'name' => $name,
            'birthdate' => $birthdate,
            'token' => $token
        ])->execute();

        return $token;
    }

    public static function isFollowing($from, $to) {
        $data = UserRelations::select()
            ->where('user_from', $from)
            ->where('user_to', $to)
        ->one();

        if($data) {
            return true;
        }

        return false;
    }

    public static function follow($from, $to) {
        echo 'entrei  aqui '.$from.' '.$to;
        UserRelations::insert([
            'id' => null,
            'user_from' => $from,
            'user_to' => $to
        ])->execute();
        
    }

    public static function unfollow($from, $to) {
        UserRelations::delete()
            ->where('user_from', $from)
            ->where('user_to', $to)
        ->execute();
    }

}
