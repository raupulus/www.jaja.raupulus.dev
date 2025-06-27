#!/usr/bin/env bash

## Este script prepara la base de datos
## Recibe como parámetros o deben estar seteados en el entorno:
## $1 DB_USERNAME
## $2 DB_PASSWORD

if [[ ! $DB_USERNAME ]]; then
    DB_USERNAME=${1}
fi

if [[ ! $DB_PASSWORD ]]; then
    DB_PASSWORD=${2}
fi

## Compruebo si existe la db, en ese caso devolverá "1"
#db_exist=$(psql -U ${DB_USERNAME} -tAc "SELECT 1 FROM pg_database WHERE datname='jaja_project'")

db_exist=$(psql -tAc "SELECT 1 FROM pg_database WHERE datname='jaja_project'" "user=${DB_USERNAME} password=${DB_PASSWORD}")

if [[ $db_exist = '1' ]]; then
    echo 'Ya existe la base de datos, se aborta crearla'
    exit 0
else
    #@SET PGPASSWORD="${DB_PASSWORD}"
    #createdb -O "${DB_USERNAME}" -T template1 'jaja_project'
    psql -c "CREATE DATABASE jaja_project" "user=${DB_USERNAME} password=${DB_PASSWORD}"
    exit 0
fi

exit 1
