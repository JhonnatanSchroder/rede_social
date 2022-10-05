<?php
namespace src\helpers;
use \src\models\Post;
use \src\models\User;
use \src\models\UserRelations;

class PostHandler {
    public static function addPost($idUser, $type, $body) {
        $body = trim($body);
        
        if(!empty($idUser) && !empty($body)) {
            Post::insert([
                'id_user' => $idUser,
                'type' => $type,
                'created_at' => date('y-m-d h:i:s'),
                'body' => $body
            ])->execute();
        }
    }

    public function _postListToObject($postList, $loggedUserId) {
        $posts = [];
        foreach($postList as $postItem) {
            $newPost = new Post();
            $newPost->id = $postItem['id'];
            $newPost->type = $postItem['type'];
            $newPost->created_at = $postItem['created_at'];
            $newPost->body = $postItem['body'];
            $newPost->mine = false;

            if($postItem['id_user'] == $loggedUserId) {
                $newPost->mine = true;
            }

            $newUser = User::select()->where('id', $postItem['id_user'])->one();
            $newPost->user = new User();
            $newPost->user->id = $newUser['id'];
            $newPost->user->name = $newUser['name'];
            $newPost->user->avatar = $newUser['avatar'];


            $newPost->likesCount = 0;
            $newPost->liked = false;

            $newPost->comments = [];

            $posts[] = $newPost;
        }

        return $posts;
    }

    public static function getHomeFeed($idUser, $page) {
        $perPage = 4;

        $usersList = UserRelations::select()->where('user_from', $idUser)->get();
        $users = [];
        foreach($usersList as $userItem) {
            $users[] = $userItem['user_to'];
        }
        $users[] = $idUser;

        $postList = Post::select()
            ->where('id_user', 'in', $users)
            ->orderBy('created_at', 'desc')
            ->page($page, 4)
        ->get();

        $total = Post::select()
            ->where('id_user', 'in', $users)
        ->count();
        $pageCount = ceil($total / $perPage);

        $posts = self::_postListToObject($postList, $idUser);
        
        return [
            'posts' => $posts,
            'pageCount' => $pageCount,
            'currentPage' => $page
        ];


    }

    public static function getUserFeed($idUser, $page, $loggedUserId) {
        $perPage = 4;

        $postList = Post::select()
            ->where('id_user', $idUser)
            ->orderBy('created_at', 'desc')
            ->page($page, 4)
        ->get();

        $total = Post::select()
            ->where('id_user', $idUser)
        ->count();
        $pageCount = ceil($total / $perPage);

        $posts = self::_postListToObject($postList, $idUser);
        
        return [
            'posts' => $posts,
            'pageCount' => $pageCount,
            'currentPage' => $page
        ];
    }

    public static function getPhotosFrom($idUser) {
        $photosData = Post::select()
            ->where('id_user',$idUser)
            ->where('type', 'photo')
        ->get();

        $photos = [];

        foreach($photosData as $photo) {
            $newPost = new Post();
            $newPost->id = $photo['id'];
            $newPost->created_at = $photo['created_at'];
            $newPost->body = $photo['body'];

            $photos[] = $newPost;
        }


        return $photos;
    }
}