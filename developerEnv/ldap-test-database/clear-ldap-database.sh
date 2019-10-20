#!/usr/bin/env bash
echo "This script needs root access to clear the database"

echo $passwd | sudo -S echo

sudo systemctl stop slapd
sudo rm /etc/openldap/slapd.d/ -R
sudo mkdir /etc/openldap/slapd.d/
sudo rm /var/lib/openldap/* -rf
sudo slapadd -n 0 -F /etc/openldap/slapd.d -l ./developerEnv/ldap-test-database/ldif/ldap-test-database-config.ldif
sudo slapadd -n 1 -l ./developerEnv/ldap-test-database/ldif/ldap-test-database-data.ldif
sudo mkdir /var/run/slapd -p
sudo chown ldap:ldap /etc/openldap/slapd.d/ -R
sudo chown ldap:ldap /var/lib/openldap/ -R
sudo chown ldap:ldap /var/run/slapd -R
sudo systemctl start slapd