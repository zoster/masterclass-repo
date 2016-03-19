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

        $error = '';

        $this->commentModel->set([
            'story_id'   => $_POST['story_id'],
            'created_by' => $_SESSION['username'],
            'comment'    => filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
        ]);

        $error = $this->commentModel->errors();

        if($this->commentModel->validate()) {
            try {
                $this->commentModel->create();
            } catch (CommentNotSavedException $e) {
                $error = "Comment failed to save, please try again";
            }
        }

        $_SESSION['error'] = $error;

        header("Location: /story?id=" . $_POST['story_id']);
    }

}
