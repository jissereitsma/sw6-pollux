#!/usr/bin/env bash

CWD="$(cd -P -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd -P)"

set -euo pipefail

export PROJECT_ROOT="${PROJECT_ROOT:-"$(dirname "$CWD")"}"
ADMIN_ROOT="${ADMIN_ROOT:-"${PROJECT_ROOT}/vendor/shopware/administration"}"

BIN_TOOL="${CWD}/console"

if [[ ${CI-""} ]]; then
    BIN_TOOL="${CWD}/ci"

    if [[ ! -x "$BIN_TOOL" ]]; then
        chmod +x "$BIN_TOOL"
    fi
fi

# build admin
[[ ${SHOPWARE_SKIP_BUNDLE_DUMP-""} ]] || "${BIN_TOOL}" bundle:dump

if [[ $(command -v jq) ]]; then
    OLDPWD=$(pwd)
    cd "$PROJECT_ROOT" || exit

    jq -c '.[]' "var/plugins.json" | while read -r config; do
        srcPath=$(echo "$config" | jq -r '(.basePath + .administration.path)')

        # the package.json files are always one upper
        path=$(dirname "$srcPath")
        name=$(echo "$config" | jq -r '.technicalName' )

        if [[ -f "$path/package.json" && ! -d "$path/node_modules" && $name != "administration" ]]; then
            echo "=> Installing npm dependencies for ${name}"

            if [[ -f "$path/package-lock.json" ]]; then
                npm clean-install --prefix "$path"
            else
                npm install --prefix "$path"
            fi
        fi
    done
    cd "$OLDPWD" || exit
else
    echo "Cannot check extensions for required npm installations as jq is not installed"
fi

if [ ! -d vendor/shopware/administration/Resources/app/administration/node_modules ]; then
    npm install --prefix vendor/shopware/administration/Resources/app/administration/
fi

mkdir -p vendor/shopware/administration/Resources/app/administration/test/_mocks_/

bin/console -e prod framework:schema -s 'entity-schema' vendor/shopware/administration/Resources/app/administration/test/_mocks_/entity-schema.json

npm run --prefix vendor/shopware/administration/Resources/app/administration/ convert-entity-schema

cd "${ADMIN_ROOT}"/Resources/app/administration && npm run build;

[[ ${SHOPWARE_SKIP_ASSET_COPY-""} ]] ||"${BIN_TOOL}" assets:install

