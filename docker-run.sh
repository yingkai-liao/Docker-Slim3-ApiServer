#yum install docker
#service docker start

#參考:
#http://www.dylanlindgren.com/docker-for-the-laravel-framework/

#啟動docker容器並載入docker映像檔
#1.啟動data容器，綁定主機資料夾目錄
mkdir -p app/logs
mkdir -p app/www
mkdir -p app/dynamodb_local

docker run --name server-data -v /project/app:/data:rw slim3/data ls /data 

#2.啟動composer，建立php專案
#alias composer="docker run --privileged=true --volumes-from server-data --rm slim3/php-composer" 
#composer create-project slim/slim-skeleton /data/www --prefer-dist
#composer update

#3.啟動db
docker run --privileged=true --name server-db -e "TZ=Asia/Taipei" --volumes-from server-data -d slim3/aws_dynamo_db

#4.啟動php-fpm
docker run --privileged=true --name server-php -e "TZ=Asia/Taipei" --volumes-from server-data --link server-db:db -d slim3/phpfpm

#5.啟動nginx
docker run --privileged=true --name server-web -e "TZ=Asia/Taipei" --volumes-from server-data -p 80:80 --link server-php:fpm -d slim3/nginx