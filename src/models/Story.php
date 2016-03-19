<?php

namespace App\Models;

use App\Dbal\Mysql;
use App\Exceptions\NotFoundException;
use App\Exceptions\StoryNotFoundException;
use App\Exceptions\StoryNotSavedException;

class Story implements Model
{

    /** @var array */
    protected $story;
    protected $db;

    /**
     * Story constructor.
     *
     * @param Mysql $db
     */
    public function __construct(Mysql $db)
    {
        $this->db = $db;
        $this->db->setTable('story');
    }

    /**
     * @param $id
     *
     * @return array
     * @throws StoryNotFoundException
     */
    public function show($id)
    {
        try {
            return $this->db->fetchOne($id);
        }catch (NotFoundException $e) {
            throw new StoryNotFoundException();
        }
    }

    /**
     * @return array
     */
    public function index()
    {
        return $this->db->fetchAll();
    }

    /**
     * @return string
     * @throws StoryNotSavedException
     */
    public function create()
    {
        try {
            return $this->db->insert($this->story);
        } catch (\PDOException $e) {
            //log $e somewhere
            throw new StoryNotSavedException();
        }
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
