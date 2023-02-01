<?php
namespace App\Models;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;

class LoginModel extends Model{

    protected $table = "login";
    protected $primaryKey = 'id';
    protected $allowedFields = ['username','password', 'role'];
    protected $returnType = 'array';
    
}