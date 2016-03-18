<?php

namespace App\Models;

use App\Exceptions\UserNotSavedException;
use PDO;

class User
{
    protected $user;
    protected $db;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function show($username)
    {
        $stmt = $this->db->prepare('SELECT * FROM user WHERE username = ?');
        $stmt->execute(array($username));
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(array $user = null)
    {

        if ($user) {
            $this->user = $user;
        }

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

    public function update($username, $password)
    {
        $stmt = $this->db->prepare('UPDATE user SET password = ? WHERE username = ?');
        return $stmt->execute(array(
                md5($username . $password), // THIS IS NOT SECURE.
                $username,
            ));
    }

    public function set(array $user)
    {
        $this->user = $user;
    }

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

    public function validate($forUpdate = false)
    {
        return !$this->errors($forUpdate);
    }

    public function authenticate(array $auth)
    {
        $username = $auth['username'];
        $password = $auth['password'];
        $password = md5($username . $password); // THIS IS NOT SECURE. DO NOT USE IN PRODUCTION.
        $stmt = $this->db->prepare('SELECT * FROM user WHERE username = ? AND password = ? LIMIT 1');
        $stmt->execute(array($username, $password));

        if((bool)$stmt->rowCount()) {
            return true;
        }

        return false;
    }
}
