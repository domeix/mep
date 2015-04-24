<?php
    $db = mysqli_connect("127.0.0.1", "root", "", "meptest1");

    $query = "select * from benutzer";

    $result = mysqli_query($db, $query);

    foreach ($result as $row) {
        foreach ($row as $tupel) {
            echo $tupel.", ";
        }
    }