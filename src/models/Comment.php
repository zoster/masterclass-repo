<?php

namespace App\Models;

use App\Exceptions\CommentNotSavedException;
use PDO;

class Comment
{
    public function __construct($config) {
        $dbconfig = $config['database'];
        $dsn = 'mysql:host=' . $dbconfig['host'] . ';dbname=' . $dbconfig['name'];
        $this->db = new PDO($dsn, $dbconfig['user'], $dbconfig['pass']);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function byStory($story_id)
    {
        $comments = $this->db->prepare('SELECT * FROM comment WHERE story_id = ?');
        $comments->execute(array($story_id));
        return $comments->fetchAll(PDO::FETCH_ASSOC);
    }

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
