## Proyecto ejemplo

### Contenido

- Estructura básica arquitectura hexagonal
- Tests unitarios con phpunit y funcionales con behat
- Doctrine ORM, migraciones, fixtures
- Handlers con Symfony Messenger (síncrono)
- Documentación OpenAPI
- CI básico con Github

### Requisitos

- Docker
- Docker-compose
- Make

### Instalación

Levantar el proyecto:
```
make
```

Ejecutar tests:
```
make test
```

### Descripcion

Una pequeña API que gestione (CRUD) clubes, jugadores y entrenadores.
Sobre estos modelos (clubes, jugadores y entrenadores) se deberán de poder realizar las siguientes operaciones:

Clubes

- [x] Dar de alta un club
- [x] Dar de alta un jugador en el club
- [x] Dar de alta un entrenador en el club
- [x] Modificar el presupuesto de un club
- [x] Dar de baja un jugador del club
- [x] Dar de baja un entrenador del club
- [x] Listar jugadores de un club con posibilidad de filtrar por una de las propiedades (por ejemplo nombre) y con paginación
Jugadores
- [x] Dar de alta un jugador sin pertenecer a un club

Entrenadores

- [x] Dar de alta un entrenador sin pertenecer a un club

Cada club deberá de tener un presupuesto, este presupuesto se asignará en el alta del club.

Al dar de alta un jugador/entrenador a un club se deberá especificar el salario del jugador/entrenador para ese club, ese salario debe de salir del
presupuesto del club y el presupuesto nunca puede ser menor que cero.

Al modificar el presupuesto de un club se tiene que tener en cuenta los salarios actuales de ese club.

Un jugador/entrenador no podrá estar dado de alta en mas de un club.

Cada vez que se de alta o baja a un jugador/entrenador tendrá que ser notificado por email(en un futuro se está pensando en pueda ser
notificado por otras vías (sms, whatsapp, ...) por lo tanto lo tendremos que dejar abierta esta posibilidad sin ser implementada actualmente)
Requerimientos

Requisitos mínimos:

- [x] Symfony 4.4 o superior.
- [x] Se deberá utilizar Doctrine como ORM.
- [x] Se pueden utilizar bundles de terceros (excepto API Platform).
- [x] El tipo de contenido siempre debe ser application/json.
- [X] Entregar un dump de la DB con datos de prueba. (Fixtures/Migrations)
- [x] Se valorará muy positivamente una colección de Postman.
- [x] Se valorará muy positivamente el uso de docker.
- [x] Se valorará muy positivamente la estructura y limpieza del código, tests unitarios, el uso de “Patrones de diseño”, el uso de los verbos
REST y las buenas prácticas de Symfony.

### Docs

- Swagger en http://localhost:7080
- Collection de Postman en docs/postman_collection.js

### TODO

- [ ] Mejorar gestión de errores HTTP
- [ ] Añadir algunos casos de uso asíncronos
- [ ] Implementar logging
- [ ] Añadir cache
- [ ] Modelo lectura/escritura
