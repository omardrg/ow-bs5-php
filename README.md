# Demo de sitio web creado con Bootstrap 5 y php
**Este sitio web tiene una finalidad puramente pedagógica.**

Este sitio web utiliza la librería de [Bootstrap](https://getbootstrap.esdocu.com/) para hacer un sitio web dinámico y _repsonsive_. De todas formas, necesitará algunas configuraciones por parte del usuario en el formulario de envío.

Se puede ver la demo [aquí](https://onaweb.es/ow-bs5-php/)

## Configuración del formulario
### Archivo */index.html*, línea *34*:
Añadir la clave pública de [Google Recaptcha](https://www.google.com/recaptcha/admin/) V3

### Archivo */assets/js/funciones.js*, línea *68*:
Añadir la clave pública de [Google Recaptcha](https://www.google.com/recaptcha/admin/) V3

### Archivo */envio.php*, línea *8*:
Añadir la configuración de envío del email:
1. Dirección a la que se envía el formulario
2. Usuario de una cuenta de correo existente para validar el envío a través de los servidores SMTP de Gmail
3. Contraseña de una cuenta de correo existente para validar el envío a través de los servidores SMTP de Gmail
4. Clave privada de [Google Recaptcha](https://www.google.com/recaptcha/admin/)