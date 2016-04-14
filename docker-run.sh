#yum install docker
#service docker start

#�Ѧ�:
#http://www.dylanlindgren.com/docker-for-the-laravel-framework/

#�Ұ�docker�e���ø��Jdocker�M����
#1.�Ұ�data�e���A�j�w�D����Ƨ��ؿ�
mkdir -p app/logs
mkdir -p app/www
mkdir -p app/dynamodb_local

docker run --name server-data -v /project/app:/data:rw slim3/data ls /data 

#2.�Ұ�composer�A�إ�php�M��
#alias composer="docker run --privileged=true --volumes-from server-data --rm slim3/php-composer" 
#composer create-project slim/slim-skeleton /data/www --prefer-dist
#composer update

#3.�Ұ�db
docker run --privileged=true --name server-db -e "TZ=Asia/Taipei" --volumes-from server-data -d slim3/aws_dynamo_db

#4.�Ұ�php-fpm
docker run --privileged=true --name server-php -e "TZ=Asia/Taipei" --volumes-from server-data --link server-db:db -d slim3/phpfpm

#5.�Ұ�nginx
docker run --privileged=true --name server-web -e "TZ=Asia/Taipei" --volumes-from server-data -p 80:80 --link server-php:fpm -d slim3/nginx