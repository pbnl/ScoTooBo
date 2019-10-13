<?php
if (isset($_ENV['BOOTSTRAP_CLEAR_LDAP_DATABASE']) && $_ENV['BOOTSTRAP_CLEAR_LDAP_DATABASE']) {
    $passwd = getenv("sudopasswd");
    passthru(sprintf("./developerEnv/ldap-test-database/clear-ldap-database.sh '$passwd'"));
}

if (isset($_ENV['BOOTSTRAP_CLEAR_MYSQL_DATABASE']) && $_ENV['BOOTSTRAP_CLEAR_MYSQL_DATABASE']) {
    $passwd = getenv("sudopasswd");
    passthru(sprintf("./developerEnv/mysql-test-database/clear-sqlite-database.sh '$passwd'"));
}

require __DIR__.'/../vendor/autoload.php';
