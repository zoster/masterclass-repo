<?php

namespace App\Controllers;

use App\Models\User;

class UserController {

    /** @var User */
    protected $userModel;

    /**
     * UserController constructor.
     *
     * @param User $userModel
     */
    public function __construct(User $userModel)
    {
        $this->userModel = $userModel;
    }

    public function create() {
        $error = null;
        
        // Do the create
        if(isset($_POST['create'])) {

            $this->userModel->set([
                'username' => $_POST['username'],
                'email' => $_POST['email'],
                'password' => $_POST['password'],
                'password_check' => $_POST['password_check']
            ]);

            if($this->userModel->validate()) {
                $this->userModel->create();
                header("Location: /user/login");
                exit;
            }

            $error = $this->userModel->errors();

        }
        // Show the create form
        
        $content = '
            <form method="post">
                ' . $error . '<br />
                <label>Username</label> <input type="text" name="username" value="" /><br />
                <label>Email</label> <input type="text" name="email" value="" /><br />
                <label>Password</label> <input type="password" name="password" value="" /><br />
                <label>Password Again</label> <input type="password" name="password_check" value="" /><br />
                <input type="submit" name="create" value="Create User" />
            </form>
        ';
        
        require_once __BASE_DIR__ . 'templates/layout.phtml';
        
    }

    public function account() {
        $error = null;
        if(!isset($_SESSION['AUTHENTICATED'])) {
            header("Location: /user/login");
            exit;
        }
        
        if(isset($_POST['updatepw'])) {
            $this->userModel->set([
                'username' => $_SESSION['username'],
                'password' => $_POST['password'],
                'password_check' => $_POST['password_check']
            ]);

            $this->userModel->validate(true);


            $this->userModel->update($_SESSION['username'], $_POST['password']);

            $error = 'Your password was changed.';
        }

        $details = $this->userModel->show($_SESSION['username']);
        
        $content = '
        ' . $error . '<br />
        
        <label>Username:</label> ' . $details['username'] . '<br />
        <label>Email:</label>' . $details['email'] . ' <br />
        
         <form method="post">
                ' . $error . '<br />
            <label>Password</label> <input type="password" name="password" value="" /><br />
            <label>Password Again</label> <input type="password" name="password_check" value="" /><br />
            <input type="submit" name="updatepw" value="Create User" />
        </form>';
        
        require_once __BASE_DIR__ . 'templates/layout.phtml';
    }
    
    public function login() {
        $error = null;
        // Do the login
        if(isset($_POST['login'])) {

            if($this->userModel->authenticate($_POST['user'], $_POST['pass'])) {
                $_SESSION['AUTHENTICATED'] = true;
                $_SESSION['username'] = $_POST['user'];
                header("Location: /");
                exit;
            }
        }

        
        $content = '
            <form method="post">
                ' . $error . '<br />
                <label>Username</label> <input type="text" name="user" value="" />
                <label>Password</label> <input type="password" name="pass" value="" />
                <input type="submit" name="login" value="Log In" />
            </form>
        ';
        
        require_once(__BASE_DIR__ . 'templates/layout.phtml');
        
    }
    
    public function logout() {
        // Log out, redirect
        session_destroy();
        header("Location: /");
    }
}
