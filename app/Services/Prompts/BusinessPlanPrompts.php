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

La lógica del documento es: idea y oportunidad de negocio → plan estratégico y organizacional (misión, visión, valores, objetivos, FODA, ventaja competitiva y estrategias) → análisis del entorno y del mercado (PESTEL, cinco fuerzas de Porter, demanda y oferta) → plan de marketing (producto, precio, plaza y promoción) → plan de operaciones → organización de la empresa → plan financiero (inversión, financiamiento, costos, presupuestos, estados financieros, flujo de caja, VAN, TIR, ratios, sensibilidad y escenarios).

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
            'variable' => ['nombre' => 'capacidad_maxima', 'valor' => '468', 'estado' => 'Calculado', 'formula' => '18 diarios * 26 días laborables'],
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

    public function assistantContextDefaults(): array
    {
        return [
            'nombre_proyecto_empresa'   => null,
            'alcance_geografico'        => null,
            'capacidad_operativa'       => null,
            'precio_o_tarifa'           => null,
            'costos_o_inversion'        => null,
            'demanda_o_volumen'         => null,
            'impacto_o_meta_financiera' => null,
        ];
    }

    public function consolidationFocus(): string
    {
        return 'destacando el producto o servicio, el segmento de clientes, precios, demanda, capacidad, costos, inversión, resultados financieros y las decisiones tomadas';
    }

    public function mandatoryNodes(): string
    {
        return <<<TXT
   b) Todos los nodos cuyo "codigo" sea "2" o empiece por "2." (Capítulo II completo: factores internos y MEFI, visión, misión, valores, objetivo general, objetivos específicos, FODA, FODA cruzada, ventaja competitiva y estrategias).
   c) Los nodos cuyo "codigo" sea "3" o empiece por "3.1" o "3.2" (Análisis PESTEL con todos sus factores, Cinco Fuerzas de Porter con cada una de sus fuerzas y Matriz MEFE).
   d) Todos los nodos cuyo "codigo" sea "7" o empiece por "7." (Capítulo VII completo: supuestos, inversiones, capital de trabajo, financiamiento, costos, presupuestos, estados financieros, flujo de caja, VAN, TIR, ratios, sensibilidad, escenarios y viabilidad).
TXT;
    }
}
