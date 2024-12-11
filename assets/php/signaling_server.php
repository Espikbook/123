<?php
// Intenta cargar autoload.php
function findAutoload() {
    $currentDir = __DIR__;
    while (!file_exists($currentDir . '/vendor/autoload.php')) {
        $currentDir = dirname($currentDir);
        if ($currentDir === dirname($currentDir)) {
            throw new Exception('No se encontró el archivo autoload.php');
        }
    }
    return $currentDir . '/vendor/autoload.php';
}

try {
    require findAutoload();
    echo "Autoload cargado correctamente.\n";
} catch (Exception $e) {
    die($e->getMessage());
}

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class SignalingServer implements MessageComponentInterface {
    private $rooms = [];

    public function onOpen(ConnectionInterface $conn) {
        $queryParams = [];
        parse_str($conn->httpRequest->getUri()->getQuery(), $queryParams);
        $roomId = $queryParams['roomId'] ?? 'default';

        if (!isset($this->rooms[$roomId])) {
            $this->rooms[$roomId] = [];
        }
        $this->rooms[$roomId][$conn->resourceId] = $conn;

        echo "Nueva conexión en la sala: {$roomId} ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
        $roomId = $data['roomId'] ?? 'default';

        if (!isset($data['type']) || !in_array($data['type'], ['offer', 'answer', 'candidate'])) {
            echo "Tipo de mensaje no válido: {$msg}\n";
            return;
        }

        if (($data['type'] === 'offer' || $data['type'] === 'answer') && (!isset($data['sdp']) || !is_string($data['sdp']))) {
            echo "SDP no válido: {$msg}\n";
            return;
        }

        foreach ($this->rooms[$roomId] as $clientId => $client) {
            if ($from !== $client) {
                $client->send(json_encode($data));
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        foreach ($this->rooms as $roomId => &$clients) {
            if (isset($clients[$conn->resourceId])) {
                unset($clients[$conn->resourceId]);
                echo "Conexión cerrada: {$roomId} ({$conn->resourceId})\n";
            }
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }
}

// Inicia el servidor Ratchet
$server = new Ratchet\App('localhost', 8080);
$server->route('/signaling', new SignalingServer());
$server->run();
?>
