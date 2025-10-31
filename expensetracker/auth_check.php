<?php
session_start();
if (!isset($_SESSION['username'])) {
  http_response_code(401);
  exit;
}
http_response_code(200);
exit;
