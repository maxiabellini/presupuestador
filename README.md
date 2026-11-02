# Jazmín Pastelería

Aplicación Symfony 8 para calcular costos de pastelería, administrar recetas reutilizables y generar presupuestos históricos de múltiples productos.

## Puesta en marcha

Requisitos: Docker y Docker Compose.

```bash
make install
```

Abrir <http://localhost:8088> e ingresar con:

- Usuario: `admin@pasteleria.local`
- Contraseña inicial: `admin1234`

La contraseña es solamente para desarrollo local y debe cambiarse antes de exponer el sistema.

Comandos habituales:

```bash
make up       # levanta/reconstruye los contenedores
make migrate  # aplica migraciones pendientes
make assets   # reinstala CSS/JS públicos de EasyAdmin
make test     # ejecuta la suite automatizada
make down     # detiene los contenedores
```

PostgreSQL queda publicado en el puerto `5438` y la aplicación en el `8088`.

## Criterios de cálculo

- Las unidades guardan dimensión y factor a una unidad base: gramo, mililitro o unidad.
- Una conversión entre dimensiones diferentes se rechaza salvo que exista una equivalencia específica para ese insumo (por ejemplo, una taza de harina = 120 g).
- Las operaciones de conversión utilizan `brick/math`, precisión interna de hasta 8 decimales y redondeo `HALF_UP`. Los importes finales se guardan con dos decimales.
- Las recetas pueden incluir otras recetas. El calculador detecta y rechaza dependencias circulares.
- Al cargar una receta en un presupuesto, sus componentes se aplanan y sus precios se copian a líneas históricas. Cambiar un insumo o una receta no altera un presupuesto existente.
- La acción **Recalcular con precios actuales** reemplaza explícitamente esos snapshots.
- El recargo se calcula como `costo × (1 + porcentaje)`; el margen sobre venta como `costo ÷ (1 - porcentaje)`.
- Los gastos compartidos viven una sola vez a nivel presupuesto, evitando duplicarlos entre productos.

## Datos iniciales

La migración inicial incorpora unidades, categorías, equivalencias por ingrediente, insumos de ejemplo, gastos (incluyendo depreciación de horno y batidora) y el usuario administrador.

No incluye facturación electrónica, pagos, tienda, inventario avanzado ni contabilidad completa.
