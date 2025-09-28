<?php

require_once 'BaseModel.php';

class UserModel extends BaseModel {

    public function findUserById($id) {
        $sql = 'SELECT * FROM users WHERE id = '.intval($id);
        $user = $this->select($sql);

        return $user;
    }

    public function findUser($keyword) {
        $keyword = mysqli_real_escape_string(self::$_connection, $keyword);
        $sql = 'SELECT * FROM users WHERE user_name LIKE "%'.$keyword.'%" OR user_email LIKE "%'.$keyword.'%"';
        $user = $this->select($sql);

        return $user;
    }

    /**
     * Authentication user
     * @param $userName
     * @param $password
     * @return array
     */
    public function auth($userName, $password) {
        $userName = mysqli_real_escape_string(self::$_connection, $userName);
        $md5Password = md5($password);
        $sql = 'SELECT * FROM users WHERE name = "' . $userName . '" AND password = "'.$md5Password.'"';

        $user = $this->select($sql);
        return $user;
    }

    /**
     * Delete user by id
     * @param $id
     * @return mixed
     */
    public function deleteUserById($id) {
        $sql = 'DELETE FROM users WHERE id = '.intval($id);
        return $this->delete($sql);

    }

    /**
     * Update user
     * @param $input
     * @return mixed
     */
    public function updateUser($input) {
        $sql = 'UPDATE users SET 
                 name = "' . mysqli_real_escape_string(self::$_connection, $input['name']) .'", 
                 password="'. md5($input['password']) .'"
                WHERE id = ' . $input['id'];

        $user = $this->update($sql);

        return $user;
    }

    /**
     * Insert user
     * @param $input
     * @return mixed
     */
    public function insertUser($input) {
    $name = mysqli_real_escape_string(self::$_connection, $input['name']);
    $fullname = isset($input['fullname']) ? mysqli_real_escape_string(self::$_connection, $input['fullname']) : '';
    $email = isset($input['email']) ? mysqli_real_escape_string(self::$_connection, $input['email']) : '';
    $type = isset($input['type']) ? mysqli_real_escape_string(self::$_connection, $input['type']) : 'user';
    $password = md5($input['password']);
    
    $sql = "INSERT INTO `app_web1`.`users` (`name`, `fullname`, `email`, `type`, `password`) VALUES (" .
        "'" . $name . "', " .
        "'" . $fullname . "', " .
        "'" . $email . "', " .
        "'" . $type . "', " .
        "'" . $password . "')";

    $user = $this->insert($sql);
    return $user;
    }

    /**
     * Search users
     * @param array $params
     * @return array
     */
    public function getUsers($params = []) {
        if (!empty($params['keyword'])) {
            $sql = 'SELECT * FROM users WHERE name LIKE "%' . mysqli_real_escape_string(self::$_connection, $params['keyword']) .'%"';
            $users = $this->select($sql);
        } else {
            $sql = 'SELECT * FROM users';
            $users = $this->select($sql);
        }
        return $users;
    }
}