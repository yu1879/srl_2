<?php
function connect_sql()
{
    $server_name = '127.0.0.1';
    $username = 'root';
    $password = 'yu850526';
    $db_name = 'srl';

    $connect = new mysqli($server_name, $username, $password, $db_name);
    if ($connect->connect_error) {
        die($connect->connect_error);
    }
    $connect->query('SET NAMES UTF8');
    $connect->query('SET time_zone = "+8:00"');

    return $connect;
}

function select($connect, $table, $columns, $conditions = null)
{
    // [columns]: 'column1, column2'
    $sql = "select {$columns} from {$table}";
    if (!is_null($conditions)) {
        $sql .= " {$conditions}";
    }

    $result = $connect->query($sql);
    if (!$result) {
        die($connect->error);
    }

    $rows = array();
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    return $rows;
}

function insert($connect, $table, $values, $columns = null)
{
    // [values] : 'value1, value2'
    // [columns]: 'column1, column2'
    $sql = "insert into {$table}";
    if (!is_null($columns)) {
        $sql .= " ({$columns})";
    }
    $sql .= " values ({$values})";

    $result = $connect->query($sql);
    if (!$result) {
        die($connect->error);
    }
}

function update($connect, $table, $contents, $conditions)
{
    // [contents]: 'column1=value1, column2=value2'
    $sql = "update {$table} set {$contents} {$conditions}";

    $result = $connect->query($sql);
    if (!$result) {
        die($connect->error);
    }
}

function delete($connect, $table, $conditions)
{
    $sql = "delete from {$table} {$conditions}";

    $result = $connect->query($sql);
    if (!$result) {
        die($connect->error);
    }

    return $connect->affected_rows;
}

function upload_file($file, $destination)
{
    // destination without extension
    if (is_null($file) || $file['size'] === 0) {
        return '';
    }
    $filename = $file['name'];
    $temp = $file['tmp_name'];
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    $destination = "$destination.$extension";
    move_uploaded_file($temp, $destination);
    return $destination;
}
