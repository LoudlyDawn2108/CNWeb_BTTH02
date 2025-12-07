<?php
namespace Models;
use Lib\Model;

require_once __DIR__ . '/../lib/Model.php';

class UserTable {
    public function __toString(): string {
        return 'users';
    }
    public string $ID = 'users.id';
    public string $USERNAME = 'users.username';
    public string $EMAIL = 'users.email';
    public string $PASSWORD = 'users.password';
    public string $FULLNAME = 'users.fullname';
    public string $ROLE = 'users.role';
    public string $STATUS = 'users.status';
    public string $AVATAR = 'users.avatar';
    public string $CREATED_AT = 'users.created_at';
}

class User extends Model {
    protected ?string $table = 'users';

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