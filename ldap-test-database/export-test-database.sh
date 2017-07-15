#!/usr/bin/env bash

echo "This script needs root access to export the database"

if [ $EUID != 0 ]; then
    sudo "$0" "$@"
    exit $?
fi

SCRIPT=`realpath $0`
SCRIPTPATH=`dirname $SCRIPT`

echo "Exporting config..."
slapcat -n 0 -l $SCRIPTPATH/ldap-test-database-config.ldif
echo "OK"
echo "Exporting data..."
slapcat -n 1 -l $SCRIPTPATH/ldap-test-database-data.ldif -a '(entryDN:dnSubtreeMatch:=dc=pbnl,dc=de)'
echo "OK"