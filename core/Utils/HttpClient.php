<?php 
namespace Core\Utils;

class HttpClient {

    public function get($url, $headers = []) {
        return $this->Curl($url, 'GET', null, $headers);
    }

    public function post($url, $data, $headers = []) {
        return $this->Curl($url, 'POST', $data, $headers);
    }

    public function put($url, $data, $headers = []) {
        return $this->Curl($url, 'PUT', $data, $headers);
    }

    public function delete($url, $headers = []) {
        return $this->Curl($url, 'DELETE', null, $headers);
    }

    public function Curl($url, $method = 'GET', $data = null, $headers = []) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $error = curl_error($ch);
        $errno = curl_errno($ch);
        return [
            'response' => $response, 
            'http_code' => $httpCode, 
            'error' => $error,
            'errno' => $errno
        ];
    }
}


?>