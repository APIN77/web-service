<?php
header("Content-Type: application/json; charset=UTF-8");
$servername = "localhost"; // Ubah jika perlu
$username = "root"; // Ubah jika perlu
$password = ""; // Ubah jika perlu
$dbname = "billiard"; // Ganti dengan nama database Anda

// Koneksi ke database
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Mendapatkan method request
$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

switch ($method) {
    case 'GET':
        if ($id) {
            // Mendapatkan satu pelanggan
            $sql = "SELECT * FROM Pelanggan WHERE id_pelanggan = $id";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                echo json_encode($result->fetch_assoc());
            } else {
                echo json_encode(["message" => "Pelanggan tidak ditemukan."]);
            }
        } else {
            // Mendapatkan semua pelanggan
            $sql = "SELECT * FROM Pelanggan";
            $result = $conn->query($sql);
            $pelanggan = [];
            while ($row = $result->fetch_assoc()) {
                $pelanggan[] = $row;
            }
            echo json_encode($pelanggan);
        }
        break;

    case 'POST':
        // Menambahkan pelanggan
        $data = json_decode(file_get_contents("php://input"), true);
        $nama = $conn->real_escape_string($data['nama']);
        $nomor_telepon = $conn->real_escape_string($data['nomor_telepon']);
        $email = $conn->real_escape_string($data['email']);

        $sql = "INSERT INTO Pelanggan (nama, nomor_telepon, email) VALUES ('$nama', '$nomor_telepon', '$email')";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["message" => "Pelanggan berhasil ditambahkan.", "id" => $conn->insert_id]);
        } else {
            echo json_encode(["message" => "Error: " . $conn->error]);
        }
        break;

    case 'PUT':
        // Memperbarui pelanggan
        if ($id) {
            $data = json_decode(file_get_contents("php://input"), true);
            $nama = $conn->real_escape_string($data['nama']);
            $nomor_telepon = $conn->real_escape_string($data['nomor_telepon']);
            $email = $conn->real_escape_string($data['email']);

            $sql = "UPDATE Pelanggan SET nama='$nama', nomor_telepon='$nomor_telepon', email='$email' WHERE id_pelanggan=$id";
            if ($conn->query($sql) === TRUE) {
                echo json_encode(["message" => "Pelanggan berhasil diperbarui."]);
            } else {
                echo json_encode(["message" => "Error: " . $conn->error]);
            }
        } else {
            echo json_encode(["message" => "ID pelanggan tidak valid."]);
        }
        break;

    case 'DELETE':
        // Menghapus pelanggan
        if ($id) {
            $sql = "DELETE FROM Pelanggan WHERE id_pelanggan=$id";
            if ($conn->query($sql) === TRUE) {
                echo json_encode(["message" => "Pelanggan berhasil dihapus."]);
            } else {
                echo json_encode(["message" => "Error: " . $conn->error]);
            }
        } else {
            echo json_encode(["message" => "ID pelanggan tidak valid."]);
        }
        break;

    default:
        echo json_encode(["message" => "Metode tidak didukung."]);
        break;
}

// Menutup koneksi
$conn->close();
?>
