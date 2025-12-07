<?php
namespace App\Core;

abstract class BaseActiveRecord {
    protected $id = null;
    protected static $tableName = '';
    protected $attributes = [];

    // Метод должен быть реализован в каждом наследнике
    abstract protected function getAttributes();

    public function __construct(array $data = []) {
        $this->attributes = $data;
        if (isset($data['id'])) {
            $this->id = $data['id'];
        }
    }

    public function __get($name) {
        return $this->attributes[$name] ?? null;
    }

    public function __set($name, $value) {
        $this->attributes[$name] = $value;
    }

    public function save() {
        $db = \Database::getConnection();

        if ($this->id) {
            // UPDATE
            $fields = [];
            foreach ($this->getAttributes() as $attribute) {
                if ($attribute !== 'id') {
                    $fields[] = "$attribute = :$attribute";
                }
            }

            $sql = "UPDATE " . static::$tableName .
                " SET " . implode(', ', $fields) .
                " WHERE id = :id";

            $stmt = $db->prepare($sql);
            foreach ($this->getAttributes() as $attribute) {
                if ($attribute !== 'id') {
                    $stmt->bindValue(":$attribute", $this->$attribute);
                }
            }
            $stmt->bindValue(':id', $this->id);
            $stmt->execute();
        } else {
            // INSERT
            $attributes = $this->getAttributes();
            unset($attributes[array_search('id', $attributes)]);

            $columns = implode(', ', $attributes);
            $placeholders = ':' . implode(', :', $attributes);

            $sql = "INSERT INTO " . static::$tableName .
                " ($columns) VALUES ($placeholders)";

            $stmt = $db->prepare($sql);
            foreach ($attributes as $attribute) {
                $stmt->bindValue(":$attribute", $this->$attribute);
            }
            $stmt->execute();

            $this->id = $db->lastInsertId();
        }

        return $this;
    }

    public function delete() {
        if (!$this->id) {
            return false;
        }

        $db = \Database::getConnection();
        $sql = "DELETE FROM " . static::$tableName . " WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $this->id);
        $stmt->execute();

        return true;
    }

    public static function find($id) {
        $db = \Database::getConnection();
        $sql = "SELECT * FROM " . static::$tableName . " WHERE id = :id LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            return new static($data);
        }

        return null;
    }

    public static function findAll($orderBy = 'id DESC') {
        $db = \Database::getConnection();
        $sql = "SELECT * FROM " . static::$tableName;

        if ($orderBy) {
            $sql .= " ORDER BY " . $orderBy;
        }

        $stmt = $db->query($sql);
        $results = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = new static($row);
        }

        return $results;
    }

    public static function paginate($page = 1, $perPage = 10, $orderBy = 'id DESC') {
        $db = \Database::getConnection();
        $offset = ($page - 1) * $perPage;

        // Получаем данные для текущей страницы
        $sql = "SELECT * FROM " . static::$tableName .
            " ORDER BY $orderBy LIMIT :limit OFFSET :offset";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $items = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $items[] = new static($row);
        }

        // Получаем общее количество
        $countStmt = $db->query("SELECT COUNT(*) FROM " . static::$tableName);
        $total = $countStmt->fetchColumn();
        $totalPages = ceil($total / $perPage);

        return [
            'items' => $items,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_items' => $total
        ];
    }
}
?>