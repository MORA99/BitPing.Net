#!/bin/sh

date

if [ ! -e "/tmp/bpn-update" ]; then
   trap "rm -f /tmp/bpn-update; exit" INT TERM EXIT
   touch /tmp/bpn-update

   cd /home/bitcoin/Abe/
   python -m Abe.abe --config abe-my.conf --no-serve
   php /var/www/bitping/monitor/bpn-monitor.php
   php /var/www/bitping/monitor/oneshot-monitor.php

   rm /tmp/bpn-update
   trap - INT TERM EXIT
else
   echo Already running
fi
