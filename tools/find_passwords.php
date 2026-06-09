<?php
$mysqli = new mysqli('localhost', 'root', '', 'ospos');
$res = $mysqli->query("SELECT username, password, tenant_id FROM ospos_employees WHERE deleted=0");
$passwords = ['pointofsale', 'Pointofsale1', 'password', 'Password1', '12345678', 'admin', 'Admin123!', '123456789', 'changeme', 'pos123456', 'Srorn123!', 'Myco1234!'];
while ($row = $res->fetch_assoc()) {
    foreach ($passwords as $pw) {
        if (password_verify($pw, $row['password'])) {
            echo $row['username'] . ' tenant=' . $row['tenant_id'] . ' pw=' . $pw . PHP_EOL;
            break;
        }
    }
}
