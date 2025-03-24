<?php
require_once __DIR__ . '/qrcode/qrlib.php';
QRcode::png($_GET['code'],false,QR_ECLEVEL_L,3,2);
?>