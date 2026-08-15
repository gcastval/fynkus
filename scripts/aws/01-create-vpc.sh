#!/bin/bash
set -euo pipefail

VPC_ID=$(aws ec2 create-vpc \
      --cidr-block 10.0.0.0/16 \
      --tag-specifications "ResourceType=vpc,Tags=[{Key=Name,Value=fynkus-vpc}]" \
      --query 'Vpc.VpcId' \
      --output text)

aws ec2 modify-vpc-attribute \
      --vpc-id $VPC_ID \
      --enable-dns-hostnames 

SUBNET_ID_1=$(aws ec2 create-subnet \
      --vpc-id $VPC_ID \
      --cidr-block 10.0.1.0/24 \
      --availability-zone eu-west-1a \
      --tag-specifications "ResourceType=subnet,Tags=[{Key=Name,Value=fynkus-public-subnet-1}]" \
      --query 'Subnet.SubnetId' \
      --output text)

SUBNET_ID_2=$(aws ec2 create-subnet \
      --vpc-id $VPC_ID \
      --cidr-block 10.0.2.0/24 \
      --availability-zone eu-west-1b \
      --tag-specifications "ResourceType=subnet,Tags=[{Key=Name,Value=fynkus-public-subnet-2}]" \
      --query 'Subnet.SubnetId' \
      --output text)


echo "  ✅ VPC and Subnets created"

IGW_ID=$(aws ec2 create-internet-gateway \
      --tag-specifications "ResourceType=internet-gateway,Tags=[{Key=Name,Value=fynkus-igw}]" \
      --query 'InternetGateway.InternetGatewayId' \
      --output text)

aws ec2 attach-internet-gateway \
      --internet-gateway-id $IGW_ID \
      --vpc-id $VPC_ID 

ROUTE_TABLE_ID=$(aws ec2 create-route-table \
      --vpc-id $VPC_ID \
      --tag-specifications "ResourceType=route-table,Tags=[{Key=Name,Value=fynkus-igw-public-subnet-route-table}]" \
      --query 'RouteTable.RouteTableId' \
      --output text) 

aws ec2 create-route \
      --route-table-id $ROUTE_TABLE_ID \
      --destination-cidr-block 0.0.0.0/0 \
      --gateway-id $IGW_ID 

aws ec2 associate-route-table \
      --route-table-id $ROUTE_TABLE_ID \
      --subnet-id $SUBNET_ID_1 

aws ec2 associate-route-table \
      --route-table-id $ROUTE_TABLE_ID \
      --subnet-id $SUBNET_ID_2 

echo "  ✅ IGW to Public Subnets route table created"


ALB_SG_ID=$(aws ec2 create-security-group \
      --group-name fynkus-alb-sg \
      --description "Security group for the ALB" \
      --vpc-id $VPC_ID \
      --tag-specifications "ResourceType=security-group,Tags=[{Key=Name,Value=fynkus-alb-sg}]" \
      --query 'GroupId' \
      --output text)

aws ec2 authorize-security-group-ingress \
      --group-id $ALB_SG_ID \
      --protocol tcp \
      --port 80 \
      --cidr 0.0.0.0/0 

aws ec2 authorize-security-group-ingress \
      --group-id $ALB_SG_ID \
      --protocol tcp \
      --port 443 \
      --cidr 0.0.0.0/0 

ECS_SG_ID=$(aws ec2 create-security-group \
      --group-name fynkus-ecs-sg \
      --description "Security group for the ECS tasks" \
      --vpc-id $VPC_ID \
      --tag-specifications "ResourceType=security-group,Tags=[{Key=Name,Value=fynkus-ecs-sg}]" \
      --query 'GroupId' \
      --output text)

aws ec2 authorize-security-group-ingress \
      --group-id $ECS_SG_ID \
      --protocol tcp \
      --port 8000 \
      --source-group $ALB_SG_ID 

echo "  ✅ ALB and ECS Security Groups created"