#!/bin/sh
set -e
export REDISCLI_AUTH=$REDIS_PASSWORD
response=$(timeout -s 3 5 redis-cli -h localhost -p $REDIS_PORT ping)
if [ "$response" != "PONG" ] && [ "$response" != "LOADING Redis is loading the dataset in memory" ]; then
    echo "$response"
    exit 1
fi
