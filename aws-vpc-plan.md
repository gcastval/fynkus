# AWS VPC Plan — fynkus

## Arquitectura

```
Internet
   |
   v
Internet Gateway (attachado a la VPC)
   |
   v
ALB (en 2 subnets publicas, SG: inbound 80/443 desde 0.0.0.0/0)
   |
   v
ECS Tasks / Fargate (en subnet(s) publica(s), SG: inbound 8000 solo desde el SG del ALB, assignPublicIp=ENABLED)
```

Sin NAT Gateway. Tasks en subnet publica con IP publica propia (necesaria para poder hacer pull de imagenes desde GHCR, ya que no hay NAT).

## Componentes a crear

1. **VPC** (CIDR propio, ej `10.0.0.0/16`)
2. **Internet Gateway** + attach a la VPC
3. **2 subnets publicas** (distintas AZs — requisito duro de `create-load-balancer`, que exige minimo 2 subnets en 2 AZs distintas)
4. **Route table publica**: `0.0.0.0/0` → IGW, asociada a ambas subnets
5. **Security Group ALB**: inbound 80 (y 443 si hay TLS) desde `0.0.0.0/0`
6. **Security Group ECS tasks**: inbound 8000 solo desde el SG del ALB (no publico)
7. **ALB** + **Target Group** (tipo `ip`, puerto 8000, health check path)
8. **Listener** del ALB → target group
9. **ECS service** actualizado: `network-configuration` con las 2 subnets publicas, el SG de tasks, `assignPublicIp=ENABLED`, y `--load-balancers` apuntando al target group

## Pendiente / a revisar

- `docker/nginx/api.conf` necesita exponer un endpoint de health check (ej. `/health` o `/`) para que el target group pueda marcar la task como healthy.
- Decidir si se agrega TLS (requiere certificado en ACM) para el listener 443.

## Trade-offs de no usar NAT Gateway

- Ahorro: sin costo de NAT (~$32-65/mes + trafico procesado).
- Riesgo: las tasks tienen IP publica asignada por AWS. El Security Group bloquea todo salvo desde el ALB, pero la superficie de exposicion es mayor que con subnets privadas.
- Alternativa si mas adelante se quiere subnet privada: mover imagenes a ECR + VPC Interface Endpoints (`ecr.api`, `ecr.dkr`, `s3` gateway endpoint), ya que GHCR no es alcanzable sin salida a internet (NAT o IP publica).

## Por que ALB necesita 2 subnets en 2 AZs distintas

El API `elbv2` (`create-load-balancer`) exige minimo 2 subnets en al menos 2 AZs distintas; falla con error de validacion si no se cumple. Motivo: AWS provisiona un nodo del balanceador por AZ indicada, con IP propia en cada subnet. Si una AZ cae, el ALB sigue sirviendo desde la otra. Con una sola AZ, un problema en esa zona tira todo el entry point, incluso si el resto de la region sigue funcionando.
