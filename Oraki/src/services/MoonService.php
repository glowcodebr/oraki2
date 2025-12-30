<?php
// src/services/MoonService.php

class MoonService {
    
    public static function getCurrentPhaseData() {
        // Data de referência de uma Lua Nova conhecida (06/01/2000)
        $baseDate = new DateTime('2000-01-06 18:14:00');
        $today    = new DateTime();
        
        // Diferença em segundos
        $diff = $today->getTimestamp() - $baseDate->getTimestamp();
        
        // Ciclo lunar médio em segundos (29.53 dias)
        $lunarCycle = 29.53058867 * 86400;
        
        // Onde estamos no ciclo (0 a 1)
        $position = ($diff % $lunarCycle) / $lunarCycle;
        $age = $position * 29.53; // Idade da lua em dias

        // Define a fase e o conselho baseado na idade
        if ($age < 1.84) {
            return [
                'phase' => 'Nova',
                'advice' => 'Início, sementes, potencial infinito e o mistério do escuro.',
                'icon' => 'circle' // Representação vazia/escura
            ];
        } elseif ($age < 9.22) {
            return [
                'phase' => 'Crescente',
                'advice' => 'Movimento, coragem, expansão e foco.',
                'icon' => 'moon' 
            ];
        } elseif ($age < 16.61) {
            return [
                'phase' => 'Cheia',
                'advice' => 'Plenitude, iluminação, emoção à flor da pele e poder.',
                'icon' => 'sun' // Representando o brilho total (ou circle-dot)
            ];
        } else {
            return [
                'phase' => 'Minguante',
                'advice' => 'Limpeza, desapego, sabedoria e banimento.',
                'icon' => 'loader' // Representando o ciclo fechando
            ];
        }
    }
}
?>