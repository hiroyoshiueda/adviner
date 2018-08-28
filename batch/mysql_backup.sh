#!/bin/sh

db_host=127.0.0.1
db_name=adviner_db
db_user=root
db_pass=68gh2fbx

bk_num=`date +%d%H`
bk_name=$db_name
bk_out=$bk_name.dump
bk_dir=/apps/backup/adviner.com
bk_file=$db_name-$bk_num.tar.gz

if [ ! -d $bk_dir ]; then
	mkdir $bk_dir
fi

cd $bk_dir

mysqldump -u$db_user -p$db_pass --opt $db_name > $bk_out
if [ ! -f $bk_out -o ! -s $bk_out ]; then
	echo "Cannot dump database."
	exit 1
fi

tar czf $bk_file $bk_out
if [ ! -f $bk_file -o ! -s $bk_file ]; then
	echo "Cannot archive file."
	exit 1
fi

ruby /usr/local/s3sync/s3sync.rb -r --delete $bk_dir/ adviner-data:adviner-backup --exclude="dump$"

#mysqlcheck -o -h$db_host -u$db_user -p$db_pass $db_name

exit 0
