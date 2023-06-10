<?php

session_start();

if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    $id = trim($_GET["id"]);

    require_once "config.php";

    $sql = "SELECT * FROM tmahasiswa WHERE nim = ?";

    if($stmt = mysqli_prepare($link, $sql)){
        mysqli_stmt_bind_param($stmt, "s", $param_nim);

        $param_nim = $id;

        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);

            if(mysqli_num_rows($result) == 1){
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

                $nim = $row["nim"];
                $status_mahasiswa = $row["status_mahasiswa"];
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }

    if ($status_mahasiswa == "belum berpartisipasi"){
        header("location: belumdaftar.php?id=". $nim);
    }
    else{
        header("location: sudahdaftar.php?id=". $nim);
    }

    mysqli_stmt_close($stmt);

} else{
    echo "Oops! Something went wrong. Please try again later.";
}

?>