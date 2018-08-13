<?php
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase {
    public function testSend() {
        $url = 'http://localhost/taskador-backend/tests/response/200OKResponse.php';   
        $response = $this->sendRequest($url);
        $this->handleResponse($response);
    }
    
    public function testErrorResponse() {
        $url = 'http://localhost/taskador-backend/tests/response/500ServerErrorResponse.php';
        $response = $this->sendRequest($url);
        $this->handleResponse($response);
    }
    
    private function sendRequest($url) {
        $data = ['var' => '1'];
        $options = ['http' => [
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'method' => 'POST',
            'content'=> http_build_query($data),
            'ignore_errors' => true
            ]];
        $context  = stream_context_create($options);
        return file_get_contents($url, false, $context); 
    }
    
    private function handleResponse($response) {
        if ($response != false) {
            echo $response;
            $this->assertJson($response);
        } else {
            $this->fail('Failed to get Response.');
        }
    }
}
