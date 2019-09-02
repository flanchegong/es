<?php
/**
 * Created by PhpStorm.
 * User: gong
 * Date: 2/9/2019
 * Time: 15:00
 */

namespace App\Model;

use EasySwoole\Mysqli\Mysqli;

class BaseModel
{
    protected $db;
    protected $table;
    function __construct(Mysqli $connection)
    {
        $this->db = $connection;
    }

    function getDbConnection():Mysqli
    {
        return $this->db;
    }

    /**
     * @return mixed
     */
    public function getTable()
    {
        return $this->table;
    }
}