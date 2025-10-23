<?php
function handleTasks($pdo, $method, $id = null) {
    switch ($method) {
        case 'GET':
            if ($id) {
                $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
                $stmt->execute([$id]);
                $task = $stmt->fetch(PDO::FETCH_ASSOC);
                echo json_encode($task ?: ['error' => 'Task not found']);
            } else {
                $stmt = $pdo->query("SELECT * FROM tasks");
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
            $stmt = $pdo->prepare("INSERT INTO tasks (content) VALUES (?)");
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
            $stmt = $pdo->prepare("UPDATE tasks SET content = ? WHERE id = ?");
            $stmt->execute([$data['content'], $id]);
            echo json_encode(['success' => true]);
            break;

        case 'DELETE':
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing ID']);
                return;
            }
            $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
}
