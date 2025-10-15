# fynkus

## Instrucciones de ejecución

- **Levantar el proyecto**:

```bash
docker-compose up --build
```
una vez levantado, acceder a `http://localhost:3000/` para ver la aplicación.

- **Ejecutar los tests (backend)**:

```bash
cd api
composer install
make test
```

- **Requisitos**:
  - Debes tener instalado `make` (se utilizan Makefiles para agrupar instrucciones).

## Decisiones de arquitectura

- **Arquitectura hexagonal (backend)**: Se ha utilizado porque facilita la realización de tests (un criterio con gran peso en la decisión), separa responsabilidades y mantiene un dominio más limpio.
- **Entidades acopladas a Doctrine**: Se han mantenido con anotaciones. El acoplamiento es mínimo y el beneficio en simplicidad y productividad es alto. Evita duplicar entidades en la capa de infraestructura, reduciendo complejidad sin comprometer la claridad del dominio.
- **Value Object**: Se ha implementado el patrón Value Object mediante la clase HourCollection para gestionar las reservas de horas de forma consistente y encapsular la lógica asociada.
- **Endpoints expuestos**:
  - `POST` `common-area/reserve`
  - `GET` `common-area/schedule/{area}/{date}`

  Si se consulta una fecha no registrada, la API devuelve un rango de horas de 9–21, todas disponibles. El `GET` devuelve un JSON con el formato:

```json
{
  "hours": [
    { "hour": 9,  "reserved": false },
    { "hour": 10, "reserved": true }
    
  ]
}
```
- Se han utilizado DTOs en lugar de Commands para la interacción entre los controllers y la capa de aplicación.

### Trade-offs

- Debido al límite de tiempo, no se han implementado tests en el frontend.
- Para ser más pragmáticos, las horas se guardan en una columna `json` en la base de datos (es un MVP y no se requieren analíticas de horas más demandadas por area).
- Para ejecutar los tests es necesario instalar dependencias localmente en lugar de ejecutar toda la suite totalmente dockerizada.

## Posibles mejoras

- Creación de áreas dinámicas para que no estén definidas de forma estática ni en el back ni en el front.
- Definir rangos de fechas/hora reservables (por ejemplo, de lunes a viernes) y limitar horas en función del día de la semana.
- Incorporar Swagger para generar una documentación clara y actualizada de los endpoints, los parámetros y las respuestas esperadas, evitando depender del código para comprender cómo interactuar con la API.
