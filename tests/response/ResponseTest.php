<?php
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase {
    var $responseCode;
    var $responseContentType;
    public function testSend() {
        $url = 'http://localhost/taskador-backend/tests/response/200OKResponse.php';   
        $response = $this->sendRequest($url);
        $expectedStatus = 'HTTP/1.1 200 OK';
        $expectedBody = '{"number":4}';
        $this->handleResponse($response, $expectedStatus, $expectedBody);
    }
    
    public function testErrorResponse() {
        $url = 'http://localhost/taskador-backend/tests/response/500ServerErrorResponse.php';
        $response = $this->sendRequest($url);
        $expectedStatus = 'HTTP/1.1 500 Internal Server Error';
        $expectedBody = '{"message":"Server Error!!!"}';
        $this->handleResponse($response, $expectedStatus, $expectedBody);
    }
    
    private function sendRequest($url) {
        $data = ['var' => '1'];
        $options = ['http' => [
            'header'  => 'Content-Type: application/x-www-form-urlencoded',
            'method' => 'POST',
            'content'=> http_build_query($data),
            'ignore_errors' => true
            ]];
        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $this->responseCode = $http_response_header[0];
        $this->responseContentType = $http_response_header[5];
        return $response; 
    }
    
    private function handleResponse($response, $expectedStatus, $expectedBody) {
        if ($response != false) {
            $this->assertThat($this->responseContentType, $this->equalTo(
                    'Content-Type: application/json'));
            $this->assertJson($response);
            $this->assertThat($this->responseCode, $this->equalTo($expectedStatus));
            $this->assertThat($response, $this->equalTo($expectedBody));
        } else {
            $this->fail('Failed to get Response.');
        }
    }
}
