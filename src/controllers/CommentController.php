<?php

namespace App\Controllers;

use App\Exceptions\CommentNotSavedException;
use App\Models\Comment;

class CommentController
{

    protected $commentModel;

    public function __construct(Comment $commentModel)
    {
        $this->commentModel = $commentModel;
    }

    public function create()
    {
        if (!isset($_SESSION['AUTHENTICATED'])) {
            header("Location: /");
            exit;
        }

        //validation??

        $this->commentModel->create([
            'story_id'   => $_POST['story_id'],
            'created_by' => $_SESSION['username'],
            'comment'    => filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
        ]);

        //catch CommentNotSavedException and show an error

        header("Location: /story/?id=" . $_POST['story_id']);
    }

}
