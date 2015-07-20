#!/bin/bash
docker run -d --name graphaware-simplemq-rabbit -p 5678:5672 -p 15678:15672 -e RABBITMQ_PASS="secret" tutum/rabbitmq