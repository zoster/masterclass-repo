<?php

namespace App\Models;

use App\Exceptions\CommentNotSavedException;
use PDO;

class Comment
{
    /** @var PDO  */
    protected $db;

    /** @var array */
    protected $comment;

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

    public function set(array $comment) {
        $this->comment = $comment;
    }

    /**
     * @throws CommentNotSavedException
     */
    public function create() {
        $sql = 'INSERT INTO comment (created_by, created_on, story_id, comment) VALUES (?, NOW(), ?, ?)';
        $stmt = $this->db->prepare($sql);
        if( !$stmt->execute([
            $this->comment['created_by'],
            $this->comment['story_id'],
            $this->comment['comment']
        ]) ) {
            throw new CommentNotSavedException();
        }
    }
}
