<?php

namespace App\Dbal;

use App\Exceptions\NotFoundException;
use PDO;

class Mysql
{
    /** @var  PDO */
    private $pdo;

    private $table;

    /**
     * Mysql constructor.
     *
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function setTable($table)
    {
        $this->table = $table;
    }

    public function fetchOne($value, $field = 'id')
    {
        $item = $this->pdo->prepare('SELECT * FROM '.$this->table.' WHERE '.$field.' = ?');
        $item->execute([$value]);
        
        if ($item->rowCount() < 1) {
            throw new NotFoundException();
        }

        return $item->fetch(PDO::FETCH_ASSOC);

    }

    public function fetchMatching($value, $field)
    {
        $list = $this->pdo->prepare('SELECT * FROM '.$this->table.' WHERE '.$field.' = ?');
        $list->execute([$value]);

        return $list->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchAll()
    {
        $list = $this->pdo->prepare('SELECT * FROM '.$this->table.' ORDER BY created_on DESC');
        $list->execute();

        return $list->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert($item)
    {
        $keys = implode(',', array_keys($item));
        $values = '"' . implode('", "', $item) . '"';

        $stmt = $this->pdo->prepare('INSERT INTO '.$this->table.' ('.$keys.', created_on) VALUES ('.$values.', NOW())');
        $stmt->execute();

        return $this->pdo->lastInsertId();
    }
    
    public function update(array $where, array $update)
    {
        $where_key = array_keys($where)[0];
        $where_value = $where[$where_key];

        $update_key = array_keys($update)[0];
        $update_value = $update[$update_key];

        $stmt = $this->pdo->prepare('UPDATE '.$this->table.' SET '.$update_key.' = ? WHERE '.$where_key.' = ?');

        return $stmt->execute([
            $update_value,
            $where_value
        ]);
    }

}
