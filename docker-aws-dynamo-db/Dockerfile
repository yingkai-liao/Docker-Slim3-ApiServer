FROM makuk66/docker-oracle-java7

MAINTAINER "yingkai" <yingkai.liao@madhead.com>

# Create working space
RUN mkdir -p /var/dynamodb_wd
WORKDIR /var/dynamodb_wd

RUN mkdir -p /data/dynamodb_local

# Default port for DynamoDB Local
EXPOSE 8000

# Get the package from Amazon
RUN wget -O /var/dynamodb_wd/dynamodb_local_latest http://dynamodb-local.s3-website-us-west-2.amazonaws.com/dynamodb_local_latest
RUN tar xfz /var/dynamodb_wd/dynamodb_local_latest

# Default command for image
ENTRYPOINT ["/usr/bin/java", "-Djava.library.path=.", "-jar", "DynamoDBLocal.jar", "-dbPath", "/data/dynamodb_local"]
CMD ["-port", "8000"]

# Add VOLUMEs to allow backup of config, logs and databases
VOLUME ["/data"]
