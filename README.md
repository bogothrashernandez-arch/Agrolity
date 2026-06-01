# Agrolity

Plataforma de comercio electrónico para productos agrícolas colombianos.

## Tecnologías utilizadas
- PHP 8.x
- MySQL
- HTML5/CSS3
- JavaScript
- Mercado Pago API

## Estructura del proyecto
- `CSS/` - Estilos de la aplicación
- `JS/` - Funcionalidades JavaScript
- `img/` - Imágenes estáticas
- `Uploads/` - Imágenes subidas por productores

-                     ┌─────────────┐
                    │   USUARIO   │
                    └──────┬──────┘
                           │
         ┌─────────────────┼─────────────────┐
         ↓                 ↓                 ↓
    ┌─────────┐      ┌──────────┐      ┌──────────┐
    │  INDEX  │      │  LOGIN   │      │ REGISTRO │
    └────┬────┘      └────┬─────┘      └────┬─────┘
         │                │                 │
         ↓                ↓                 ↓
    ┌─────────┐      ┌──────────┐      ┌──────────┐
    │PRODUCTOS│      │PERFIL    │      │DASHBOARD │
    └────┬────┘      │CLIENTE   │      │PRODUCTOR │
         │           └──────────┘      └────┬─────┘
         ↓                                  ↓
    ┌─────────┐                        ┌──────────┐
    │ CARRITO │                        │PUBLICAR  │
    │(session │                        │PRODUCTO  │
    │Storage) │                        └────┬─────┘
    └────┬────┘                             │
         ↓                                  ↓
    ┌─────────┐                        ┌──────────┐
    │CHECKOUT │                        │EDITAR/   │
    │         │                        │ELIMINAR  │
    └────┬────┘                        └──────────┘
         ↓
    ┌─────────┐
    │MERCADO  │
    │PAGO     │
    └────┬────┘
         ↓
    ┌─────────┐
    │PEDIDO   │
    │GUARDADO │
    └─────────┘

## Autor
Cristrian David Hernandez Barrantes
Tecnologo en desarrollo multimedia y web
Servicio Nacional de Aprendizaje SENA. 2026
