<?php
// [2025-06-08 01:13 AM] Added logout handler
session_start();
session_unset();
session_destroy();
header('Location: index.php');
exit;
