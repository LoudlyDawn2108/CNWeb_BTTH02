<?php
require_once __DIR__ . '/../lib/Model.php';

/**
 * @property int id
 * @property string name
 */
class Category extends Model {
    protected ?string $table = 'categories';
}