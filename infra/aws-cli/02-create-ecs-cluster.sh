#!/bin/bash
set -euo pipefail

aws iam create-role \
      --role-name fynkus-ecs-task-execution-role \
      --assume-role-policy-document file://policy/ecs-trust-policy.json 

aws iam attach-role-policy \
      --role-name fynkus-ecs-task-execution-role \
      --policy-arn arn:aws:iam::aws:policy/service-role/AmazonECSTaskExecutionRolePolicy 

echo "  ✅ ECS Task Execution Role created"

aws ecs create-cluster --cluster-name fynkus-ecs-cluster

MSYS_NO_PATHCONV=1 aws logs create-log-group --log-group-name /ecs/fynkus-ecs-task-def

echo "  ✅ CloudWatch Log Group created"

aws ecs register-task-definition \
    --cli-input-json file://task/task-definition.json 

echo "  ✅ ECS Cluster and Task Definition created"