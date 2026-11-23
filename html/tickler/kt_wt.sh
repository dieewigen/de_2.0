cd /var/www/html/tickler/
php kt.php > /var/log/de/kt_`date "+%Y_%m%d_%H%M"`.txt
php wt.php > /var/log/de/wt_`date "+%Y_%m%d_%H%M"`.txt
php ki_rptick.php > /var/log/de/ki_rp_`date "+%Y_%m%d_%H%M"`.txt
php ki_tick.php > /var/log/de/ki_`date "+%Y_%m%d_%H%M"`.txt
