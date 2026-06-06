<?php
header('Location: index.php?page=menu-detail' . ($_SERVER['QUERY_STRING'] ? '&' . $_SERVER['QUERY_STRING'] : ''));
exit;
