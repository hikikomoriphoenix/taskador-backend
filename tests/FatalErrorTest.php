<?php
use PHPUnit\Framework\TestCase;

class FatalErrorTest extends TestCase {
    public function testHandleFatalError() {
        $url = 'http://localhost/taskador-backend/tests/FatalError.php';
        $data = ['var' => 5];
        $options = ['http' =>[            
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'method' => 'POST',
            'content' => http_build_query($data),
            'ignore_errors' => true
        ]];
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $responseCode = $http_response_header[0];
        $expectedResponseCode = 'HTTP/1.1 500 Internal Server Error';
        if ($response != false) {
            $this->assertJson($response);
            $this->assertThat($responseCode, $this->equalTo($expectedResponseCode));
            echo "\n$response";
        } else {
            $this->fail('Failed to get Response.');
        }
    }
}

