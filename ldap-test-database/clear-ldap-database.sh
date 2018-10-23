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

sudo service slapd stop
sudo rm /etc/ldap/slapd.d/* -R
sudo rm /var/lib/ldap/* -rf
sudo slapadd -n 0 -F /etc/ldap/slapd.d -l ./ldap-test-database/ldap-test-database-config.ldif
sudo slapadd -n 1 -l ./ldap-test-database/ldap-test-database-data.ldif
sudo chown openldap:openldap /etc/ldap/slapd.d/ -R
sudo chown openldap:openldap /var/lib/ldap/ -R
sudo service slapd start
