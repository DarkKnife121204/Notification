#!/bin/bash

/etc/confluent/docker/run &

KAFKA_PID=$!

cub kafka-ready -b localhost:9092 1 60

kafka-topics \
  --bootstrap-server localhost:9092 \
  --create \
  --if-not-exists \
  --topic notifications \
  --partitions 3 \
  --replication-factor 1

wait $KAFKA_PID
