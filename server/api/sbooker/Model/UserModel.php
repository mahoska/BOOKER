<?php

class UserModel extends Model
{
    public function __construct() 
    {
        parent::__construct();
        $this->tableName = 'userBooker';
    }

    /*
    function that returns count all users
    @return <Integer> $countUser
    */ 
    public function countUser()
    {
        try{
            return $this->selectCount(); 
        }catch(PDOException $err){
            file_put_contents('errors.txt', $err->getMessage(), FILE_APPEND); 
            return ['status'=>500, 'clientCode'=>'0006'];
        } 
    } 


    /*
    methods that starts a user edit request
    @param <Array> $params - 
        assoc array with data to update 
    @param <Boolean> $ifPass - 
        flag signaling the need to update the password
    @return <Array> $result - 
        assoc array with status update and 
        if success count affected row
     */
    public function updateUser($params, $ifPass)
    {
        try{
            $query = "UPDATE $this->tableName "
                    . "SET fullname = :fullname, login = :login,"
                    . "role_id = :role_id, email = :email"
                    . (($ifPass)? ", password = :password  ":" ")
                    . "WHERE id = :id LIMIT 1";

            $sth = $this->pdo->prepare($query);
            $sth->execute($params);
            $count =  $sth->rowCount();
            if($count>0)
                return ['status'=>200, 'data'=>$count];
             else 
                return ['status'=>500, 'clientCode'=>'0009'];
        }catch(PDOException $err){
            file_put_contents('errors.txt', $err->getMessage(), FILE_APPEND); 
            return ['status'=>500, 'clientCode'=>'0006'];
        }
    }
}
    




