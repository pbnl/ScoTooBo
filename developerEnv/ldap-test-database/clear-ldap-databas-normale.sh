#!/usr/bin/env bash

echo "This script needs root access to clear the database"

if [ $EUID != 0 ]; then
    {
        kdesudo "$0" "$@"
    } || {
        {
            gksudo "$0" "$@"
        } || {
            sudo -S "$0" "$@"
        }
    }
    exit $?
fi
echo "Clearing..."
sudo systemctl stop slapd
sudo rm /etc/openldap/slapd.d/* -R
sudo rm /var/lib/ldap/* -rf
sudo slapadd -n 0 -F /etc/openldap/slapd.d -l ./ldif/ldap-test-database-config.ldif
sudo slapadd -n 1 -l ./ldif/ldap-test-database-data.ldif
sudo chown ldap:ldap /etc/openldap/slapd.d/ -R
sudo chown ldap:ldap /var/lib/openldap/ -R
sudo systemctl start slapd