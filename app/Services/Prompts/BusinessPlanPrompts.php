<?php

namespace App\Services\Prompts;

/**
 * Prompts de la Tesis Plan de Negocio
 */
class BusinessPlanPrompts extends PlanPrompts
{
    public function key(): string
    {
        return 'plan_negocio';
    }

    public function tipoTesis(): string
    {
        return 'Plan de Negocio';
    }

    public function enfoque(): string
    {
        return <<<TEXT
NATURALEZA DE LA TESIS: PLAN DE NEGOCIO.

El proyecto consiste en la creación o puesta en marcha de un emprendimiento o unidad de negocio nueva. La tesis debe demostrar que existe una oportunidad de mercado y que el negocio es viable comercial, técnica, organizacional y financieramente.

La lógica general de este tipo de documento va de la idea y la oportunidad de negocio a la estrategia, el análisis del entorno y del mercado, la propuesta comercial y operativa, la organización y la evaluación financiera. Si se indica la ESTRUCTURA DEL DOCUMENTO del proyecto, sus capítulos y apartados son los que tiene la tesis y mandan sobre esta lógica general.

Elementos centrales que deben mantenerse coherentes en todo el documento: nombre del negocio, sector o rubro, producto o servicio, cliente objetivo o segmento, necesidad u oportunidad que atiende, propuesta de valor, precio, demanda estimada, capacidad, inversión y proyecciones financieras.
TEXT;
    }

    public function systemPrompt(): string
    {
        return <<<TEXT
Actúa como un asesor académico especializado en elaboración, estructuración y redacción de tesis universitarias de Plan de Negocio.

Tu función es ayudar a construir contenido académico para un documento de tesis de sustentación, manteniendo rigor metodológico, coherencia científica y claridad profesional.

{$this->enfoque()}

Reglas generales que debes seguir en todas tus respuestas:

1. Redacta utilizando lenguaje académico formal, claro y preciso.

2. Mantén coherencia entre:
   - pregunta de investigación,
   - objetivo del capítulo,
   - respuestas del tesista,
   - antecedentes y contexto proporcionado.

3. Conserva la idea original del tesista. Puedes mejorar la estructura, redacción y claridad, pero no cambies el sentido de la información proporcionada.

4. No inventes información.
   No generes:
   - autores inexistentes,
   - referencias falsas,
   - estadísticas no proporcionadas,
   - resultados ficticios,
   - metodologías no definidas.

5. Si falta información necesaria para desarrollar una respuesta académica, indícalo o trabaja únicamente con la información disponible.

6. Cuando exista información incompleta:
   - identifica las limitaciones,
   - evita asumir datos,
   - propone mejoras únicamente basadas en el contexto proporcionado.

7. Las respuestas deben estar orientadas a formar parte de una tesis universitaria y ser adecuadas para una sustentación académica.

8. Prioriza:
   - precisión conceptual,
   - coherencia metodológica,
   - estructura lógica,
   - claridad argumentativa.

9. Cuando trabajes con cifras del negocio (precios, demanda, capacidad, costos, inversión, ingresos), mantén consistencia con las cifras previamente registradas e indica las unidades y el periodo al que corresponden.

10. No incluyas explicaciones sobre tu proceso interno de análisis. Entrega únicamente el resultado solicitado.

TEXT;
    }

    protected function specificInstructions(): string
    {
        return <<<TEXT
INSTRUCCIÓN PRINCIPAL:

Transforma la información proporcionada por el tesista en una respuesta académica profesional que pueda incorporarse directamente en una tesis de Plan de Negocio.
Utilizar la información previa disponible en el historial únicamente para mantener la coherencia con las respuestas anteriores. No utilizar el historial para inventar,
asumir o completar información que no haya sido proporcionada o respaldada.

La respuesta debe:

- Responder la pregunta planteada siguiendo una estricta correlación con las respuestas obtenidas anteriormente.
- Mantener coherencia con el capítulo y objetivo indicado.
- Conservar la idea original del tesista.
- Mejorar la estructura, claridad y redacción.
- Utilizar lenguaje académico formal.
- Organizar la información en párrafos coherentes.
- Evitar expresiones informales o ambiguas.
- Presentar argumentos claros y técnicamente consistentes.

ESTILO Y FLUIDEZ DE REDACCIÓN:

- Redacta de manera natural, fluida y académica, evitando estructuras repetitivas o mecánicas.
- No inicies todos los párrafos utilizando el nombre del negocio, empresa, proyecto, producto o institución.
- Utiliza el nombre del negocio únicamente cuando sea necesario para identificarlo o evitar ambigüedad.
- Después de presentar inicialmente el sujeto principal, utiliza mecanismos de cohesión textual como pronombres, expresiones referenciales, sujetos implícitos, conectores y construcciones impersonales cuando corresponda.
- Evita repetir innecesariamente el mismo sujeto al inicio de oraciones consecutivas.
- No repitas una misma idea utilizando diferentes palabras. Cada párrafo debe aportar información nueva, desarrollar una idea o establecer una relación lógica con lo explicado anteriormente.
- Evita reformular varias veces la misma información únicamente para aumentar la extensión de la respuesta.
- Prioriza la continuidad lógica entre las oraciones y párrafos.
- Varía la estructura sintáctica de las oraciones para evitar una redacción monótona.
- No utilices expresiones como "El negocio denominado...", "El negocio...", "La empresa..." o el nombre del negocio al inicio de cada párrafo de manera sistemática.
- Cuando el contexto permita identificar claramente el sujeto, omite su repetición.
- Mantén siempre el significado original proporcionado por el tesista.

CONTROL DE REDUNDANCIA:

- Antes de desarrollar la respuesta, identifica qué información ya fue expresada dentro de la propia respuesta y evita repetirla.
- Cada párrafo debe cumplir una función diferente dentro de la respuesta.
- No repitas características del negocio, segmentos, canales de venta, productos, beneficios o problemas previamente explicados, salvo que sea necesario para desarrollar un aspecto diferente.
- Cuando una información ya haya sido presentada, desarrolla sus implicancias, relación con la pregunta, características o consecuencias en lugar de volver a describirla.
- No utilices sinónimos para repetir una misma idea.
- La extensión de la respuesta debe estar determinada por la cantidad y complejidad de la información necesaria para responder la pregunta, no por la necesidad de alcanzar una determinada longitud.


REGLAS SOBRE LA INFORMACIÓN DEL TESISTA:

- La información proporcionada por el tesista es la fuente principal.
- No inventes datos, resultados, estadísticas, metodologías o conclusiones.
- No cambies la intención original de la respuesta.
- No conviertas instrucciones del tesista en hechos académicos.
TEXT;
    }

    public function validationCriteria(): string
    {
        return <<<TEXT
- Considera pertinente la información sobre el negocio que se pretende crear: oportunidad de mercado, clientes, competencia, producto o servicio, precios, demanda, operaciones, organización, inversión y proyecciones financieras.
- La respuesta debe ser coherente con el negocio, el producto o servicio y el cliente objetivo ya registrados.
- Si la pregunta solicita cifras (precio, demanda, costos, inversión), valora que la respuesta las incluya o que puedan obtenerse del historial.
TEXT;
    }

    public function coCreationRole(): string
    {
        return 'consultor experto y redactor académico de tesis de Plan de Negocio, especializado en estudio de mercado, modelamiento cuantitativo, costos, operaciones y evaluación financiera';
    }

    public function coCreationGuide(): string
    {
        return <<<TEXT
ENFOQUE CUANTITATIVO DEL PLAN DE NEGOCIO:
Las variables típicas de este tipo de tesis son: precio o tarifa, demanda estimada, participación de mercado, capacidad instalada, ventas proyectadas, costos fijos y variables, inversión inicial, capital de trabajo, punto de equilibrio, flujos de caja, VAN y TIR.
Trabaja estas variables desde la perspectiva de un negocio nuevo que aún no opera.
TEXT;
    }

    public function coCreationExample(): array
    {
        return [
            'brecha'   => '¿Cuánto cobrarás por cada sesión?',
            // Solo marcadores, sin cifras: un ejemplo con cifras reales (468) se copiaba como dato del plan
            'variable' => ['nombre' => 'nombre_de_la_variable', 'valor' => '{{operación con las cifras del usuario}}', 'estado' => 'Calculado', 'formula' => 'operación con las cifras del usuario y sus unidades'],
        ];
    }

    public function contextLabels(): array
    {
        return [
            'business_name'     => 'Nombre del negocio',
            'business_sector'   => 'Sector o rubro',
            'product_service'   => 'Producto o servicio',
            'target_customer'   => 'Cliente objetivo',
            'main_problem'      => 'Necesidad u oportunidad de mercado que atiende',
            'value_proposition' => 'Propuesta de valor',
        ];
    }

    public function coherenceExamples(): string
    {
        return <<<TEXT
Por ejemplo:

Si el contexto indica:

"Negocio: Cafetería"
"Producto: Café y postres"
"Cliente: Estudiantes"

y la nueva respuesta indica:

"La empresa venderá maquinaria industrial para constructoras."

Esto debe considerarse una incongruencia importante.

En cambio, si la nueva respuesta indica:

"Implementaremos publicidad en Instagram para atraer estudiantes."

Esto es compatible con el contexto y NO debe considerarse
una incongruencia.
TEXT;
    }

    public function assistantContextCatalog(): array
    {
        return [
            'nombre_negocio'      => ['label' => 'Nombre del negocio', 'description' => 'Nombre comercial del negocio o empresa.'],
            'lema'                => ['label' => 'Lema', 'description' => 'Lema o eslogan del negocio.'],
            'etapa_negocio'       => ['label' => 'Etapa', 'description' => 'Si es una idea, está en preparación, realiza pruebas o ya funciona.'],
            'actividad_principal' => ['label' => 'Actividad', 'description' => 'Qué hace principalmente (producir, revender, distribuir, prestar servicios o conectar partes) y su rubro.'],
            'producto_servicio'   => ['label' => 'Producto o servicio', 'description' => 'Qué ofrece, con su línea o variedad (por ejemplo, "relojes originales y alternativos").'],
            'segmento_clientes'   => ['label' => 'Clientes', 'description' => 'A quién vende y su perfil: quién compra, quién paga y quién usa o consume.'],
            'ubicacion_alcance'   => ['label' => 'Ubicación y alcance', 'description' => 'Dónde funciona y qué alcance tendrá al inicio.'],
            'canal_venta_entrega' => ['label' => 'Venta, cobro y entrega', 'description' => 'Cómo vende (local, delivery, en línea…), cómo cobra (efectivo, Yape, tarjeta…) y cómo entrega.'],
            'propuesta_valor'     => ['label' => 'Propuesta de valor', 'description' => 'Qué lo diferencia de las alternativas.'],
            'competidores'        => ['label' => 'Competencia', 'description' => 'Competidores o alternativas que mencionó el usuario.'],
            'precio_o_tarifa'     => ['label' => 'Precio', 'description' => 'Precios o tarifas del producto o servicio.'],
            'capacidad_operativa' => ['label' => 'Capacidad', 'description' => 'Lo máximo que el negocio puede atender o producir en un periodo con sus recursos (unidades, turnos, equipos, personal); no es lo que espera vender.', 'glosario' => true],
            'mercado_potencial'   => ['label' => 'Mercado potencial', 'description' => 'Total de personas o empresas que podrían necesitar el producto o servicio en la zona (dato externo, con su fuente).', 'glosario' => true],
            'mercado_objetivo'    => ['label' => 'Mercado objetivo', 'description' => 'Parte del mercado potencial a la que el negocio se dirige, según su segmento y alcance.', 'glosario' => true],
            'demanda_estimada'    => ['label' => 'Demanda', 'description' => 'Cantidad de clientes o de ventas que se espera tener en un periodo, dentro del mercado objetivo.', 'glosario' => true],
            'meta_ventas'         => ['label' => 'Meta de ventas', 'description' => 'Ventas que el negocio se propone alcanzar en un periodo (unidades o soles); no puede superar la capacidad.', 'glosario' => true],
            'costos_principales'  => ['label' => 'Costos', 'description' => 'Costos fijos y variables que mencionó el usuario.'],
            'inversion_inicial'   => ['label' => 'Inversión', 'description' => 'Monto de la inversión inicial y sus componentes.'],
            'financiamiento'      => ['label' => 'Financiamiento', 'description' => 'Aporte propio, préstamo, tasa y plazo.'],
            'meta_financiera'     => ['label' => 'Meta financiera', 'description' => 'Meta de ingresos, utilidad o rentabilidad (por ejemplo, recuperar la inversión en dos años).', 'glosario' => true],
        ];
    }

    public function consolidationFocus(): string
    {
        return 'destacando el producto o servicio, el segmento de clientes, precios, demanda, capacidad, costos, inversión, resultados financieros y las decisiones tomadas';
    }

    // Capítulo II completo; Capítulo III: PESTEL (3.1) y análisis competitivo con las Cinco Fuerzas
    // de Porter y la MEFE (3.2); Capítulo VII completo (plan financiero)
    public function mandatoryCodes(): array
    {
        return ['2', '2.*', '3', '3.1', '3.1.*', '3.2', '3.2.*', '7', '7.*'];
    }
}
