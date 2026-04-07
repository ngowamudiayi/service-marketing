<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Configuration de la base de données
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'saglotis_crm';

// Fichier JSON pour persister les données mockées
$mockDataFile = __DIR__ . '/mock_data.json';

// Connexion à la base de données
$conn = new mysqli($host, $user, $password, $database);

// Vérifier la connexion
$useMock = $conn->connect_error;
if ($conn->connect_error) {
    $useMock = true; // Utiliser des données mockées si DB indisponible
} else {
    $conn->set_charset("utf8mb4");
}

// Fonction pour charger les données mockées
function loadMockData($file) {
    if (file_exists($file)) {
        $json = file_get_contents($file);
        return json_decode($json, true);
    }
    return [
        'clients' => [
            ['id' => 1, 'nom' => 'Client 1', 'telephone' => '+33123456789', 'email' => 'client1@example.com', 'type' => 'Particulier', 'statut' => 'Actif', 'dateCreation' => '2023-01-01'],
            ['id' => 2, 'nom' => 'Client 2', 'telephone' => '+33987654321', 'email' => 'client2@example.com', 'type' => 'Entreprise', 'statut' => 'Fidèle', 'dateCreation' => '2023-02-01']
        ],
        'interactions' => [
            [
                'id' => 1,
                'type' => 'appel',
                'clientId' => 1,
                'clientNom' => 'Client 1',
                'telephone' => '+33123456789',
                'duree' => '15 min',
                'commentaire' => 'Appel de suivi',
                'date' => '2023-10-01'
            ],
            [
                'id' => 2,
                'type' => 'message',
                'clientId' => 2,
                'clientNom' => 'Client 2',
                'destinataire' => 'client2@example.com',
                'sujet' => 'Rappel RDV',
                'message' => 'Bonjour, rappel de votre RDV demain.',
                'date' => '2023-10-02'
            ],
            [
                'id' => 3,
                'type' => 'visite',
                'clientId' => 1,
                'clientNom' => 'Client 1',
                'sujet' => 'Visite de suivi',
                'commentaire' => 'Visite pour discuter des besoins futurs',
                'date' => '2023-10-03'
            ]
        ]
    ];
}

// Fonction pour sauvegarder les données mockées
function saveMockData($file, $data) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Récupérer l'action demandée
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'login':
        login($conn);
        break;
    
    case 'getUsers':
        getUsers($conn);
        break;
    
    case 'getUsers':
        getUsers($conn);
        break;
    
    case 'getClients':
        getClients($conn);
        break;
    
    case 'getInteractions':
        getInteractions($conn);
        break;
    
    case 'getPlanifications':
        getPlanifications($conn);
        break;
    
    case 'addClient':
        addClient($conn);
        break;
    
    case 'updateClient':
        updateClient($conn);
        break;
    
    case 'deleteClient':
        deleteClient($conn);
        break;

    // Utilisateurs
    case 'addUser':
        addUser($conn);
        break;
    case 'updateUser':
        updateUser($conn);
        break;
    case 'deleteUser':
        deleteUser($conn);
        break;
    
    case 'addInteraction':
        addInteraction($conn);
        break;
    case 'updateInteraction':
        updateInteraction($conn);
        break;
    case 'deleteInteraction':
        deleteInteraction($conn);
        break;
    
    case 'addPlanification':
        addPlanification($conn);
        break;
    
    case 'deletePlanification':
        deletePlanification($conn);
        break;
    
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Action non reconnue']);
        break;
}

$conn->close();

// ====== FONCTIONS ======

function login($conn) {
    global $useMock;
    if ($useMock) {
        $data = json_decode(file_get_contents("php://input"), true);
        $email = $data['email'];
        $motDePasse = $data['motDePasse'];

        // Utilisateurs mockés
        $users = [
            [
                'id' => 1,
                'nom' => 'Admin User',
                'email' => 'admin@saglotis.com',
                'role' => 'administrateur'
            ],
            [
                'id' => 2,
                'nom' => 'Utilisateur',
                'email' => 'user@saglotis.com',
                'role' => 'utilisateur'
            ],
            [
                'id' => 3,
                'nom' => 'Lecteur',
                'email' => 'lecteur@saglotis.com',
                'role' => 'lecteur'
            ]
        ];

        foreach ($users as $user) {
            if ($user['email'] === $email && $motDePasse === 'password') {
                echo json_encode($user);
                return;
            }
        }

        http_response_code(401);
        echo json_encode(['error' => 'Email ou mot de passe incorrect']);
        return;
    }

    $data = json_decode(file_get_contents("php://input"), true);
    
    $email = $conn->real_escape_string($data['email']);
    $motDePasse = $data['motDePasse']; // Ne pas échapper avant de vérifier
    
    $result = $conn->query("SELECT id, nom, email, role FROM users WHERE email='$email' AND motDePasse='$motDePasse'");
    
    if (!$result) {
        http_response_code(500);
        echo json_encode(['error' => $conn->error]);
        return;
    }
    
    if ($result->num_rows === 0) {
        http_response_code(401);
        echo json_encode(['error' => 'Email ou mot de passe incorrect']);
        return;
    }
    
    $user = $result->fetch_assoc();
    echo json_encode($user);
}

function getUsers($conn) {
    global $useMock, $mockDataFile;
    if ($useMock) {
        $mockData = json_decode(file_get_contents($mockDataFile), true);
        echo json_encode($mockData['users']);
        return;
    }

    $result = $conn->query("SELECT id, nom, email, role FROM users");
    if (!$result) {
        http_response_code(500);
        echo json_encode(['error' => $conn->error]);
        return;
    }
    
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    echo json_encode($users);
}

function getClients($conn) {
    global $useMock, $mockDataFile;
    if ($useMock) {
        $mockData = loadMockData($mockDataFile);
        echo json_encode($mockData['clients'] ?? []);
        return;
    }

    // Vérifier si les colonnes d'audit existent
    $columnsResult = $conn->query("SHOW COLUMNS FROM clients LIKE 'created_by'");
    $hasAuditColumns = $columnsResult && $columnsResult->num_rows > 0;

    $selectColumns = "id, nom, telephone, email, adresse, type, statut, dateCreation";
    if ($hasAuditColumns) {
        $selectColumns .= ", created_by, created_at, updated_by, updated_at";
    }

    $result = $conn->query("SELECT $selectColumns FROM clients");
    if (!$result) {
        // Si la requête échoue, utiliser mock
        $mockData = loadMockData($mockDataFile);
        echo json_encode($mockData['clients'] ?? []);
        return;
    }
    
    $clients = [];
    while ($row = $result->fetch_assoc()) {
        // Pour les anciens enregistrements sans audit, utiliser l'admin par défaut
        if ($hasAuditColumns && (!$row['created_by'] || $row['created_by'] == 0)) {
            $row['created_by'] = 1;
            $row['created_at'] = $row['dateCreation'] . 'T12:00:00.000Z';
        }
        if ($hasAuditColumns && (!$row['updated_by'] || $row['updated_by'] == 0)) {
            $row['updated_by'] = 1;
            $row['updated_at'] = $row['dateCreation'] . 'T00:00:00.000Z';
        }
        $clients[] = $row;
    }
    echo json_encode($clients);
}

function getInteractions($conn) {
    global $useMock, $mockDataFile;
    if ($useMock) {
        $data = loadMockData($mockDataFile);
        echo json_encode($data['interactions']);
        return;
    }

    // Vérifier si les colonnes d'audit existent
    $columnsResult = $conn->query("SHOW COLUMNS FROM interactions LIKE 'created_by'");
    $hasAuditColumns = $columnsResult && $columnsResult->num_rows > 0;

    $selectColumns = "i.id, i.type, i.clientId, c.nom as clientNom, 
                     i.telephone, i.duree, i.commentaire, 
                     i.destinataire, i.sujet, i.message, i.date";
    if ($hasAuditColumns) {
        $selectColumns .= ", i.created_by, i.created_at, i.updated_by, i.updated_at";
    }

    $result = $conn->query("
        SELECT $selectColumns
        FROM interactions i 
        LEFT JOIN clients c ON i.clientId = c.id 
        ORDER BY i.date DESC
    ");
    if (!$result) {
        // Si la requête échoue, utiliser mock
        $data = loadMockData($mockDataFile);
        echo json_encode($data['interactions']);
        return;
    }
    
    $interactions = [];
    while ($row = $result->fetch_assoc()) {
        // Pour les anciens enregistrements sans audit, utiliser l'admin par défaut
        if ($hasAuditColumns && (!$row['created_by'] || $row['created_by'] == 0)) {
            $row['created_by'] = 1;
            $row['created_at'] = $row['date'] . 'T12:00:00.000Z';
        }
        if ($hasAuditColumns && (!$row['updated_by'] || $row['updated_by'] == 0)) {
            $row['updated_by'] = 1;
            $row['updated_at'] = $row['date'] . 'T00:00:00.000Z';
        }
        $interactions[] = $row;
    }
    echo json_encode($interactions);
}

function getPlanifications($conn) {
    global $useMock;
    if ($useMock) {
        $planifications = [
            [
                'id' => 1,
                'titre' => 'Réunion équipe',
                'description' => 'Discussion sur les objectifs',
                'date' => '2023-10-05',
                'heure' => '10:00'
            ]
        ];
        echo json_encode($planifications);
        return;
    }

    $result = $conn->query("SELECT id, titre, description, date, heure FROM planifications");
    if (!$result) {
        http_response_code(500);
        echo json_encode(['error' => $conn->error]);
        return;
    }
    
    $planifications = [];
    while ($row = $result->fetch_assoc()) {
        $planifications[] = $row;
    }
    echo json_encode($planifications);
}

function addClient($conn) {
    global $useMock, $mockDataFile;
    if ($useMock) {
        $data = json_decode(file_get_contents("php://input"), true);
        $mockData = loadMockData($mockDataFile);
        if (!isset($mockData['clients'])) {
            $mockData['clients'] = [];
        }
        
        $newId = count($mockData['clients']) > 0 ? max(array_column($mockData['clients'], 'id')) + 1 : 1;
        $entry = [
            'id' => $newId,
            'nom' => $data['nom'],
            'telephone' => $data['telephone'],
            'email' => $data['email'],
            'adresse' => $data['adresse'],
            'type' => $data['type'],
            'statut' => $data['statut'],
            'dateCreation' => $data['dateCreation']
        ];
        if (isset($data['user_id'])) $entry['created_by'] = $data['user_id'];
        if (isset($data['timestamp'])) $entry['created_at'] = $data['timestamp'];
        if (isset($data['user_id'])) $entry['updated_by'] = $data['user_id'];
        if (isset($data['timestamp'])) $entry['updated_at'] = $data['timestamp'];
        
        $mockData['clients'][] = $entry;
        saveMockData($mockDataFile, $mockData);
        echo json_encode(['success' => true, 'id' => $newId]);
        return;
    }

    $data = json_decode(file_get_contents("php://input"), true);
    
    // Vérifier si les colonnes d'audit existent
    $columnsResult = $conn->query("SHOW COLUMNS FROM clients LIKE 'created_by'");
    $hasAuditColumns = $columnsResult && $columnsResult->num_rows > 0;
    
    $nom = $conn->real_escape_string($data['nom']);
    $telephone = $conn->real_escape_string($data['telephone']);
    $email = $conn->real_escape_string($data['email']);
    $adresse = $conn->real_escape_string($data['adresse']);
    $type = $conn->real_escape_string($data['type']);
    $statut = $conn->real_escape_string($data['statut']);
    $dateCreation = $conn->real_escape_string($data['dateCreation']);
    
    $columns = "nom, telephone, email, adresse, type, statut, dateCreation";
    $values = "'$nom', '$telephone', '$email', '$adresse', '$type', '$statut', '$dateCreation'";
    
    if ($hasAuditColumns && isset($data['user_id'])) {
        $user_id = intval($data['user_id']);
        $columns .= ", created_by, created_at, updated_by, updated_at";
        $values .= ", $user_id, NOW(), $user_id, NOW()";
    }
    
    $query = "INSERT INTO clients ($columns) VALUES ($values)";
    
    if ($conn->query($query)) {
        echo json_encode(['success' => true, 'id' => $conn->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => $conn->error]);
    }
}

function updateClient($conn) {
    global $useMock, $mockDataFile;
    if ($useMock) {
        $data = json_decode(file_get_contents("php://input"), true);
        $mockData = loadMockData($mockDataFile);
        $id = $data['id'];
        
        foreach ($mockData['clients'] as &$client) {
            if ($client['id'] == $id) {
                $client['nom'] = $data['nom'];
                $client['telephone'] = $data['telephone'];
                $client['email'] = $data['email'];
                $client['adresse'] = $data['adresse'];
                $client['type'] = $data['type'];
                $client['statut'] = $data['statut'];
                if (isset($data['user_id'])) $client['updated_by'] = $data['user_id'];
                if (isset($data['timestamp'])) $client['updated_at'] = $data['timestamp'];
                break;
            }
        }
        unset($client);
        saveMockData($mockDataFile, $mockData);
        echo json_encode(['success' => true]);
        return;
    }

    $data = json_decode(file_get_contents("php://input"), true);
    
    // Vérifier si les colonnes d'audit existent
    $columnsResult = $conn->query("SHOW COLUMNS FROM clients LIKE 'updated_by'");
    $hasAuditColumns = $columnsResult && $columnsResult->num_rows > 0;
    
    $id = intval($data['id']);
    $nom = $conn->real_escape_string($data['nom']);
    $telephone = $conn->real_escape_string($data['telephone']);
    $email = $conn->real_escape_string($data['email']);
    $adresse = $conn->real_escape_string($data['adresse']);
    $type = $conn->real_escape_string($data['type']);
    $statut = $conn->real_escape_string($data['statut']);
    
    $setClause = "nom='$nom', telephone='$telephone', email='$email', adresse='$adresse', type='$type', statut='$statut'";
    
    if ($hasAuditColumns && isset($data['user_id'])) {
        $user_id = intval($data['user_id']);
        $setClause .= ", updated_by=$user_id, updated_at=NOW()";
    }
    
    $query = "UPDATE clients SET $setClause WHERE id=$id";
    
    if ($conn->query($query)) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => $conn->error]);
    }
}

function deleteClient($conn) {
    global $useMock, $mockDataFile;
    if ($useMock) {
        $data = json_decode(file_get_contents("php://input"), true);
        $mockData = loadMockData($mockDataFile);
        $id = $data['id'];
        
        $mockData['clients'] = array_filter($mockData['clients'], function($client) use ($id) {
            return $client['id'] != $id;
        });
        $mockData['clients'] = array_values($mockData['clients']); // réindexer
        
        saveMockData($mockDataFile, $mockData);
        echo json_encode(['success' => true]);
        return;
    }

    $data = json_decode(file_get_contents("php://input"), true);
    $id = intval($data['id']);
    
    $query = "DELETE FROM clients WHERE id=$id";
    
    if ($conn->query($query)) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => $conn->error]);
    }
}

// ====== UTILISATEURS ======

function addUser($conn) {
    global $useMock;
    if ($useMock) {
        echo json_encode(['success' => true, 'id' => rand(100, 999)]);
        return;
    }

    $data = json_decode(file_get_contents("php://input"), true);
    $nom = $conn->real_escape_string($data['nom']);
    $email = $conn->real_escape_string($data['email']);
    $motDePasse = $conn->real_escape_string($data['motDePasse']);
    $role = $conn->real_escape_string($data['role']);

    $query = "INSERT INTO users (nom, email, motDePasse, role) VALUES ('$nom', '$email', '$motDePasse', '$role')";
    if ($conn->query($query)) {
        echo json_encode(['success' => true, 'id' => $conn->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => $conn->error]);
    }
}

function updateUser($conn) {
    global $useMock;
    if ($useMock) {
        echo json_encode(['success' => true]);
        return;
    }

    $data = json_decode(file_get_contents("php://input"), true);
    $id = intval($data['id']);
    $nom = $conn->real_escape_string($data['nom']);
    $email = $conn->real_escape_string($data['email']);
    $motDePasse = $conn->real_escape_string($data['motDePasse']);
    $role = $conn->real_escape_string($data['role']);

    $query = "UPDATE users SET nom='$nom', email='$email', motDePasse='$motDePasse', role='$role' WHERE id=$id";
    if ($conn->query($query)) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => $conn->error]);
    }
}

function deleteUser($conn) {
    global $useMock;
    if ($useMock) {
        echo json_encode(['success' => true]);
        return;
    }

    $data = json_decode(file_get_contents("php://input"), true);
    $id = intval($data['id']);
    $query = "DELETE FROM users WHERE id=$id";
    if ($conn->query($query)) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => $conn->error]);
    }
}

function addInteraction($conn) {
    global $useMock, $mockDataFile;
    if ($useMock) {
        $data = json_decode(file_get_contents("php://input"), true);
        $mockData = loadMockData($mockDataFile);
        $interactions = &$mockData['interactions'];
        
        $newId = count($interactions) > 0 ? max(array_column($interactions, 'id')) + 1 : 1;
        
        // Trouver le vrai nom du client
        $clientNom = 'Client ' . $data['clientId'];
        if (isset($mockData['clients'])) {
            foreach ($mockData['clients'] as $client) {
                if ($client['id'] == $data['clientId']) {
                    $clientNom = $client['nom'];
                    break;
                }
            }
        }
        
        $entry = [
            'id' => $newId,
            'type' => $data['type'],
            'clientId' => $data['clientId'],
            'clientNom' => $clientNom,
            'date' => $data['date']
        ];
        if (isset($data['telephone'])) $entry['telephone'] = $data['telephone'];
        if (isset($data['duree'])) $entry['duree'] = $data['duree'];
        if (isset($data['commentaire'])) $entry['commentaire'] = $data['commentaire'];
        if (isset($data['destinataire'])) $entry['destinataire'] = $data['destinataire'];
        if (isset($data['sujet'])) $entry['sujet'] = $data['sujet'];
        if (isset($data['message'])) $entry['message'] = $data['message'];
        if (isset($data['user_id'])) $entry['created_by'] = $data['user_id'];
        if (isset($data['timestamp'])) $entry['created_at'] = $data['timestamp'];
        // Pour les nouvelles, updated_by et updated_at sont les mêmes que created
        if (isset($data['user_id'])) $entry['updated_by'] = $data['user_id'];
        if (isset($data['timestamp'])) $entry['updated_at'] = $data['timestamp'];
        
        $interactions[] = $entry;
        saveMockData($mockDataFile, $mockData);
        echo json_encode(['success' => true, 'id' => $newId]);
        return;
    }

    $data = json_decode(file_get_contents("php://input"), true);
    
    // Vérifier si les colonnes d'audit existent
    $columnsResult = $conn->query("SHOW COLUMNS FROM interactions LIKE 'created_by'");
    $hasAuditColumns = $columnsResult && $columnsResult->num_rows > 0;
    
    $type = $conn->real_escape_string($data['type']);
    $clientId = intval($data['clientId']);
    $telephone = isset($data['telephone']) ? $conn->real_escape_string($data['telephone']) : NULL;
    $duree = isset($data['duree']) ? $conn->real_escape_string($data['duree']) : NULL;
    $commentaire = isset($data['commentaire']) ? $conn->real_escape_string($data['commentaire']) : NULL;
    $destinataire = isset($data['destinataire']) ? $conn->real_escape_string($data['destinataire']) : NULL;
    $sujet = isset($data['sujet']) ? $conn->real_escape_string($data['sujet']) : NULL;
    $message = isset($data['message']) ? $conn->real_escape_string($data['message']) : NULL;
    $date = $conn->real_escape_string($data['date']);
    
    $columns = "type, clientId, telephone, duree, commentaire, destinataire, sujet, message, date";
    $values = "'$type', $clientId, " . ($telephone ? "'$telephone'" : "NULL") . ", " . ($duree ? "'$duree'" : "NULL") . ", " . ($commentaire ? "'$commentaire'" : "NULL") . ", " . ($destinataire ? "'$destinataire'" : "NULL") . ", " . ($sujet ? "'$sujet'" : "NULL") . ", " . ($message ? "'$message'" : "NULL") . ", '$date'";
    
    if ($hasAuditColumns && isset($data['user_id'])) {
        $user_id = intval($data['user_id']);
        $columns .= ", created_by, created_at, updated_by, updated_at";
        $values .= ", $user_id, NOW(), $user_id, NOW()";
    }
    
    $query = "INSERT INTO interactions ($columns) VALUES ($values)";
    
    if ($conn->query($query)) {
        echo json_encode(['success' => true, 'id' => $conn->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => $conn->error]);
    }
}

function updateInteraction($conn) {
    global $useMock, $mockDataFile;
    if ($useMock) {
        $data = json_decode(file_get_contents("php://input"), true);
        $mockData = loadMockData($mockDataFile);
        $interactions = &$mockData['interactions'];
        
        $id = $data['id'];
        foreach ($interactions as &$item) {
            if ($item['id'] == $id) {
                if (isset($data['type'])) $item['type'] = $data['type'];
                if (isset($data['commentaire'])) $item['commentaire'] = $data['commentaire'];
                if (isset($data['duree'])) $item['duree'] = $data['duree'];
                if (isset($data['date'])) $item['date'] = $data['date'];
                if (isset($data['user_id'])) $item['updated_by'] = $data['user_id'];
                if (isset($data['timestamp'])) $item['updated_at'] = $data['timestamp'];
                break;
            }
        }
        saveMockData($mockDataFile, $mockData);
        echo json_encode(['success' => true]);
        return;
    }
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Vérifier si les colonnes d'audit existent
    $columnsResult = $conn->query("SHOW COLUMNS FROM interactions LIKE 'updated_by'");
    $hasAuditColumns = $columnsResult && $columnsResult->num_rows > 0;
    
    $id = intval($data['id']);
    $type = $conn->real_escape_string($data['type']);
    $clientId = intval($data['clientId']);
    $telephone = isset($data['telephone']) ? $conn->real_escape_string($data['telephone']) : NULL;
    $duree = isset($data['duree']) ? $conn->real_escape_string($data['duree']) : NULL;
    $commentaire = isset($data['commentaire']) ? $conn->real_escape_string($data['commentaire']) : NULL;
    $destinataire = isset($data['destinataire']) ? $conn->real_escape_string($data['destinataire']) : NULL;
    $sujet = isset($data['sujet']) ? $conn->real_escape_string($data['sujet']) : NULL;
    $message = isset($data['message']) ? $conn->real_escape_string($data['message']) : NULL;
    $date = $conn->real_escape_string($data['date']);
    
    $setClause = "type='$type', clientId=$clientId, telephone=" . ($telephone ? "'$telephone'" : "NULL") . ", duree=" . ($duree ? "'$duree'" : "NULL") . ", commentaire=" . ($commentaire ? "'$commentaire'" : "NULL") . ", destinataire=" . ($destinataire ? "'$destinataire'" : "NULL") . ", sujet=" . ($sujet ? "'$sujet'" : "NULL") . ", message=" . ($message ? "'$message'" : "NULL") . ", date='$date'";
    
    if ($hasAuditColumns && isset($data['user_id'])) {
        $user_id = intval($data['user_id']);
        $setClause .= ", updated_by=$user_id, updated_at=NOW()";
    }
    
    $query = "UPDATE interactions SET $setClause WHERE id=$id";
    
    if ($conn->query($query)) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => $conn->error]);
    }
}

function deleteInteraction($conn) {
    global $useMock;
    if ($useMock) {
        echo json_encode(['success' => true]);
        return;
    }
    $data = json_decode(file_get_contents("php://input"), true);
    $id = intval($data['id']);
    $query = "DELETE FROM interactions WHERE id=$id";
    if ($conn->query($query)) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => $conn->error]);
    }
}

function addPlanification($conn) {
    global $useMock;
    if ($useMock) {
        echo json_encode(['success' => true, 'id' => rand(100, 999)]);
        return;
    }

    $data = json_decode(file_get_contents("php://input"), true);
    
    $titre = $conn->real_escape_string($data['titre']);
    $description = isset($data['description']) ? $conn->real_escape_string($data['description']) : NULL;
    $date = $conn->real_escape_string($data['date']);
    $heure = $conn->real_escape_string($data['heure']);
    
    $query = "INSERT INTO planifications (titre, description, date, heure) 
              VALUES ('$titre', " . ($description ? "'$description'" : "NULL") . ", '$date', '$heure')";
    
    if ($conn->query($query)) {
        echo json_encode(['success' => true, 'id' => $conn->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => $conn->error]);
    }
}

function deletePlanification($conn) {
    global $useMock, $mockDataFile;
    $data = json_decode(file_get_contents("php://input"), true);
    $id = intval($data['id']);

    if ($useMock) {
        $mockData = json_decode(file_get_contents($mockDataFile), true);
        $mockData['planifications'] = array_filter($mockData['planifications'], function($p) use ($id) {
            return $p['id'] !== $id;
        });
        file_put_contents($mockDataFile, json_encode($mockData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo json_encode(['success' => true]);
        return;
    }

    $query = "DELETE FROM planifications WHERE id = $id";
    if ($conn->query($query)) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => $conn->error]);
    }
}
?>
