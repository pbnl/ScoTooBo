#!/usr/bin/env bash

echo "Clearing sqlite"
rm var/data/data.sqlite
cp developerEnv/mysql-test-database/data.sqlite var/data/data.sqlite