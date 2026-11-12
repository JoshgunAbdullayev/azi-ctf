<?php
require __DIR__ . '/inc/lib.php';
unset($_SESSION['team']);
redirect('/scoreboard/');
