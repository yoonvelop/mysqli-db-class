<?php


class Board
{
    private $conn;
    private $table_name = 'board_tb';

    public $board_idx;
    public $board_title;
    public $board_content;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * insert function example for use
     * @param array $data
     * @return array
     */
    function insert_board($data = array())
    {
        return $this->conn->insert($this->table_name, $data);
    }

    /**
     * select function example for use
     * @param $board_idx
     * @return array
     */
    function select_board($board_idx)
    {
        // if $board_idx==null => select all
        $where = $board_idx!=null?"board_idx = " . $board_idx:null;

        return $this->conn->select($this->table_name, '*', $where);
    }


}