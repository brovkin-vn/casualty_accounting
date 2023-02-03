<?php
if (isset($_GET['id']))
    $casualty_id = $_GET['id'];
else
    $casualty_id = 0;

// http://pnoc-pc177:8089/ca/edit.php?id=11
?>    

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <script>
        let id  = <?=$casualty_id?>

        console.log(`localStorageSetCasualtyId: ${id}`)
        localStorage.setItem('casualty_id', id);
        window.location.href = "editor.html"
    </script>
</body>
</html>