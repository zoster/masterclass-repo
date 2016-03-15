<?php

namespace App\Models;

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

}
