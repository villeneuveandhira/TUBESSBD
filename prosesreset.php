<?php

session_start();

require_once "config.php";

$sql = "UPDATE tmahasiswa SET semester_mahasiswa=semester_mahasiswa+1, status_mahasiswa=DEFAULT";

if($stmt = mysqli_prepare($link, $sql)){
    
    if(mysqli_stmt_execute($stmt)){
        echo "
        <script>
            alert('Semester berhasil ditambah');
            document.location.href = 'admin.php';
        </script>
        ";
    }
}

mysqli_stmt_close($stmt);

?>