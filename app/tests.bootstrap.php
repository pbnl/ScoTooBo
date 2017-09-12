<?php
if (isset($_ENV['BOOTSTRAP_CLEAR_LDAP_DATABASE']) && $_ENV['BOOTSTRAP_CLEAR_LDAP_DATABASE'] == "true") {
    passthru(sprintf('./ldap-test-database/clear-ldap-database.sh'));
}

require __DIR__.'/../vendor/autoload.php';
