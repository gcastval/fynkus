#!/bin/bash

set -euo pipefail

cd "$(dirname "$0")"

export AWS_PROFILE="${AWS_PROFILE:-admin}"
export AWS_PAGER=""

source ./01-create-vpc.sh
source ./02-create-ecs-cluster.sh
source ./03-create-ecs-service.sh