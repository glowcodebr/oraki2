<?php
require_once __DIR__ . '/../../config/db.php';

class ClientService {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    // Busca lista de clientes filtrando por Status (Active/Inactive)
    public function getClientsByStatus(int $userId, string $viewType) {
        // Regra de Negócio:
        // Inativo = Status 'inactive' OU (Sem consulta há mais de 60 dias E não é cliente novo de < 30 dias)
        
        $sql = "SELECT 
                    c.*,
                    DATEDIFF(NOW(), MAX(a.start_time)) as days_since_last_app,
                    MAX(a.start_time) as last_appointment_date
                FROM clients c
                LEFT JOIN appointments a ON c.id = a.client_id AND a.status != 'cancelled'
                WHERE c.user_id = :uid
                GROUP BY c.id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['uid' => $userId]);
        $allClients = $stmt->fetchAll();

        $filtered = [];
        foreach ($allClients as $client) {
            $days = $client['days_since_last_app'];
            $isInactiveByTime = ($days !== null && $days > 60);
            $isManuallyInactive = ($client['status'] === 'inactive');

            // Lógica de Filtro
            if ($viewType === 'inactive') {
                if ($isManuallyInactive || $isInactiveByTime) {
                    $client['computed_status'] = 'Inativo';
                    $filtered[] = $client;
                }
            } else {
                // Lista Padrão (Ativos)
                if (!$isManuallyInactive && !$isInactiveByTime) {
                    $client['computed_status'] = 'Ativo';
                    $filtered[] = $client;
                }
            }
        }
        return $filtered;
    }

    // Busca TODOS os dados profundos para o Modal
    public function getClientFullProfile(int $userId, int $clientId) {
        // 1. Dados Pessoais
        $stmt = $this->pdo->prepare("SELECT *, TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) as age FROM clients WHERE id = :cid AND user_id = :uid");
        $stmt->execute(['cid' => $clientId, 'uid' => $userId]);
        $profile = $stmt->fetch();

        if (!$profile) return null;

        // 2. Jornada (Histórico)
        $stmt2 = $this->pdo->prepare("
            SELECT a.*, s.name as service_name 
            FROM appointments a 
            JOIN services s ON a.service_id = s.id
            WHERE a.client_id = :cid ORDER BY a.start_time DESC
        ");
        $stmt2->execute(['cid' => $clientId]);
        $journey = $stmt2->fetchAll();

        // 3. Investimentos (Financeiro)
        $stmt3 = $this->pdo->prepare("
            SELECT 
                COUNT(*) as total_apps,
                COALESCE(SUM(s.price), 0) as total_spent,
                AVG(s.price) as avg_ticket
            FROM appointments a
            JOIN services s ON a.service_id = s.id
            WHERE a.client_id = :cid AND a.status = 'completed'
        ");
        $stmt3->execute(['cid' => $clientId]);
        $investments = $stmt3->fetch();

        return [
            'profile' => $profile,
            'journey' => $journey,
            'investments' => $investments
        ];
    }
}