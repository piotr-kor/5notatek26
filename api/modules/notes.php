<?php
function handleNotes($pdo, $method, $id = null) {
    switch ($method) {
        case 'GET':
            if ($id) {
                $stmt = $pdo->prepare("SELECT * FROM notes WHERE id = ?");
                $stmt->execute([$id]);
                $note = $stmt->fetch(PDO::FETCH_ASSOC);
                echo json_encode($note ?: ['error' => 'Note not found']);
            } else {
                $stmt = $pdo->query("SELECT * FROM notes");
                echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents("php://input"), true);
            if (empty($data['content'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing content']);
                return;
            }
            $stmt = $pdo->prepare("INSERT INTO notes (content) VALUES (?)");
            $stmt->execute([$data['content']]);
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
            break;

        case 'PUT':
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing ID']);
                return;
            }
            $data = json_decode(file_get_contents("php://input"), true);
            $stmt = $pdo->prepare("UPDATE notes SET content = ? WHERE id = ?");
            $stmt->execute([$data['content'], $id]);
            echo json_encode(['success' => true]);
            break;

        case 'DELETE':
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing ID']);
                return;
            }
            $stmt = $pdo->prepare("DELETE FROM notes WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
}
