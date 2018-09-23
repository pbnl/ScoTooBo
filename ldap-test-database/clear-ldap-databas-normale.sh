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
sudo service slapd stop
sudo rm /etc/ldap/slapd.d/* -R
sudo rm /var/lib/ldap/* -rf
sudo slapadd -n 0 -F /etc/ldap/slapd.d -l ./ldap-test-database/ldap-test-database-config.ldif
sudo slapadd -n 1 -l ./ldap-test-database/ldap-test-database-data.ldif
sudo chown openldap:openldap /etc/ldap/slapd.d/ -R
sudo chown openldap:openldap /var/lib/ldap/ -R
sudo service slapd start
