<?php
namespace App\Models;

use App\Core\BaseActiveRecord;

class BlogPost extends BaseActiveRecord {
    protected static $tableName = 'blog_posts';

    protected function getAttributes() {
        return ['id', 'title', 'image_path', 'content', 'author', 'created_at'];
    }
}
?>
