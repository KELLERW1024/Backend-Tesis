<?php

namespace App\Constants;

class Prompts
{
// PROMPT_INICIAL y PROMPT_ESPECIFICO ahora son propios de cada plan: app/Services/Prompts/

public const PROMPT_IMAGEN = <<<TEXT
Crea una escena fotográfica realista basada en el estricto significado del siguiente concepto:

[Contenido]

La imagen debe parecer una fotografía, NO una infografía, NO un diagrama,
NO un mapa conceptual y NO una presentación.

Muestra personas, objetos, acciones y un entorno relacionados con el concepto.

IMPORTANTE:
No mostrar ninguna palabra escrita.
No mostrar ninguna letra.
No mostrar ningún número.
No mostrar ningún texto.
No mostrar carteles.
No mostrar pizarras.
No mostrar menús.
No mostrar documentos.
No mostrar etiquetas.
No mostrar pantallas.
No mostrar interfaces.
No mostrar logotipos.
No mostrar marcas de agua.

No crear cajas, tarjetas, paneles, diagramas, flechas ni elementos
gráficos que puedan contener texto.

La imagen debe comunicar el concepto exclusivamente mediante una escena
visual y fotográfica.

Estilo profesional, moderno, natural y realista.
TEXT;


}
