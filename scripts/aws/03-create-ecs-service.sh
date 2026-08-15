#!/bin/bash

set -euo pipefail

ALB_ARN=$(aws elbv2 create-load-balancer \
    --name fynkus-alb \
    --subnets $SUBNET_ID_1 $SUBNET_ID_2 \
    --security-groups $ALB_SG_ID \
    --query 'LoadBalancers[0].LoadBalancerArn' \
    --output text)


API_TG_ARN=$(aws elbv2 create-target-group \
    --name fynkus-api-tg \
    --vpc-id $VPC_ID \
    --protocol HTTP \
    --port 8000 \
    --target-type ip \
    --health-check-path /api/v1/health-check \
    --query 'TargetGroups[0].TargetGroupArn' \
    --output text)


LISTENER_ARN=$(aws elbv2 create-listener \
    --load-balancer-arn $ALB_ARN \
    --protocol HTTP \
    --port 80 \
    --default-actions Type=forward,TargetGroupArn=$API_TG_ARN \
    --query 'Listeners[0].ListenerArn' \
    --output text)

aws ecs create-service \
    --cluster fynkus-ecs-cluster \
    --service-name fynkus-api \
    --task-definition fynkus-ecs-task-def \
    --desired-count 1 \
    --launch-type FARGATE \
    --network-configuration "awsvpcConfiguration={subnets=[$SUBNET_ID_1,$SUBNET_ID_2],securityGroups=[$ECS_SG_ID],assignPublicIp=ENABLED}" \
    --load-balancers "targetGroupArn=$API_TG_ARN,containerName=api-nginx,containerPort=8000" 


echo "  ✅ ECS Service created"