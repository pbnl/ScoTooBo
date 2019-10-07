#!/usr/bin/env bash

echo "This script needs root access to clear the database"

if [ $EUID != 0 ]; then
    if which kdesudo 1>/dev/null 2>&1 ; then
        kdesudo "$0" "$@"
    elif which gksudo 1>/dev/null 2>&1 ; then
        gksudo "$0" "$@"
    else
        echo "$1" | sudo -S"$0" "$@"
    fi
    exit $?
fi

sudo systemctl stop slapd
sudo rm /etc/openldap/slapd.d/* -R
sudo rm /var/lib/ldap/* -rf
sudo slapadd -n 0 -F /etc/openldap/slapd.d -l ./ldif/ldap-test-database-config.ldif
sudo slapadd -n 1 -l ./ldif/ldap-test-database-data.ldif
sudo chown ldap:ldap /etc/openldap/slapd.d/ -R
sudo chown ldap:ldap /var/lib/openldap/ -R
sudo systemctl start slapd