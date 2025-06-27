#!/usr/bin/env bash

## Este script genera el archivo .env
## Recibe como parámetros o deben estar seteados en el entorno:
## $1 DB_USERNAME
## $2 DB_PASSWORD

## Parámetros tomados desde el entorno:
## GOOGLE_CAPTCHA_KEY
## GOOGLE_CAPTCHA_SECRET
## MAIL_HOST
## MAIL_PORT
## MAIL_USERNAME
## MAIL_PASSWORD
## MAIL_ENCRYPTION
## MAIL_FROM_ADDRESS
## MAIL_FROM_NAME


################ Asignación de variables por parámetros ################
echo $DB_USERNAME
echo $DB_PASSWORD

if [[ ! $DB_USERNAME ]]; then
    DB_USERNAME=${1}
fi

if [[ ! $DB_PASSWORD ]]; then
    DB_PASSWORD=${2}
fi

################ Creo variables de trabajo ################

WORKSCRIPT="$(pwd)"

################ Comprueba requisitos ################

## En caso de ser root se aborta.
if [[ $(whoami) = 'root' ]]; then
    echo 'No puedes ejecutar este script como root'
    exit 1
fi

## En caso de no encontrar archivos esenciales para la ejcución se aborta.
if [[ ! -f "${WORKSCRIPT}/.env.example.production" ]] ||
   [[ ! -d "${WORKSCRIPT}/scripts" ]] ||
   [[ ! -f "${WORKSCRIPT}/scripts/functions.sh" ]]; then
    echo 'Este script solo puede ser ejecutado desde la raíz del proyecto.'
    exit 1
fi

## En caso de existir un archivo .env se omiten operaciones.
if [[ -f "${WORKSCRIPT}/.env" ]]; then
    echo 'Ya existe un archivo .env en el proyecto, omitiendo operaciones.'
    exit 0
fi

################ Incluyo archivos de funciones ################

source "${WORKSCRIPT}/scripts/functions.sh"

################ Comienza el flujo de generar .env ################

## Creo el archivo .env a partir del archivo con parámetros predefinidos.

cp "${WORKSCRIPT}/.env.example.production" "${WORKSCRIPT}/.env"
chmod ug+rw "${WORKSCRIPT}/.env"

## General

replace_or_add_var_in_file "${WORKSCRIPT}/.env" 'APP_ENV' "${APP_ENV}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" 'APP_NAME' "${APP_NAME}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" 'APP_AUTHOR' "${APP_AUTHOR}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" 'APP_DESCRIPTION' "${APP_DESCRIPTION}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" 'APP_URL' "${APP_URL}"

## DB.

replace_or_add_var_in_file "${WORKSCRIPT}/.env" 'DB_DATABASE' "${DB_DATABASE}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" 'DB_USERNAME' "${DB_USERNAME}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" 'DB_PASSWORD' "${DB_PASSWORD}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" 'DB_CONNECTION' "${DB_CONNECTION}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" 'DB_PORT' "${DB_PORT}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" 'MAIL_SCHEME' "${MAIL_SCHEME}"

## Redis

replace_or_add_var_in_file "${WORKSCRIPT}/.env" 'REDIS_PASSWORD' "${REDIS_PASSWORD}"


## Google Captcha

replace_or_add_var_in_file "${WORKSCRIPT}/.env" 'GOOGLE_CAPTCHA_KEY' "${GOOGLE_CAPTCHA_KEY}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" 'GOOGLE_CAPTCHA_SECRET' "${GOOGLE_CAPTCHA_SECRET}"

## Mail

replace_or_add_var_in_file "${WORKSCRIPT}/.env" 'MAIL_MAILER' "${MAIL_MAILER}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" 'MAIL_HOST' "${MAIL_HOST}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" 'MAIL_PORT' "${MAIL_PORT}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" 'MAIL_USERNAME' "${MAIL_USERNAME}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" 'MAIL_PASSWORD' "${MAIL_PASSWORD}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" 'MAIL_FROM_ADDRESS' "${MAIL_FROM_ADDRESS}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" 'MAIL_FROM_NAME' "${MAIL_FROM_NAME}"

echo ""
echo "Resultado del .env final:"
cat "${WORKSCRIPT}/.env"
