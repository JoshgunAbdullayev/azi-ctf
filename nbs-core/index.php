<?php
http_response_code(403);
header('Content-Type: application/json');
echo json_encode(['error' => 'Forbidden', 'message' => 'Direct access not allowed.']);
