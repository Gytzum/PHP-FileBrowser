<?php
session_start();

if ($_SESSION['logged_in'] != 'true') {
    $host  = $_SERVER['HTTP_HOST'];
    $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $extra = 'login.php';
    header("Location: http://$host$uri/$extra");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="./style.css">
    <title>FileBrowser</title>
</head>

<body>
   
    <div class="container " style="margin: auto; text-align:center">
        <h2 class="header">Files Browser</h2>
        <table class="table table-dark">
            <thead>
                <tr style="color:bisque">
                    <td style="width: 33%">Type</td>
                    <td style="width: 33%">Name</td>
                    <td style="width: 33%">Actions</td>
                </tr>
            </thead>
            <tbody>
                <?php

                $path = './' . $_GET['path'] ;
                $dirContent = scandir($path);

                    foreach ($dirContent as $contentPiece) {
                    if (substr_count($path, "./") > 1) {
                        $path = $_GET['path'] . '/';
                    }

                    if ($contentPiece == '.' || $contentPiece == '..' || $contentPiece == '.git' || $contentPiece == 'index.php'
                        || $contentPiece == 'login.php'|| $contentPiece == 'logout.php'|| $contentPiece == 'style.css') continue;
                    if (is_dir($path . '/' . $contentPiece)) {
                        $type = 'Dir';
                        $contentPiece = '<a href="?path=' . $path .  $contentPiece . '">' . $contentPiece . '</a>';
                        $actions = '';
                    }
                    if (is_file($path . '/' . $contentPiece)) {
                        $type = 'File';
                        $actions =
                            '<form method="POST">
                                <button type="submit" class="btn-delete" name="delete" value="' . $contentPiece . '">Delete</button>
                            </form>
                            <form action="" method="POST">
                                <button type="submit" class="btn-download" name="download" value="' . $contentPiece . '">Download</button>
                            </form>';
                    }

                    print("<tr>");
                    print("<td>$type</td>");
                    print("<td>$contentPiece</td>");
                    print("<td>$actions</td>");
                    print("</tr>");
                }

                ?>
            </tbody>
        </table>
    </div>

    <!-- DELETE FILE LOGIC -->
    <?php
    if (isset($_POST['delete'])) {
        unlink($path . '/' . $_POST['delete']);
        echo 'File deleted!';
        header("Refresh:0");
    }

    // BUTTON BACK
    $back = dirname($path, 1);
    if ($back == ".")  $back = ltrim(strpbrk($input , "." ),'. ');
    
    print '<button><a class="backBtn" href="?path='.$back.'">Back</a></button>';
   
?>

    <!-- UPLOAD FILES HTML-->
    <div class="uploadForm">
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="file" name="image" id="img" style="display:none;" />
            <button style="width: 49%" type="button">
                <label for="img">Choose file</label>
            </button>
            <button <input style="width: 49%" type="submit" />Upload file</button>
        </form>
    </div>

    <!-- UPLOAD FILES LOGIC -->
    <?php
    if (isset($_FILES['image'])) {
        $errors = array();

        $file_name = $_FILES['image']['name'];
        $file_size = $_FILES['image']['size'];
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_type = $_FILES['image']['type'];
        $file_ext = strtolower(end(explode('.', $_FILES['image']['name'])));

        $extensions = array("jpeg", "jpg", "png");
        if (in_array($file_ext, $extensions) === false) {
            $error = "extension not allowed, please choose a JPEG or PNG file.";
        }
        if ($file_size > 2097152) {
            $error = 'File size must be exactly 2 MB';
        }
        if (empty($errors) == true) {
            move_uploaded_file($file_tmp, "./" . $path . $file_name);
            echo "Success";
            header("Refresh:0");
        } else {
            print $error;
        }
    }
    ?>

    <!-- DOWNLOAD FILES LOGIC    TODO FIX--> 
    <?php if (isset($_POST['download'])) {
        $file = './' . $_GET["path"] . $_POST['download'];
        $fileToDownloadEscaped = str_replace("&nbsp;", " ", htmlentities($file, null, 'utf-8'));
        ob_clean();
        ob_start();
        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename=' . basename($fileToDownloadEscaped));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($fileToDownloadEscaped));
        ob_end_flush();
        
        readfile($fileToDownloadEscaped);
        exit;
    }

    // CREATE DIRECTORY LOGIC
    if (isset($_POST['submit'])) {
        $create = $_POST['create'];
        if (is_dir(__DIR__ . (isset($_GET['path']) ? $_GET['path'] : '') . '/' . $create)) {
            $msg = 'Directory ' . $create . ' already exist!';
        } else {
            mkdir($path . '/' . $create);
            $msg = 'Directory ' . $create . ' was successfully created!';
            header("Refresh:0");
        }
        echo $msg;
    }
    ?>

    <form method="post">
        <input type="text" name="create" placeholder="Create folder">
        <button type="submit" name="submit" value="submit">Submit</button>
    </form>

    <!-- LOGOUT REFERENCE -->
    <p style="margin-top:20px">Click here to <a href="logout.php"> logout ! </p>
</body>

</html>
