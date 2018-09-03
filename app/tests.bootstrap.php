<?php
if (isset($_ENV['BOOTSTRAP_CLEAR_LDAP_DATABASE']) && $_ENV['BOOTSTRAP_CLEAR_LDAP_DATABASE'] == "true") {

    $passwd = getenv("sudopasswd");
    passthru(sprintf("./ldap-test-database/clear-ldap-database.sh '$passwd'"));
}

if (isset($_ENV['BOOTSTRAP_CLEAR_MYSQL_DATABASE']) && $_ENV['BOOTSTRAP_CLEAR_MYSQL_DATABASE'] == "true") {
    passthru(sprintf('./mysql-test-database/clear-mysql-database.sh'));
}

require __DIR__.'/../vendor/autoload.php';
