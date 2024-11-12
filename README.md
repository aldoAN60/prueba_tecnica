# Práctica Técnica para Desarrollador Backend

Este repositorio contiene el código de una aplicación desarrollada en Laravel y PHP que simula un Dashboard con varias funcionalidades para gestionar productos y órdenes. A continuación, se describen las funcionalidades implementadas y los aspectos técnicos del proyecto.

## Funcionalidades

1. **Top 5 productos más vendidos**
   - Se implementó una API que consume datos desde una fuente externa y permite listar los 5 productos más vendidos.
   - Se pueden aplicar filtros por fechas para personalizar los resultados.

2. **Productos con órdenes asociadas**
   - Se desarrolló una funcionalidad para listar productos junto con las órdenes en las que están incluidos.
   - Se añadió una gráfica que representa visualmente el contenido de la lista.
   - Se pueden aplicar filtros de fechas tanto para la lista como para la gráfica.

3. **Historial de órdenes y detalle de órdenes**
   - Se permite listar el historial completo de órdenes con la posibilidad de ver el detalle de cada una.
   - Se incluye una gráfica que muestra el contenido de la lista de órdenes.
   - Se pueden aplicar filtros de fechas tanto para la lista como para la gráfica.

4. **Sumatoria de órdenes filtradas y conversiones de moneda**
   - Se muestra la sumatoria de las órdenes filtradas y sus respectivas conversiones a Dólares, Euros y Bolívares.
   - Se utiliza una API de conversión de monedas para obtener los valores actualizados.

## APIs Externas Utilizadas

El proyecto consume las siguientes APIs para obtener y procesar datos:
- `/api/products`: Para listar los productos.
- `/api/orders`: Para obtener información sobre las órdenes.
- `/api/orders/list_record`: Para acceder al historial de órdenes.
- `/api/orders/detail`: Para mostrar el detalle de una orden específica.
- `/api/orders/create`: Para la creación de nuevas órdenes.

## Tecnologías Utilizadas

- **PHP con Laravel**: Utilizado como lenguaje de programación y framework principal.
- **API REST**: Implementación de endpoints que consumen otras APIs.
- **APIs Externas**:
  - **IXAYA**: [https://sandbox.ixaya.net](https://sandbox.ixaya.net)
  - **RapidAPI - Currency Converter**: [https://rapidapi.com/natkapral/api/currency-converter5](https://rapidapi.com/natkapral/api/currency-converter5)
- **Tailwind CSS**: Utilizado para el diseño y la personalización de la interfaz de usuario.
- **Chart.js**: Librería utilizada para la representación gráfica de los datos.

## Configuración y Ejecución

Antes de comenzar, asegúrate de tener instalados los siguientes requisitos en tu entorno de desarrollo:
- **PHP**: Lenguaje de programación para ejecutar la aplicación.
- **Composer**: Administrador de dependencias de PHP.
- **Laravel**: Framework utilizado para el desarrollo del proyecto.
- **Node.js y npm**: Necesarios para la gestión de paquetes y la ejecución de Vite.

### Pasos para la configuración y ejecución:

1. **Clonar el repositorio**:
   Abre una terminal y ejecuta el siguiente comando para clonar el repositorio en tu máquina local:
   ```bash
   git clone https://github.com/aldoAN60/prueba_tecnica.git

2. **Abrir el proyecto en el editor de archivos de tu preferencia**
   
3. **posterormente abrir la terminal y ejecutar el comando**
    ```bash
   npm run dev
4. **Abrir la URL que muestra la termina**
```bash
http://prueba_tecnica.test

