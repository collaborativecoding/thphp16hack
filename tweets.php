<?php
header('Content-type: application/json');
echo file_get_contents(__DIR__ . 'tweets.json');