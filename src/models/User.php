<?php

namespace App\Models;

use App\Exceptions\UserNotSavedException;
use PDO;

class User
{
    /** @var  array */
    protected $user;

    /** @var PDO */
    protected $db;

    /**
     * User constructor.
     *
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    /**
     * @param $username
     *
     * @return array|false
     */
    public function show($username)
    {
        $stmt = $this->db->prepare('SELECT * FROM user WHERE username = ?');
        $stmt->execute(array($username));
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @return string
     * @throws UserNotSavedException
     */
    public function create()
    {
        try {
            $stmt = $this->db->prepare('INSERT INTO user (username, email, password) VALUES (?, ?, ?)');
            $stmt->execute([
                $this->user['username'],
                $this->user['email'],
                md5($this->user['username'] . $this->user['password'])
            ]);
        } catch (\PDOException $e) {
            //log $e somewhere
            throw new UserNotSavedException();
        }

        return $this->db->lastInsertId();
    }

    /**
     * @param $username
     * @param $password
     *
     * @return bool
     */
    public function update($username, $password)
    {
        $stmt = $this->db->prepare('UPDATE user SET password = ? WHERE username = ?');
        return $stmt->execute(array(
                md5($username . $password), // THIS IS NOT SECURE.
                $username,
            ));
    }

    /**
     * @param array $user
     */
    public function set(array $user)
    {
        $this->user = $user;
    }

    /**
     * @param bool $forUpdate
     *
     * @return bool|string
     */
    public function errors($forUpdate = false)
    {
        if (empty($this->user['username']) || empty($this->user['password']) ||
            empty($this->user['password_check'])
        ) {
            return 'You did not fill in all required fields.';
        }

        if ($this->user['password'] != $this->user['password_check']) {
            return "Your passwords didn't match.";
        }

        if(!$forUpdate){
            if (empty($this->user['email'])) {
                return 'You did not fill in all required fields.';
            }
            if (!filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) {
                return 'Your email address is invalid';
            }

            $existing_user = $this->db->prepare('SELECT * FROM user WHERE username = ?');
            $existing_user->execute([$_POST['username']]);
            if ($existing_user->rowCount() > 0) {
                return 'Your chosen username already exists. Please choose another.';
            }
        }


        return false;
    }

    /**
     * @param bool $forUpdate
     *
     * @return bool
     */
    public function validate($forUpdate = false)
    {
        return !$this->errors($forUpdate);
    }

    /**
     * @param $username
     * @param $password
     *
     * @return bool
     */
    public function authenticate($username, $password)
    {
        $password = md5($username . $password); // THIS IS NOT SECURE. DO NOT USE IN PRODUCTION.
        $stmt = $this->db->prepare('SELECT * FROM user WHERE username = ? AND password = ? LIMIT 1');
        $stmt->execute(array($username, $password));

        if((bool)$stmt->rowCount()) {
            return true;
        }

        return false;
    }
}
