#!/usr/bin/env bash
echo "This script needs root access to clear the database"

echo $passwd | sudo -S echo

echo "Clearing..."
echo "Stop service"
sudo systemctl stop slapd
echo "Delete files"
sudo rm /etc/openldap/slapd.d/ -R
sudo rm /var/lib/openldap/ -rf
sudo mkdir /etc/openldap/slapd.d/
sudo mkdir /var/lib/openldap/
echo "Load config"
sudo slapadd -n 0 -F /etc/openldap/slapd.d -l ./developerEnv/ldap-test-database/ldif/ldap-test-database-config.ldif
echo "Load data"
sudo slapadd -n 1 -l ./developerEnv/ldap-test-database/ldif/ldap-test-database-data.ldif
echo "Set permissions"
sudo mkdir /var/run/slapd -p
sudo chown ldap:ldap /etc/openldap/slapd.d/ -R
sudo chown ldap:ldap /var/lib/openldap/ -R
sudo chown ldap:ldap /var/run/slapd -R
echo "Run"
sudo systemctl start slapd