#!/bin/bash

echo "⏳ Esperando a que PostgreSQL esté listo..."

until psql -h db -U root -d fynkus -c 'SELECT 1;' > /dev/null 2>&1; do
    echo 'Waiting...';
    sleep 2;
done;

echo "Ejecutando migraciones..."
php bin/console doctrine:migrations:migrate --no-interaction --all-or-nothing;
echo "✅ DB SYNC"
exec php-fpm