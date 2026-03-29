<?php
session_start();
session_destroy();
header('Location: /Vite-et-gourmand/index.php');
exit;