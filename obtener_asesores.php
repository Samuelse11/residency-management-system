<?php
header('Content-Type: application/json');

$db_config = [
    'host' => 'sql204.infinityfree.com',
    'user' => 'if0_40393242',
    'pass' => 'ZBCbazcIqTyh',
    'name' => 'if0_40393242_residencia'
];

try {
    $conn = new mysqli($db_config['host'], $db_config['user'], $db_config['pass'], $db_config['name']);
    
    if ($conn->connect_error) {
        throw new Exception("Error de conexión: " . $conn->connect_error);
    }

    $data = json_decode(file_get_contents('php://input'), true);
    $id_alumno = $data['id_alumno'];
    $asesores = $data['asesores'];

    $stmt = $conn->prepare("UPDATE alumnos SET asesores = ? WHERE id = ?");
    $stmt->bind_param("si", $asesores, $id_alumno);
    $stmt->execute();

    echo json_encode([
        'success' => true,
        'message' => 'Asesores guardados correctamente'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>