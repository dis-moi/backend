#!/usr/bin/env bash
chmod u+x ./docker/.dev_env
source ./docker/.dev_env

RET=1
while [ $RET -ne 0 ]; do
   mysql -h $SYMFONY__DATABASE__HOST -P $SYMFONY__DATABASE__PORT -u $SYMFONY__DATABASE__USER -p$SYMFONY__DATABASE__PASSWORD -e "status" > /dev/null 2>&1
   RET=$?
   if [ $RET -ne 0 ]; then
       echo "\n Waiting for confirmation of MySQL service startup";
       sleep 5
   fi
done

chmod -R a+x app/cache
composer install

#Run according to sourced / exported vars
php bin/console server:run 172.16.1.2:8000
