#100000000010000000001000000000100000000010000000001000000000
#100000000000100000000000100000000000100000000000100000000000
cd /var/www/server01/tickler/
minute=`date "+%M"`
./register.sh
if [ $minute = "00" ]; then
./kt_wt.sh
fi
if [ $minute = "10" ]; then
./wt.sh
fi
if [ $minute = "12" ]; then
./kt.sh
fi
if [ $minute = "20" ]; then
./wt.sh
fi
if [ $minute = "24" ]; then
./kt.sh
fi
if [ $minute = "30" ]; then
./wt.sh
fi
if [ $minute = "36" ]; then
./kt.sh
fi
if [ $minute = "40" ]; then
./wt.sh
fi
if [ $minute = "48" ]; then
./kt.sh
fi
if [ $minute = "50" ]; then
./wt.sh
fi
