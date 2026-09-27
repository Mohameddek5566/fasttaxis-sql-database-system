<?php
// public/logout.php
session_start();
session_destroy();
header("Location: /fasttaxis_app/public/login.php");
exit;