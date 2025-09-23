<?php
namespace TestEnv;

// Stub configuration for database connection used during tests
function mysqli_connect($host, $user, $pass, $name) {
    return null;
}

// Prevent actual session handling during tests
function session_start() {}

class FakeResult {
    public $rows;
    public function __construct(array $rows) {
        $this->rows = $rows;
    }
}

function mysqli_query($conn, $sql) {
    $expectedUser = $GLOBALS['test_valid_user'] ?? '';
    $expectedHash = $GLOBALS['test_valid_hash'] ?? '';
    $expected = "SELECT * from teachers where email='".$expectedUser."' AND password='".$expectedHash."'";
    if (preg_replace('/\s+/', ' ', trim($sql)) === $expected) {
        return new FakeResult([['id' => 1]]);
    }
    return new FakeResult([]);
}

function mysqli_num_rows($result) {
    return count($result->rows);
}

function mysqli_fetch_assoc($result) {
    return array_shift($result->rows);
}

// Dummy connection resource
$conn = null;
