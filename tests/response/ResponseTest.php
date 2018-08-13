<?php
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase {
    public function testSend() {
        $url = 'http://localhost/taskador-backend/tests/response/200OKResponse.php';
        $data = ['var' => '1'];
        $options = ['http' => [
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'method' => 'POST',
            'content'=> http_build_query($data)
            ]];
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        
        if ($result != false) {
            echo $result;
            $this->assertJson($result);
        } else {
            $this->fail('Failed to get Response.');
        }
    }
}
