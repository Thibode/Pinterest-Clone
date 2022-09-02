#!/bin/bash

echo "------------------------------------------------------------------------------"
echo "Provisioning"
echo "------------------------------------------------------------------------------"
$PROJECT_DIR/bin/console cache:clear \
&& $PROJECT_DIR/bin/console assets:install public \
&& $PROJECT_DIR/bin/console doctrine:migrations:migrate -n \
|| exit $?

chown -R www-data: $PROJECT_DIR/var
echo "------------------------------------------------------------------------------"
echo "End provisioning"
echo "------------------------------------------------------------------------------"

exec "$@"
