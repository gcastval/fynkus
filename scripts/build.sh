#!/bin/bash
set -euo pipefail

cd "$(dirname "$0")/.."

REGISTRY="ghcr.io/gcastval/fynkus"
COMMIT_HASH="${COMMIT_HASH}"

declare -A IMAGES=(
  ["api-nginx"]="docker/nginx/Dockerfile-api-nginx"
  ["api"]="docker/Dockerfile"
  ["app"]="docker/Dockerfile-app"
)

for name in "${!IMAGES[@]}"; do
  dockerfile="${IMAGES[$name]}"
  image="$REGISTRY/$name"

  docker build \
    -f "$dockerfile" \
    -t "$image:$COMMIT_HASH" \
    -t "$image:lts" \
    .

  docker push "$image:$COMMIT_HASH"
  docker push "$image:lts"
done
