#!/usr/bin/env bash

echo "Clearing sqlite"
cat ./developerEnv/mysql-test-database/test.sql | sqlite3 var/data/data.sqlite