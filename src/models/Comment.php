<?php

namespace App\Models;

use App\Dbal\Mysql;
use App\Exceptions\CommentNotSavedException;
use App\Exceptions\NotFoundException;

class Comment implements Model
{
    /** @var Mysql */
    protected $db;

    /** @var array */
    protected $comment;

    /**
     * Comment constructor.
     *
     * @param Mysql $db
     */
    public function __construct(Mysql $db)
    {
        $this->db = $db;
        $this->db->setTable('comment');
    }

    /**
     * @param $story_id
     *
     * @return array
     */
    public function byStoryId($story_id)
    {
        return $this->db->fetchMatching($story_id, 'story_id');
    }

    /**
     * @param array $comment
     */
    public function set(array $comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return bool
     */
    public function errors()
    {
        if (empty(trim($this->comment['comment']))) {
            return 'Please enter a comment';
        }

        return false;
    }

    /**
     * @return bool
     */
    public function validate()
    {
        return !$this->errors();
    }

    /**
     * @throws CommentNotSavedException
     */
    public function create()
    {
        try {
            $this->db->insert($this->comment);
        } catch (NotFoundException $e) {
            throw new CommentNotSavedException();
        }
    }
}
