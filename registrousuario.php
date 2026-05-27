<?php
// Parámetros recibidos, normalmente vendrían de un formulario o API
$nombre = $_POST['nombre'];
$email = $_POST['email'];
$password = $_POST['password'];

// Detalles de la conexión a la base de datos
$servername = "";
$username = "";
$password_db = "";
$dbname = "";

// Crear conexión
$conn = new mysqli($servername, $username, $password_db, $dbname);

// Comprobar si la conexión fue exitosa
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// 1. Verificar si el correo ya existe
$sql_check = "SELECT email FROM login WHERE email = ?";
$stmt_check = $conn->prepare($sql_check);

if ($stmt_check === false) {
    die("Error al preparar la consulta de verificación: " . $conn->error);
}

$stmt_check->bind_param("s", $email);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    // El correo ya existe
    $stmt_check->close();
    $conn->close();
    header("Location: indexLogin.php?registro=duplicado");
    exit();
}

// Determinar el tipo de usuario
$tipo = 'sin permisos';
if (preg_match('/^ch\d+@chapala\.tecmm\.edu\.mx$/', $email)) {
    $tipo = 'Alumno';
}

// Preparar la consulta para insertar datos en la tabla login
$sql_login = "INSERT INTO login (id, nombre, email, password, tipo) 
              VALUES (0, ?, ?, ?, ?)";

// Preparar la consulta para login
$stmt_login = $conn->prepare($sql_login);
if ($stmt_login === false) {
    die("Error al preparar la consulta de login: " . $conn->error);
}

// Hash de la contraseña por seguridad
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Enlazar los parámetros a la consulta de login
$stmt_login->bind_param("ssss", $nombre, $email, $password_hash, $tipo);

// Ejecutar la consulta de login
if ($stmt_login->execute()) {
    // Verificar si el correo tiene formato de alumno (ch+numero)
    if (preg_match('/^ch\d+@chapala\.tecmm\.edu\.mx$/', $email)) {
        // Extraer el número de control del correo electrónico
        $id_alumno = preg_replace('/^ch(\d+)@.*$/', '$1', $email);
        
        // Insertar en la tabla alumnos solo si tiene formato de alumno
        $sql_alumno = "INSERT INTO alumnos (id, nombre, carrera, creditos, calificacion, nombreProyecto, status, dictamen) 
                       VALUES (?, ?, '', 0, 0, '', 'inactivo', '')";
        
        $stmt_alumno = $conn->prepare($sql_alumno);
        if ($stmt_alumno === false) {
            die("Error al preparar la consulta de alumno: " . $conn->error);
        }
        
        // Enlazar parámetros para la consulta de alumno
        $stmt_alumno->bind_param("is", $id_alumno, $nombre);
        
        if (!$stmt_alumno->execute()) {
            // Error en registro de alumno
            $stmt_alumno->close();
            $stmt_login->close();
            $stmt_check->close();
            $conn->close();
            header("Location: indexLogin.php?registro=error_alumno");
            exit();
        }
        $stmt_alumno->close();
    }
    
    // Éxito en el registro (tanto si se insertó en alumnos como si no)
    $stmt_login->close();
    $stmt_check->close();
    $conn->close();
    header("Location: indexLogin.php?registro=exito");
    exit();
} else {
    // Error en registro de login
    $stmt_login->close();
    $stmt_check->close();
    $conn->close();
    header("Location: indexLogin.php?registro=error_login");
    exit();
}
?>
