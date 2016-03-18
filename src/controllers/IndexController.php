<?php

namespace App\Controllers;

use App\Models\Comment;
use App\Models\Story;

class IndexController
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

        $stories = $this->storyModel->index();

        $content = '<ol>';

        foreach ($stories as $story) {
            $comments = $this->commentModel->byStory($story['id']);

            $content .= '
                <li>
                <a class="headline" href="' . $story['url'] . '">' . $story['headline'] . '</a><br />
                <span class="details">' . $story['created_by'] . ' | <a href="/story/?id=' . $story['id'] . '">' . count($comments) . ' Comments</a> |
                ' . date('n/j/Y g:i a', strtotime($story['created_on'])) . '</span>
                </li>
            ';
        }

        $content .= '</ol>';

        require __BASE_DIR__ . 'src/views/layout.phtml';
    }
}

