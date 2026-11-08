cd /var/www/http/tickler/
php kt.php > /var/logs/de/kt_`date "+%Y_%m%d_%H%M"`.txt
php wt.php > /var/logs/de/wt_`date "+%Y_%m%d_%H%M"`.txt
php ki_rptick.php > /var/logs/de/ki_rp_`date "+%Y_%m%d_%H%M"`.txt
php ki_tick.php > /logs/logs/de/ki_`date "+%Y_%m%d_%H%M"`.txt