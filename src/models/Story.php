<?php

namespace App\Models;

use PDO;
use App\Exceptions\StoryNotFoundException;
use App\Exceptions\StoryNotSavedException;

class Story
{

    /** @var array */
    protected $story;
    protected $db;

    /**
     * Story constructor.
     *
     * @param PDO $db
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
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
     * @return array
     */
    public function index()
    {
        $sql  = 'SELECT * FROM story ORDER BY created_on DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return string
     * @throws StoryNotSavedException
     */
    public function create()
    {
        try {
            $stmt = $this->db->prepare('INSERT INTO story (headline, url, created_by, created_on) VALUES (?, ?, ?, NOW())');
            $stmt->execute([
                $this->story['headline'],
                $this->story['url'],
                $this->story['created_by'],
            ]);
        } catch (\PDOException $e) {
            //log $e somewhere
            throw new StoryNotSavedException();
        }

        return $this->db->lastInsertId();
    }

    /**
     * @param array $story
     */
    public function set(array $story)
    {
        $this->story = $story;
    }

    /**
     * @return bool|string
     */
    public function errors()
    {
        if (!isset($this->story['headline'])
            || !isset($this->story['url'])
            || !filter_var($this->story['url'], FILTER_VALIDATE_URL)
        ) {
            return 'You did not fill in all the fields or the URL did not validate.';
        }

        return false;
    }

    /**
     * @return bool
     */
    public function validate()
    {
        return !$this->errors($this->story);
    }

}
