#!/bin/sh
set -e
REDIS_STATUS_FILE=/tmp/.redis_cluster_check
export REDISCLI_AUTH=$REDIS_PASSWORD
response=$(timeout -s 3 1 redis-cli -h localhost -p $REDIS_PORT ping)
if [ "$response" != "PONG" ]; then
    echo "$response"
    exit 1
fi
if [ ! -f "$REDIS_STATUS_FILE" ]; then
    response=$(timeout -s 3 1 redis-cli -h localhost -p $REDIS_PORT CLUSTER INFO | grep cluster_state | tr -d '[:space:]')
    if [ "$response" != "cluster_state:ok" ]; then
        echo "$response"
        exit 1
    else
        touch "$REDIS_STATUS_FILE"
    fi
fi
