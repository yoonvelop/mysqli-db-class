<?php
$path = $_SERVER["DOCUMENT_ROOT"];

include_once $path . "/config/Database.php";
include_once $path . "/objects/Board.php";

$db = new Database();
$board = new Board($db);

$board_idx = (isset($_GET['board_idx'])) ? $_GET['board_idx'] : null;

if($board_idx!=null) {
    $result = $board->select_board($board_idx);
    echo json_encode($result);
}