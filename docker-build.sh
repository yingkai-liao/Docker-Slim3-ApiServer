#yum install docker
#service docker start

#�Ѧ�:
#http://www.dylanlindgren.com/docker-for-the-laravel-framework/

#�qdockerfile����docker image�M����
cd docker-data
docker build -t slim3/data ./

cd ../docker-nginx
docker build -t slim3/nginx ./

cd ../docker-phpfpm
docker build -t slim3/phpfpm ./

cd ../docker-php-composer
docker build -t slim3/php-composer ./

cd ../docker-aws-dynamo-db
docker build -t slim3/aws_dynamo_db ./