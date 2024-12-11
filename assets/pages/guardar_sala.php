<?php
$data = json_decode(file_get_contents('php://input'), true);

// Ruta del archivo JSON que contiene las salas
$roomsFile = __DIR__ . '/../data/rooms.json';

// Crear el archivo si no existe
if (!file_exists($roomsFile)) {
    file_put_contents($roomsFile, json_encode([]));
}

// Cargar las salas existentes
$rooms = json_decode(file_get_contents($roomsFile), true);

// Agregar la nueva sala si no existe
if (!in_array($data['room'], $rooms)) {
    $rooms[] = $data['room'];
    file_put_contents($roomsFile, json_encode($rooms));
}

echo json_encode(['success' => true]);
