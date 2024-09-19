<?php
// Configura o cabeçalho para aceitar requisições de fora (CORS)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once 'conexao.php';

// Verifica o método HTTP
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Listar usuários ou um usuário específico
        if (isset($_GET['id'])) {
            $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
            $query = $conectar->prepare("SELECT * FROM login WHERE id = :id");
            $query->bindParam(':id', $id);
            $query->execute();
            $usuario = $query->fetch(PDO::FETCH_ASSOC);

            if ($usuario) {
                echo json_encode($usuario);
            } else {
                echo json_encode(['message' => 'Usuário não encontrado']);
            }
        } else {
            $query = $conectar->query("SELECT * FROM login");
            $usuarios = $query->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($usuarios);
        }
        break;

    case 'POST':
        // Criar novo usuário
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->nome) && !empty($data->login)) {
            $query = $conectar->prepare("INSERT INTO login (nome, login) VALUES (:nome, :login)");
            $query->bindParam(':nome', $data->nome);
            $query->bindParam(':login', $data->login);

            if ($query->execute()) {
                echo json_encode(['message' => 'Usuário criado com sucesso']);
            } else {
                echo json_encode(['message' => 'Falha ao criar o usuário']);
            }
        } else {
            echo json_encode(['message' => 'Dados incompletos']);
        }
        break;

    case 'PUT':
        // Atualizar usuário
        $data = json_decode(file_get_contents("php://input"));
        if (isset($_GET['id']) && !empty($data->nome) && !empty($data->login)) {
            $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
            $query = $conectar->prepare("UPDATE login SET nome = :nome, login = :login WHERE id = :id");
            $query->bindParam(':id', $id);
            $query->bindParam(':nome', $data->nome);
            $query->bindParam(':login', $data->login);

            if ($query->execute()) {
                echo json_encode(['message' => 'Usuário atualizado com sucesso']);
            } else {
                echo json_encode(['message' => 'Falha ao atualizar o usuário']);
            }
        } else {
            echo json_encode(['message' => 'Dados incompletos ou ID ausente']);
        }
        break;

    case 'DELETE':
        // Excluir usuário
        if (isset($_GET['id'])) {
            $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
            $query = $conectar->prepare("DELETE FROM login WHERE id = :id");
            $query->bindParam(':id', $id);

            if ($query->execute()) {
                echo json_encode(['message' => 'Usuário excluído com sucesso']);
            } else {
                echo json_encode(['message' => 'Falha ao excluir o usuário']);
            }
        } else {
            echo json_encode(['message' => 'ID do usuário não informado']);
        }
        break;

    default:
        // Método não permitido
        http_response_code(405);
        echo json_encode(['message' => 'Método não permitido']);
        break;
}
?>
