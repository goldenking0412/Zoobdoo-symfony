#!/usr/bin/env bash

function info {
  printf "\033[0;36m===> \033[0;33m${1}\033[0m\n"
}

if [ ! -d "/var/www/app/logs/supervisor" ]; then
    mkdir -p /var/www/app/logs/supervisor
fi

#info "Run RabbitMQ"
php app/console rabbitmq:consumer -m 50 update_subscriptions  >/var/www/app/logs/rabbitmq-update_subscriptions.log 2>/var/www/app/logs/rabbitmq-update_subscriptions2.log &
php app/console rabbitmq:consumer -m 50 send_notification  >/var/www/app/logs/rabbitmq-send_notification.log 2>/var/www/app/logs/rabbitmq-send_notification2.log &

info "Run Cron"
#Add env variables for cron tasks
printenv >> /etc/environment

(crontab -l ; echo "0 0 * * * /usr/local/bin/php /var/www/app/console erp:property:check-scheduled-payment") | crontab -
(crontab -l ; echo "0 0 * * * /usr/local/bin/php /var/www/app/console erp:stripe:subscription:check-end-of-trial-period") | crontab -
(crontab -l ; echo "0 1 * * * /usr/local/bin/php /var/www/app/console erp:property:check-rent-payment") | crontab -
(crontab -l ; echo "0 1 * * * /usr/local/bin/php /var/www/app/console erp:property:stop-auto-withdraw") | crontab -
(crontab -l ; echo "0 1 * * * /usr/local/bin/php /var/www/app/console erp:notification:notify-users-before-rent-due-date") | crontab -
(crontab -l ; echo "0 1 * * * /usr/local/bin/php /var/www/app/console erp:notification:alert-users-after-rent-due-date") | crontab -

#info "Run RabbitMQ"
php app/console rabbitmq:consumer -m 50 update_subscriptions  >/var/www/app/logs/rabbitmq-update_subscriptions.log 2>/var/www/app/logs/rabbitmq-update_subscriptions2.log &
php app/console rabbitmq:consumer -m 50 send_notification  >/var/www/app/logs/rabbitmq-send_notification.log 2>/var/www/app/logs/rabbitmq-send_notification2.log &

info "Run Cron"
#Add env variables for cron tasks
printenv >> /etc/environment

(crontab -l ; echo "0 0 * * * /usr/local/bin/php /var/www/app/console erp:property:check-scheduled-payment") | crontab -
(crontab -l ; echo "0 0 * * * /usr/local/bin/php /var/www/app/console erp:stripe:subscription:check-end-of-trial-period") | crontab -
(crontab -l ; echo "0 1 * * * /usr/local/bin/php /var/www/app/console erp:property:check-rent-payment") | crontab -
(crontab -l ; echo "0 1 * * * /usr/local/bin/php /var/www/app/console erp:property:stop-auto-withdraw") | crontab -
(crontab -l ; echo "0 1 * * * /usr/local/bin/php /var/www/app/console erp:notification:notify-users-before-rent-due-date") | crontab -
(crontab -l ; echo "0 1 * * * /usr/local/bin/php /var/www/app/console erp:notification:alert-users-after-rent-due-date") | crontab -

exec "$@"
