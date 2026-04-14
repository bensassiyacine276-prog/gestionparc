<?php
session_start();
session_destroy();
header('Location: ../public/liste.php');
exit;
?>