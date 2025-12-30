<?php
require_once __DIR__ . '/../../config/db.php';

class DashboardStats {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    // --- GRÁFICOS E KPIS ---

    // Busca o faturamento mensal para o gráfico (Janeiro a Dezembro)
    public function getMonthlyRevenueChart(int $userId, int $year): array {
        // 1. Busca os dados brutos no banco (agrupados por mês)
        $sql = "SELECT 
                    MONTH(a.start_time) as month, 
                    SUM(s.price) as total
                FROM appointments a
                JOIN services s ON a.service_id = s.id
                WHERE a.user_id = :uid 
                AND YEAR(a.start_time) = :year
                AND a.status != 'cancelled'
                GROUP BY MONTH(a.start_time)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['uid' => $userId, 'year' => $year]);
        $results = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // Retorna algo como [1 => 500.00, 3 => 1200.00]

        // 2. Normaliza para garantir que temos os 12 meses (preenche com 0 onde não tem ganho)
        $data = [];
        for ($m = 1; $m <= 12; $m++) {
            $data[] = isset($results[$m]) ? (float)$results[$m] : 0;
        }

        return $data; // Retorna array simples: [500, 0, 1200, ...] para o Chart.js
    }

    public function getYearlyStats(int $userId): array {
        // 1. Total Consultas e Faturamento (Ano Atual)
        $sql = "SELECT 
                    COUNT(*) as total_appointments,
                    COALESCE(SUM(s.price), 0) as total_revenue
                FROM appointments a
                JOIN services s ON a.service_id = s.id
                WHERE a.user_id = :uid 
                AND YEAR(a.start_time) = YEAR(CURRENT_DATE())
                AND a.status != 'cancelled'";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['uid' => $userId]);
        $data = $stmt->fetch();

        // 2. Total Clientes Ativos
        $stmt2 = $this->pdo->prepare("SELECT COUNT(*) as total FROM clients WHERE user_id = :uid AND status = 'active'");
        $stmt2->execute(['uid' => $userId]);
        $totalClients = $stmt2->fetch()['total'];

        // 3. Cálculo do Ticket Médio
        $ticketMedio = ($data['total_appointments'] > 0) 
            ? $data['total_revenue'] / $data['total_appointments'] 
            : 0;

        return [
            'appointments_year' => $data['total_appointments'],
            'revenue_year'      => $data['total_revenue'],
            'total_clients'     => $totalClients,
            'avg_ticket'        => $ticketMedio
        ];
    }

    // --- CALENDÁRIO ---

    public function getCalendarData(int $userId, int $month, int $year): array {
        // 1. Busca dias com Agendamentos
        $sqlApps = "SELECT DISTINCT DATE(start_time) as day, COUNT(*) as total 
                    FROM appointments 
                    WHERE user_id = :uid 
                    AND MONTH(start_time) = :m AND YEAR(start_time) = :y 
                    AND status != 'cancelled'
                    GROUP BY DATE(start_time)";
        
        $stmt = $this->pdo->prepare($sqlApps);
        $stmt->execute(['uid' => $userId, 'm' => $month, 'y' => $year]);
        $appointments = $stmt->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_UNIQUE);

        // 2. Busca Agenda Pessoal (Bloqueios)
        $sqlPersonal = "SELECT event_date as day, title 
                        FROM personal_agenda 
                        WHERE user_id = :uid 
                        AND MONTH(event_date) = :m AND YEAR(event_date) = :y";
        
        $stmt2 = $this->pdo->prepare($sqlPersonal);
        $stmt2->execute(['uid' => $userId, 'm' => $month, 'y' => $year]);
        $personal = $stmt2->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_UNIQUE);

        // 3. Monta o Mapa do Mês
        $calendarData = [];
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $currentDate = sprintf('%04d-%02d-%02d', $year, $month, $d);
            
            $status = 'free'; 
            $info = '';

            if (isset($personal[$currentDate])) {
                $status = 'blocked';
                $info = $personal[$currentDate]['title'];
            } 
            elseif (isset($appointments[$currentDate])) {
                $status = 'busy';
                $count = $appointments[$currentDate]['total'];
                $info = "$count agendamento(s)";
            }

            $calendarData[$d] = [
                'status' => $status,
                'info'   => $info,
                'is_today' => ($currentDate == date('Y-m-d'))
            ];
        }
        return $calendarData;
    }

    // --- LISTAS E MODAIS ---

    public function getClientsList(int $userId): array {
        $stmt = $this->pdo->prepare("SELECT id, name FROM clients WHERE user_id = :uid AND status = 'active' ORDER BY name ASC");
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll();
    }

    public function getServicesList(int $userId): array {
        $stmt = $this->pdo->prepare("SELECT id, name, price FROM services WHERE user_id = :uid AND active = 1 ORDER BY name ASC");
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll();
    }

    public function getBirthdaysMonth(int $userId): array {
        // Nota: Requer coluna birth_date na tabela clients
        $sql = "SELECT name, DATE_FORMAT(birth_date, '%d/%m') as day 
                FROM clients 
                WHERE user_id = :uid AND MONTH(birth_date) = MONTH(CURRENT_DATE())
                ORDER BY DAY(birth_date) ASC LIMIT 5";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll();
    }

    public function getRecentAppointments(int $userId): array {
        $sql = "SELECT c.name as client_name, s.name as service_name, a.start_time, a.status
                FROM appointments a
                JOIN clients c ON a.client_id = c.id
                JOIN services s ON a.service_id = s.id
                WHERE a.user_id = :uid
                ORDER BY a.start_time DESC LIMIT 5";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll();
    }
    
    public function getDailyNotes(int $userId): array {
        $stmt = $this->pdo->prepare("SELECT * FROM daily_notes WHERE user_id = :uid AND created_at = CURDATE()");
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll();
    }
}
?>