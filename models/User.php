<?php
require_once __DIR__ . '/../lib/Model.php';

class User extends Model {
    protected ?string $table = 'users';

    const int ROLE_STUDENT = 0;
    const int ROLE_INSTRUCTOR = 1;
    const int ROLE_ADMIN = 2;
}