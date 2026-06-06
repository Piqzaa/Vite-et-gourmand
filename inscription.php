<?php
header('Location: index.php?page=register' . ($_SERVER['QUERY_STRING'] ? '&' . $_SERVER['QUERY_STRING'] : ''));
exit;
