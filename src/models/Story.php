<?php

namespace App\Models;

use PDO;
use App\Exceptions\StoryNotFoundException;
use App\Exceptions\StoryNotSavedException;

class Story {

    /** @var array */
    protected $story;

    /**
     * Story constructor.
     *
     * @param $config
     */
    public function __construct($config) {
        $dbconfig = $config['database'];
        $dsn = 'mysql:host=' . $dbconfig['host'] . ';dbname=' . $dbconfig['name'];
        $this->db = new PDO($dsn, $dbconfig['user'], $dbconfig['pass']);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * @param $id
     *
     * @return array
     * @throws StoryNotFoundException
     */
    public function show($id)
    {

        $story = $this->db->prepare('SELECT * FROM story WHERE id = ?');
        $story->execute([$id]);

        if ($story->rowCount() < 1) {
            throw new StoryNotFoundException();
        }

        return $story->fetch(PDO::FETCH_ASSOC);

    }

    /**
     * @param array $story
     *
     * @return string
     * @throws StoryNotSavedException
     */
    public function create(array $story = null) {

        if($story) {
            $this->story = $story;
        }

        try{
            $stmt = $this->db->prepare('INSERT INTO story (headline, url, created_by, created_on) VALUES (?, ?, ?, NOW())');
            $stmt->execute($this->story);
        }catch(\PDOException $e) {
            //log $e somewhere
            throw new StoryNotSavedException();
        }

        return $this->db->lastInsertId();
    }

    public function set(array $story) {
        $this->story = $story;
    }

    public function errors() {
        if(!isset($this->story['headline']) || !isset($this->story['url']) || !filter_input(INPUT_POST, 'url', FILTER_VALIDATE_URL)) {
            return 'You did not fill in all the fields or the URL did not validate.';
        }

        return false;
    }

    public function validate() {
        return !$this->errors($this->story);
    }

}
