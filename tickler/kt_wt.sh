cd /var/www/server01/tickler/
php4 kt.php > logs/kt_`date "+%Y_%m%d_%H%M"`.txt
php4 wt.php > logs/wt_`date "+%Y_%m%d_%H%M"`.txt
php4 ki_rptick.php > logs/ki_rp_`date "+%Y_%m%d_%H%M"`.txt
php4 ki_tick.php > logs/ki_`date "+%Y_%m%d_%H%M"`.txt