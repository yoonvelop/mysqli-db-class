<?php
$path = $_SERVER["DOCUMENT_ROOT"];

include_once $path . "/config/Database.php";
include_once $path . "/objects/Board.php";

$db = new Database();
$board = new Board($db);

$board_idx = (isset($_GET['board_idx'])) ? $_GET['board_idx'] : null;

$result = $board->select_board($board_idx);

if ($result['isExist']) {  // success
    echo json_encode($result['data']);
} else { // fail
    echo json_encode(array("massage" => "fail"));
}
