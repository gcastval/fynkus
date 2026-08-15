# ECS / VPC / ALB — Aprendizaje

## Por que subnets publicas y privadas necesitan al menos 2 AZs para el ALB

`create-load-balancer` exige minimo 2 subnets en al menos 2 Availability Zones distintas; falla con error de validacion si no se cumple. Motivo: AWS provisiona un nodo del balanceador por AZ indicada, con IP propia en cada subnet. Si una AZ cae, el ALB sigue sirviendo desde la otra. Con una sola AZ, un problema en esa zona tira todo el entry point aunque el resto de la region siga funcionando.

## Por que las tasks en subnet privada sin NAT no funcionan

Sin NAT Gateway, una subnet privada no tiene ninguna ruta hacia afuera. Las tasks no podrian hacer `docker pull` de `ghcr.io` (internet publico, no alcanzable por VPC endpoint). Opciones si se quiere subnet privada sin NAT: mover imagenes a ECR + VPC Interface Endpoints (`ecr.api`, `ecr.dkr`, `s3` gateway endpoint). Si no, hace falta NAT o poner las tasks en subnet publica con IP publica.

## Route tables: destino, no origen

Una entrada de route table es `destino -> target`, igual que un router IP real: se decide mirando la IP de destino del paquete, nunca la de origen. El "origen" no es un campo porque ya esta determinado por la asociacion (`associate-route-table` liga la tabla a subnets especificas — eso es el "from"). Dentro de la tabla, lo que varia por entrada es el destino:

```
10.0.0.0/16 -> local     (ruta automatica, no se puede borrar)
0.0.0.0/0   -> igw-xxxx  (la agregamos nosotros)
```

`create-route-table` crea la tabla vacia salvo la ruta local. `create-route` agrega la regla del target (ej. IGW). `associate-route-table` liga la tabla a una subnet — sin esto, la subnet sigue usando la main route table de la VPC (sin ruta a internet), aunque la tabla nueva ya tenga la ruta creada.

Una subnet es "publica" unicamente por convencion: porque su route table asociada tiene una ruta a un IGW. No existe un atributo `--public` en `create-subnet`.

## El IGW no es un target de asociacion, es un target de ruta

El Internet Gateway vive a nivel VPC, no dentro de ninguna subnet. No se le asocia una route table; se usa como target dentro de una entrada de ruta. La asociacion siempre es route table <-> subnet.

## Security Groups: stateful, ingress explicito

`create-security-group` agrega egress `0.0.0.0/0` por defecto, pero cero ingress — todo el trafico entrante bloqueado hasta agregar reglas explicitas con `authorize-security-group-ingress`. "Stateful" implica que si se permite el ingress, la respuesta saliente correspondiente se permite automaticamente sin regla de egress explicita.

El origen de una regla de ingress puede ser un CIDR o `--source-group <sg-id>` (otro Security Group). Con `--source-group`, la regla matchea cualquier ENI que tenga ese SG asociado, sin importar su IP — asi se restringe el SG de las tasks para que solo el ALB pueda hablarle (en vez de exponerlo a `0.0.0.0/0`).

## Por que el ECS Service necesita IP publica (sin NAT)

El ALB resuelve el trafico entrante, pero no el saliente. Las tasks siguen necesitando salir a internet (pull de imagen desde GHCR, llamadas salientes del codigo). El IGW solo reenvia trafico de una ENI que tenga IP publica asignada — no hace NAT/traduccion para ENIs con solo IP privada (eso es lo que hace especificamente un NAT Gateway). Por eso, sin NAT, `assignPublicIp=ENABLED` es obligatorio aunque el ALB ya maneje el trafico entrante.

## HTTP vs HTTPS en los listeners

El puerto 80 usa protocolo `HTTP` en el listener y no requiere certificado. El certificado en ACM solo hace falta cuando el protocolo del listener es `HTTPS`, para el handshake TLS. Sin dominio propio se puede arrancar solo con HTTP en el 80, usando el DNS name automatico que da el ALB (`*.elb.amazonaws.com`); mas adelante se agrega el listener 443 con ACM y se cambia el 80 a redirect en vez de forward, sin romper lo ya montado.

## Target Groups

Agrupan destinos (targets) hacia los que el ALB reenvia trafico, mas la configuracion de health checks. El listener no reenvia trafico directamente, evalua reglas y cada regla apunta a un target group.

Con ECS/Fargate el target group es `--target-type ip` (no `instance`): los targets son las IPs de las ENIs de las tasks. ECS registra/desregistra automaticamente la IP de cada task en el target group segun su ciclo de vida — no se hace manualmente.

Se necesita un target group **por puerto expuesto que debe ser alcanzable desde el ALB**, no por container en general. En este proyecto: `api-nginx` (8000) necesita target group porque es el entry point publico; `api` (9000) no, porque `api-nginx` le habla por `localhost` dentro de la misma task (mismo `awsvpc`, misma ENI).

Si se agregara `app` (frontend, 3000) a la misma task definition, haria falta un segundo target group (`fynkus-app-tg`), porque tambien sirve contenido directo al publico. Un mismo listener puede tener multiples `create-rule` (por `path-pattern` o `host-header`) apuntando a target groups distintos, con un `--default-actions` como fallback.

## Como sabe el ALB a que container mandar el trafico

En `create-service`, `--load-balancers` lleva una tripleta por cada container publico: `targetGroupArn`, `containerName` (debe matchear el `name` en `containerDefinitions`), `containerPort` (debe matchear el `portMappings.containerPort`). Con eso, cuando la task pasa a `RUNNING`, ECS Agent llama automaticamente a `register-targets` en cada target group, usando la IP de la ENI de la task + el `containerPort` correspondiente.

Como todos los containers de una task en modo `awsvpc` comparten una unica ENI (una sola IP), el target group no distingue por IP — distingue por **puerto**. La misma IP puede estar registrada en dos target groups distintos bajo dos puertos distintos (8000 para `api-nginx`, 3000 para `app`). Cuando la task se detiene, ECS hace el `deregister-targets` correspondiente automaticamente.

## Flujo completo

```
Internet
   |
   v
Internet Gateway (attachado a la VPC)
   |
   v
ALB (subnets publicas, SG: ingress 80/443 desde 0.0.0.0/0)
   |
   v
Listener (evalua reglas por path/host) -> Target Group(s)
   |
   v
ECS Tasks / Fargate (SG: ingress solo desde el SG del ALB, IP publica si no hay NAT)
```
