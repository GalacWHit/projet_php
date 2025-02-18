<?php
// problems_api.php

header('Content-Type: application/hal+json');

// Stockage de données simulé
$problems = [
    ['eventid' => 1, 'clock' => time() - 3600, 'severity' => 2],
    ['eventid' => 2, 'clock' => time() - 7200, 'severity' => 2],
    ['eventid' => 3, 'clock' => time() - 1800, 'severity' => 1]
];

// Fonction helper pour générer une réponse HAL
function createHALResponse($data, $self) {
    $response = [
        '_links' => [
            'self' => ['href' => $self],
            'collection' => ['href' => '/api/problems']
        ]
    ];
    
    if (is_array($data) && isset($data[0])) { // Collection
        $response['_embedded'] = ['problems' => []];
        foreach ($data as $problem) {
            $problem['_links'] = [
                'self' => ['href' => "/api/problems/{$problem['eventid']}"],
                'severity' => ['href' => "/api/problems/{$problem['eventid']}/severity"]
            ];
            $response['_embedded']['problems'][] = $problem;
        }
    } else { // Item unique
        $response += $data;
        if (isset($data['eventid'])) {
            $response['_links']['severity'] = [
                'href' => "/api/problems/{$data['eventid']}/severity"
            ];
        }
    }
    
    return $response;
}

// Routeur
$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$parts = explode('/', trim($uri, '/'));

if ($parts[0] !== 'api' || $parts[1] !== 'problems') {
    http_response_code(404);
    exit;
}

// Point d'entrée collection
if (count($parts) === 2) {
    if ($method === 'GET') {
        $filtered_problems = $problems;
        if (isset($_GET['severity'])) {
            $severity = intval($_GET['severity']);
            $filtered_problems = array_filter($problems, 
                fn($p) => $p['severity'] === $severity
            );
        }
        echo json_encode(createHALResponse(array_values($filtered_problems), $uri));
        exit;
    }
    http_response_code(405);
    exit;
}

// Point d'entrée problème unique
if (count($parts) === 3) {
    $eventid = intval($parts[2]);
    $problem_index = array_search($eventid, array_column($problems, 'eventid'));
    
    if ($problem_index === false) {
        http_response_code(404);
        exit;
    }
    
    if ($method === 'GET') {
        echo json_encode(createHALResponse($problems[$problem_index], $uri));
        exit;
    }
    
    if ($method === 'PUT') {
        parse_str(file_get_contents('php://input'), $input);
        if (isset($input['severity'])) {
            $severity = intval($input['severity']);
            if ($severity >= 1 && $severity <= 3) {
                $problems[$problem_index]['severity'] = $severity;
                echo json_encode(createHALResponse($problems[$problem_index], $uri));
                exit;
            }
        }
        http_response_code(400);
        exit;
    }
    
    http_response_code(405);
    exit;
}

// Point d'entrée sévérité
if (count($parts) === 4 && $parts[3] === 'severity') {
    $eventid = intval($parts[2]);
    $problem_index = array_search($eventid, array_column($problems, 'eventid'));
    
    if ($problem_index === false) {
        http_response_code(404);
        exit;
    }
    
    if ($method === 'PUT') {
        parse_str(file_get_contents('php://input'), $input);
        if (isset($input['severity'])) {
            $severity = intval($input['severity']);
            if ($severity >= 1 && $severity <= 3) {
                $problems[$problem_index]['severity'] = $severity;
                echo json_encode(createHALResponse($problems[$problem_index], $uri));
                exit;
            }
        }
        http_response_code(400);
        exit;
    }
    
    http_response_code(405);
    exit;
}

http_response_code(404);
?>
