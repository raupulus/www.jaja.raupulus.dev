<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $table = 'pages';

    protected $fillable = ['title', 'slug', 'excerpt', 'content', 'image', 'keywords', 'status'];


    /*
    public function md(): string
    {
        return '
## Subtítulo Principal (H2)
Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.

### Subtítulo Secundario (H3)
Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.

#### Subtítulo Terciario (H4)
Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.

##### Subtítulo Cuaternario (H5)
Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium.

###### Subtítulo Quinario (H6)
Totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt.

---

## Enlaces y Texto Formateado

Este párrafo contiene [un enlace de ejemplo](https://ejemplo.com) que muestra el estilo de los enlaces. También puedes ver texto **en negrita** y texto *en cursiva* para ver cómo se destacan del texto normal.

Puedes combinar **_texto en negrita y cursiva_** para mayor énfasis cuando sea necesario.

---

## Listas

### Lista No Ordenada
- Primer elemento de la lista
- Segundo elemento con más contenido para ver cómo se comporta el texto largo en las listas
- Tercer elemento
  - Sub-elemento anidado
  - Otro sub-elemento
    - Elemento anidado de tercer nivel
- Cuarto elemento principal

### Lista Ordenada
1. Primer elemento numerado
2. Segundo elemento numerado
3. Tercer elemento con contenido más extenso para probar el comportamiento del texto
   1. Sub-elemento numerado
   2. Otro sub-elemento numerado
4. Cuarto elemento principal

---

---

## Citas

> Esta es una cita de ejemplo que muestra cómo se ve el estilo de blockquote en el diseño. Las citas son útiles para destacar información importante o referencias.

> **Cita con formato:**
>
> Esta cita contiene **texto en negrita** y *texto en cursiva* para mostrar cómo se combinan los estilos dentro de una cita.

---

## Tabla de Ejemplo

| Columna 1 | Columna 2 | Columna 3 | Descripción |
|-----------|-----------|-----------|-------------|
| Fila 1    | Dato A    | 123       | Descripción del primer elemento |
| Fila 2    | Dato B    | 456       | Descripción del segundo elemento |
| Fila 3    | Dato C    | 789       | Descripción del tercer elemento con texto más largo |
| Fila 4    | Dato D    | 101112    | Última fila de ejemplo |

---

## Secciones de Información

### Términos y Condiciones (Ejemplo)
Estos términos y condiciones regulan el uso de nuestro sitio web. Al acceder y utilizar este sitio, aceptas cumplir con estos términos.

### Privacidad y Datos
La protección de tus datos personales es importante para nosotros. Consulta nuestra política de privacidad para más detalles sobre cómo manejamos tu información.

### Contacto
Para cualquier pregunta sobre estos términos, puedes contactarnos a través de:
- Email: contacto@ejemplo.com
- Teléfono: +34 900 000 000
- Dirección: Calle Ejemplo, 123, Ciudad

---

## Lista de Verificación

- [x] Elemento completado
- [x] Otro elemento terminado
- [ ] Elemento pendiente
- [ ] Otro elemento por hacer

---
';
    }
*/

    /**
     * Devuelve la url hacia la imagen principal de la página.
     *
     * @return string|null
     */
    public function getUrlImageAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function getHtmlContent(): string
    {
        $parsedown = new \Parsedown();
        $parsedown->setSafeMode(true);

        return $parsedown->text($this->content);
    }



}
