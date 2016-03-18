<?php

namespace App\Controllers;

use App\Exceptions\StoryNotFoundException;
use App\Models\Story;
use App\Models\Comment;

class StoryController
{

    protected $storyModel;
    protected $commentModel;

    public function __construct($config)
    {
        $this->storyModel   = new Story($config);
        $this->commentModel = new Comment($config);
    }

    public function index()
    {
        if (!isset($_GET['id'])) {
            header("Location: /");
            exit;
        }

        try {
            $story = $this->storyModel->show((int)$_GET['id']);
        } catch (StoryNotFoundException $e) {
            header("Location: /");
            exit;
        }

        $comments = $this->commentModel->byStory($story['id']);

        $content = '
            <a class="headline" href="' . $story['url'] . '">' . $story['headline'] . '</a><br />
            <span class="details">' . $story['created_by'] . ' | ' . count($comments) . ' Comments |
            ' . date('n/j/Y g:i a', strtotime($story['created_on'])) . '</span>
        ';

        if (isset($_SESSION['AUTHENTICATED'])) {
            $content .= '
            <form method="post" action="/comment/create">
            <input type="hidden" name="story_id" value="' . $_GET['id'] . '" />
            <textarea cols="60" rows="6" name="comment"></textarea><br />
            <input type="submit" name="submit" value="Submit Comment" />
            </form>            
            ';
        }

        foreach ($comments as $comment) {
            $content .= '
                <div class="comment"><span class="comment_details">' . $comment['created_by'] . ' | ' .
                        date('n/j/Y g:i a', strtotime($story['created_on'])) . '</span>
                ' . $comment['comment'] . '</div>
            ';
        }

        require_once __BASE_DIR__ . 'src/views/layout.phtml';

    }

    public function create()
    {
        if (!isset($_SESSION['AUTHENTICATED'])) {
            header("Location: /user/login");
            exit;
        }

        $error = '';

        if (isset($_POST['create'])) {
            $this->storyModel->set([
                'headline' => $_POST['headline'],
                'url' => $_POST['url'],
                'created_by' => $_SESSION['username'],
            ]);

            if ($this->storyModel->validate()) {
                $id = $this->storyModel->create();
                header("Location: /story/?id=$id");
                exit;
            }

            $error = $this->storyModel->errors();

        }

        $content = '
            <form method="post">
                ' . $error . '<br />
        
                <label>Headline:</label> <input type="text" name="headline" value="" /> <br />
                <label>URL:</label> <input type="text" name="url" value="" /><br />
                <input type="submit" name="create" value="Create" />
            </form>
        ';

        require_once __BASE_DIR__ . 'src/views/layout.phtml';
    }

}
