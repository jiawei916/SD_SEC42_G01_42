<?php
echo "Server Diagnostic Test<br>";
echo "=====================<br>";

// Test 1: Basic PHP
echo "✅ PHP is executing<br>";

// Test 2: Check server
$server = "sql303.infinityfree.com";
echo "Testing connection to: $server<br>";

// Test 3: Multiple connection methods
$methods = [
    'fsockopen' => function() use ($server) {
        $fp = @fsockopen($server, 3306, $errno, $errstr, 5);
        if ($fp) {
            fclose($fp);
            return "✅ Reachable via fsockopen";
        }
        return "❌ fsockopen failed: $errstr";
    },
    'gethostbyname' => function() use ($server) {
        $ip = gethostbyname($server);
        return $ip === $server ? "❌ DNS lookup failed" : "✅ DNS resolved to: $ip";
    }
];

foreach ($methods as $name => $test) {
    echo "$name: " . $test() . "<br>";
}
?>