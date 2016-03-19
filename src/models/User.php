<?php

namespace App\Models;

use App\Dbal\Mysql;
use App\Exceptions\NotFoundException;
use App\Exceptions\UserNotSavedException;
use PDO;

class User implements Model
{
    /** @var  array */
    protected $user;

    /** @var PDO */
    protected $db;

    /**
     * User constructor.
     *
     * @param Mysql $pdo
     */
    public function __construct(Mysql $pdo)
    {
        $this->db = $pdo;
        $this->db->setTable('user');
    }

    /**
     * @param $username
     *
     * @return array|false
     */
    public function show($username)
    {
        return $this->db->fetchOne($username, 'username');
    }

    /**
     * @return string
     * @throws UserNotSavedException
     */
    public function create()
    {
        try {
            $insert = [
                'username' => $this->user['username'],
                'email'    => $this->user['email'],
                'password' => md5($this->user['username'] . $this->user['password']),
            ];

            return $this->db->insert($insert);
        } catch (NotFoundException $e) {
            //log $e somewhere
            throw new UserNotSavedException();
        }
    }

    /**
     * @param $username
     * @param $password
     *
     * @return bool
     */
    public function update($username, $password)
    {
        return $this->db->update(['username' => $username], ['password' => md5($username . $password)]);
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

        if (!$forUpdate) {
            if (empty($this->user['email'])) {
                return 'You did not fill in all required fields.';
            }
            if (!filter_var($this->user['email'], FILTER_VALIDATE_EMAIL)) {
                return 'Your email address is invalid';
            }

            try {
                $this->db->fetchOne($this->user['username'], 'username');
            } catch (NotFoundException $e) {
                return false;
            }

            return 'Your chosen username already exists. Please choose another.';
        }
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
        try {
            $user = $this->db->fetchOne($username, 'username');
        } catch (NotFoundException $e) {
            dd('here');
            return false;
        }

        if ($user['password'] == md5($username . $password)) {
            return true;
        }

        return false;
    }
}
