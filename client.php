<?php
// client.php

class ZabbixClient {
    private $api_entry_point;
    private $log_file;
    
    public function __construct($entry_point, $log_file) {
        $this->api_entry_point = $entry_point;
        $this->log_file = $log_file;
    }
    
    private function makeRequest($url, $method = 'GET', $data = null) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        if ($method === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            }
        }
        
        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($status >= 200 && $status < 300) {
            return json_decode($response, true);
        }
        
        return null;
    }
    
    private function logUpdate($eventid) {
        $timestamp = date('Y-m-d H:i:s');
        $log_entry = "$timestamp - Problème $eventid mis à jour en sévérité haute\n";
        file_put_contents($this->log_file, $log_entry, FILE_APPEND);
    }
    
    public function updateWarningSeverities() {
        // Récupérer tous les problèmes de niveau warning
        $response = $this->makeRequest($this->api_entry_point . '?severity=2');
        
        if (!$response || !isset($response['_embedded']['problems'])) {
            echo "Échec de récupération des problèmes warning\n";
            return;
        }
        
        foreach ($response['_embedded']['problems'] as $problem) {
            // Utiliser le lien de sévérité fourni par HAL
            $severity_url = $problem['_links']['severity']['href'];
            
            // Mettre à jour en sévérité haute
            $update_response = $this->makeRequest($severity_url, 'PUT', ['severity' => 3]);
            
            if ($update_response) {
                $this->logUpdate($problem['eventid']);
                echo "Problème {$problem['eventid']} mis à jour en sévérité haute\n";
            }
        }
    }
}

// Utilisation
$client = new ZabbixClient('http://localhost/api/problems', 'update_problems_severity.log');
$client->updateWarningSeverities();
?>
