<?php

class FirebaseService {
    private $baseUrl;
    private $secret;

    public function __construct($baseUrl, $secret) {
        $this->baseUrl = rtrim($baseUrl, '/') . '/';
        $this->secret = $secret;
    }

    private function request($path, $method = 'GET', $data = null) {
        $url = $this->baseUrl . $path . '.json?auth=' . $this->secret;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ($method === 'PUT' || $method === 'PATCH' || $method === 'POST') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }

    public function set($path, $data) {
        return $this->request($path, 'PUT', $data);
    }

    public function update($path, $data) {
        return $this->request($path, 'PATCH', $data);
    }

    public function push($path, $data) {
        return $this->request($path, 'POST', $data);
    }

    public function get($path) {
        return $this->request($path, 'GET');
    }

    public function delete($path) {
        return $this->request($path, 'DELETE');
    }
}
