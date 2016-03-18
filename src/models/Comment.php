<?php

namespace App\Models;

use App\Exceptions\CommentNotSavedException;
use PDO;

class Comment
{
    protected $db;

    /**
     * Comment constructor.
     *
     * @param PDO $db
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * @param $story_id
     *
     * @return array
     */
    public function byStoryId($story_id)
    {
        $comments = $this->db->prepare('SELECT * FROM comment WHERE story_id = ?');
        $comments->execute(array($story_id));
        return $comments->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param array $comment
     *
     * @throws CommentNotSavedException
     */
    public function create(array $comment) {
        $sql = 'INSERT INTO comment (created_by, created_on, story_id, comment) VALUES (?, NOW(), ?, ?)';
        $stmt = $this->db->prepare($sql);
        if( !$stmt->execute([
            $comment['created_by'],
            $comment['story_id'],
            $comment['comment']
        ]) ) {
            throw new CommentNotSavedException();
        }
    }
}
