<?php
require_once _DIR_.'/includes/Auth.php';
Auth::logout();
header('Location: index.php');
exit;
