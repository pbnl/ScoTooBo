#!/usr/bin/env bash

cd mysql-test-database
rm test.sql
mysqldump -u scotoobo -psecret --databases scotoobo > test.sql

