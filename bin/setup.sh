#!/bin/bash

git clone https://github.com/javanile/webhook
cd webhook
composer install
cp manifest.json.sample manifest.json
cp secret.php.sample secret.php
touch access.log

echo "1. Add cron: * * * * * root $PWD/bin/cron.sh"
echo "2. Move 'webhook.php' in public web folder"
echo "3. Edit 'webhook.php' variable \$_WEBHOOK_DIR = '$PWD';"
echo "4. Edit 'manifest.json' set 'host' key with public 'webhook.php'"
echo "5. Browse 'webhook.php' and access with: admin"
