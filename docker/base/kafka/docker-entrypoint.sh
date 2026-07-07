#!/bin/bash

/etc/confluent/docker/run &

KAFKA_PID=$!

cub kafka-ready -b localhost:9092 1 60

kafka-topics \
  --bootstrap-server localhost:9092 \
  --create \
  --if-not-exists \
  --topic notifications.transactional \
  --partitions 3 \
  --replication-factor 1

kafka-topics \
  --bootstrap-server localhost:9092 \
  --create \
  --if-not-exists \
  --topic notifications.marketing \
  --partitions 3 \
  --replication-factor 1

wait $KAFKA_PID
