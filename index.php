<?php
require_once __DIR__.'/includes/Auth.php';
Auth::startSession();
$logged = Auth::check();
$username = Auth::username();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scooby-doo</title>
</head>
<body>
    
</body>
</html>