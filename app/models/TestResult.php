<?php
namespace App\Models;

use App\Core\BaseActiveRecord;

class TestResult extends BaseActiveRecord {
    protected static $tableName = 'test_results';

    protected function getAttributes() {
        return ['id', 'full_name', 'answers', 'is_correct', 'created_at'];
    }
}
?>