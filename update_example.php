<?php
$path = $_SERVER["DOCUMENT_ROOT"];

include_once $path . "/config/Database.php";
include_once $path . "/objects/Board.php";

$db = new Database();
$board = new Board($db);


$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data) &&
    array_key_exists('board_idx', $data) && $data['board_idx'] != null &&
    array_key_exists('board_title', $data) && $data['board_title'] != null &&
    array_key_exists('board_content', $data) && $data['board_content'] != null) { // NotNull Check

    $result = ($board->update_board($data));

    if ($result['isExist']) {
        if ($result['isExist']) {
            echo json_encode(array("massage" => "update success"));
        } else {
            echo json_encode(array("massage" => "update fail"));
        }
    } else {
        echo json_encode(array("massage" => "fail"));
    }
}else{
    echo json_encode(array("massage" => "empty value"));
}