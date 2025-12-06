<?php
namespace Models;
use Model;

require_once __DIR__ . '/../lib/Model.php';

class User extends Model {
    const TABLE = 'users';
    const ID = 'id';
    const USERNAME = 'username';
    const EMAIL = 'email';
    const PASSWORD = 'password';
    const FULLNAME = 'fullname';
    const ROLE = 'role';
    const STATUS = 'status';
    const AVATAR = 'avatar';
    const CREATED_AT = 'created_at';

    protected ?string $table = self::TABLE;

    public int $id;
    public string $username;
    public string $email;
    public string $password;
    public string $fullname;
    public int $role = 0;
    public int $status = 1;
    public ?string $avatar = null;
    public ?string $created_at = null;

    const int ROLE_STUDENT = 0;
    const int ROLE_INSTRUCTOR = 1;
    const int ROLE_ADMIN = 2;
}