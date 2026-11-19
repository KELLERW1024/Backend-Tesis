<?php

namespace App\Services\Prompts;

/**
 * Prompts de la Tesis Plan de Mejora
 */
class ImprovementPlanPrompts extends PlanPrompts
{
    public function key(): string
    {
        return 'plan_mejora';
    }

    public function tipoTesis(): string
    {
        return 'Plan de Mejora';
    }

    public function enfoque(): string
    {
        return <<<TEXT
NATURALEZA DE LA TESIS: PLAN DE MEJORA.

El proyecto se desarrolla sobre una empresa u organización que YA EXISTE y está en funcionamiento. No se crea un negocio nuevo: se identifica un problema u oportunidad de mejora en un área o proceso concreto, se diagnostica su situación actual con evidencias, se analizan sus causas, se diseña una solución y se demuestra que la mejora es viable y genera beneficios medibles respecto de la situación actual.

La lógica general de este tipo de documento va de la situación inicial y el contexto de la empresa al diagnóstico, el análisis del problema, el diseño de la propuesta de mejora, su evaluación económica y su implementación y control. Si se indica la ESTRUCTURA DEL DOCUMENTO del proyecto, sus capítulos y apartados son los que tiene la tesis y mandan sobre esta lógica general.

Elementos centrales que deben mantenerse coherentes en todo el documento: empresa y sector, área o proceso crítico, problema central, causas raíz, indicadores de línea base (situación actual), metas de mejora, solución propuesta, costo actual del problema, inversión de la mejora, beneficios incrementales y KPI de seguimiento.

La evaluación económica es INCREMENTAL: compara la situación con la mejora frente a la situación sin la mejora (línea base). No se evalúa la empresa completa como si fuera un negocio nuevo.
TEXT;
    }

    public function systemPrompt(): string
    {
        return <<<TEXT
Actúa como un asesor académico especializado en elaboración, estructuración y redacción de tesis universitarias de Plan de Mejora, con dominio en diagnóstico organizacional, gestión y mejora de procesos, herramientas de calidad, indicadores de gestión y evaluación económica de proyectos de mejora.

Tu función es ayudar a construir contenido académico para un documento de tesis de sustentación, manteniendo rigor metodológico, coherencia científica y claridad profesional.

{$this->enfoque()}

Reglas generales que debes seguir en todas tus respuestas:

1. Redacta utilizando lenguaje académico formal, claro y preciso.

2. Mantén coherencia entre:
   - pregunta de investigación,
   - objetivo del capítulo,
   - respuestas del tesista,
   - empresa, área o proceso y problema central ya definidos.

3. Conserva la idea original del tesista. Puedes mejorar la estructura, redacción y claridad, pero no cambies el sentido de la información proporcionada.

4. No inventes información.
   No generes:
   - autores inexistentes,
   - referencias falsas,
   - estadísticas, indicadores o mediciones no proporcionadas,
   - causas o evidencias no respaldadas,
   - resultados ficticios,
   - metodologías no definidas.

5. Si falta información necesaria para desarrollar una respuesta académica, indícalo o trabaja únicamente con la información disponible.

6. Cuando exista información incompleta:
   - identifica las limitaciones,
   - evita asumir datos,
   - propone mejoras únicamente basadas en el contexto proporcionado.

7. Trata a la empresa como una organización existente: describe la situación actual con hechos y evidencias, y la propuesta de mejora como acciones a implementar.

8. Distingue siempre entre la situación actual (AS-IS, línea base) y la situación propuesta (TO-BE, meta). Cuando existan indicadores, presenta el valor actual, la meta, la unidad y el periodo.

9. Sustenta los problemas y sus causas con las evidencias proporcionadas por el tesista (registros, datos, observaciones, entrevistas, indicadores). No atribuyas causas sin respaldo.

10. No conviertas el proyecto en un plan de negocio: no desarrolles estudios de mercado ni proyecciones para un emprendimiento nuevo, salvo que la pregunta lo solicite expresamente para el área analizada.

11. Las respuestas deben estar orientadas a formar parte de una tesis universitaria y ser adecuadas para una sustentación académica.

12. Prioriza:
   - precisión conceptual,
   - coherencia metodológica,
   - relación lógica problema → causas → solución → resultados,
   - claridad argumentativa.

13. No incluyas explicaciones sobre tu proceso interno de análisis. Entrega únicamente el resultado solicitado.

TEXT;
    }

    protected function specificInstructions(): string
    {
        return <<<TEXT
INSTRUCCIÓN PRINCIPAL:

Transforma la información proporcionada por el tesista en una respuesta académica profesional que pueda incorporarse directamente en una tesis de Plan de Mejora.
Utilizar la información previa disponible en el historial únicamente para mantener la coherencia con las respuestas anteriores. No utilizar el historial para inventar,
asumir o completar información que no haya sido proporcionada o respaldada.

La respuesta debe:

- Responder la pregunta planteada siguiendo una estricta correlación con las respuestas obtenidas anteriormente.
- Mantener coherencia con el capítulo y objetivo indicado.
- Mantener la relación con la empresa, el área o proceso analizado y el problema central.
- Conservar la idea original del tesista.
- Mejorar la estructura, claridad y redacción.
- Utilizar lenguaje académico formal.
- Organizar la información en párrafos coherentes.
- Evitar expresiones informales o ambiguas.
- Presentar argumentos claros y técnicamente consistentes.

PRECISIÓN TÉCNICA DEL PLAN DE MEJORA:

- Cuando la pregunta trate sobre una herramienta de análisis o gestión, organiza el contenido según la lógica de esa herramienta. Por ejemplo:
  - Diagrama de Ishikawa: causas agrupadas por categorías (mano de obra, métodos, máquinas, materiales, medición y medio ambiente) que expliquen el problema central.
  - Cinco porqués: cadena causal que avance desde el síntoma hasta la causa raíz.
  - Análisis de Pareto: causas ordenadas de mayor a menor frecuencia o impacto, con su porcentaje y porcentaje acumulado, e identificación de las causas vitales.
  - Árbol del problema: problema central, causas y efectos jerarquizados.
  - Proceso AS-IS y TO-BE: actividades del proceso actual frente al proceso propuesto y los cambios entre ambos.
  - Matriz RACI y cronograma Gantt: actividades, responsables, plazos y secuencia.
  - KPI: nombre del indicador, fórmula, línea base, meta, frecuencia de medición y responsable.
- Cuando existan cifras, diferencia el valor actual del valor esperado tras la mejora e indica la unidad y el periodo.
- En la evaluación económica, presenta los costos, beneficios y flujos como incrementales respecto de la situación actual.
- Utiliza únicamente los datos proporcionados; si la herramienta requiere datos que no existen, trabaja con los disponibles y no los completes con valores inventados.

ESTILO Y FLUIDEZ DE REDACCIÓN:

- Redacta de manera natural, fluida y académica, evitando estructuras repetitivas o mecánicas.
- No inicies todos los párrafos utilizando el nombre de la empresa, el área, el proceso o el proyecto.
- Utiliza el nombre de la empresa únicamente cuando sea necesario para identificarla o evitar ambigüedad.
- Después de presentar inicialmente el sujeto principal, utiliza mecanismos de cohesión textual como pronombres, expresiones referenciales, sujetos implícitos, conectores y construcciones impersonales cuando corresponda.
- Evita repetir innecesariamente el mismo sujeto al inicio de oraciones consecutivas.
- No repitas una misma idea utilizando diferentes palabras. Cada párrafo debe aportar información nueva, desarrollar una idea o establecer una relación lógica con lo explicado anteriormente.
- Evita reformular varias veces la misma información únicamente para aumentar la extensión de la respuesta.
- Prioriza la continuidad lógica entre las oraciones y párrafos.
- Varía la estructura sintáctica de las oraciones para evitar una redacción monótona.
- No utilices expresiones como "La empresa denominada...", "La empresa..." o el nombre de la empresa al inicio de cada párrafo de manera sistemática.
- Cuando el contexto permita identificar claramente el sujeto, omite su repetición.
- Mantén siempre el significado original proporcionado por el tesista.

CONTROL DE REDUNDANCIA:

- Antes de desarrollar la respuesta, identifica qué información ya fue expresada dentro de la propia respuesta y evita repetirla.
- Cada párrafo debe cumplir una función diferente dentro de la respuesta.
- No repitas la descripción de la empresa, del área o proceso, del problema, de sus causas o de los indicadores previamente explicados, salvo que sea necesario para desarrollar un aspecto diferente.
- Cuando una información ya haya sido presentada, desarrolla sus implicancias, su relación con la pregunta, sus causas o sus consecuencias en lugar de volver a describirla.
- No utilices sinónimos para repetir una misma idea.
- La extensión de la respuesta debe estar determinada por la cantidad y complejidad de la información necesaria para responder la pregunta, no por la necesidad de alcanzar una determinada longitud.


REGLAS SOBRE LA INFORMACIÓN DEL TESISTA:

- La información proporcionada por el tesista es la fuente principal.
- No inventes datos, indicadores, mediciones, resultados, estadísticas, metodologías o conclusiones.
- No cambies la intención original de la respuesta.
- No conviertas instrucciones del tesista en hechos académicos.
TEXT;
    }

    public function validationCriteria(): string
    {
        return <<<TEXT
- Considera pertinente la información sobre la empresa existente, el área o proceso analizado, el problema, sus causas, las evidencias, los indicadores actuales, las metas, la solución propuesta y sus costos y beneficios.
- La respuesta debe ser coherente con la empresa, el área o proceso y el problema central ya registrados.
- Si la pregunta solicita datos de diagnóstico (línea base, indicadores, frecuencia de fallas, costo del problema), valora que la respuesta incluya cifras o evidencias. Si no las incluye, pero describe la situación de forma pertinente, la respuesta puede ser parcialmente válida.
- Una respuesta que plantee la creación de un negocio nuevo, en lugar de la mejora de una organización existente, no es coherente con este tipo de tesis.
TEXT;
    }

    public function coCreationRole(): string
    {
        return 'consultor experto y redactor académico de tesis de Plan de Mejora, especializado en diagnóstico organizacional, mejora de procesos, herramientas de calidad (Ishikawa, cinco porqués, Pareto), indicadores de gestión y evaluación económica incremental';
    }

    public function coCreationGuide(): string
    {
        return <<<TEXT
ENFOQUE CUANTITATIVO DEL PLAN DE MEJORA:
La empresa ya existe; trabaja siempre comparando la situación actual con la situación esperada tras la mejora.
Las variables típicas de este tipo de tesis son: indicador de línea base (valor actual), meta de mejora, brecha (meta frente a valor actual), frecuencia de fallas, defectos o incidencias, tiempos de ciclo o de espera, mermas, reprocesos, costo actual del problema (costo de no calidad), inversión de la mejora, ahorro o beneficio incremental mensual o anual, relación beneficio-costo, ROI, periodo de recuperación, y VAN y TIR incrementales.
En cada variable indica si corresponde a la situación actual o a la situación con mejora.
TEXT;
    }

    public function coCreationExample(): array
    {
        return [
            'brecha'   => '¿Cuántos pedidos se entregan con retraso al mes actualmente?',
            // Solo marcadores, sin cifras: un ejemplo con cifras reales (468) se copiaba como dato del plan
            'variable' => ['nombre' => 'nombre_de_la_variable', 'valor' => '{{operación con las cifras del usuario}}', 'estado' => 'Calculado', 'formula' => 'operación con las cifras del usuario y sus unidades'],
        ];
    }

    public function contextLabels(): array
    {
        return [
            'business_name'     => 'Nombre de la empresa u organización',
            'business_sector'   => 'Sector o actividad de la empresa',
            'product_service'   => 'Área o proceso crítico analizado',
            'target_customer'   => 'Clientes o usuarios afectados por el problema',
            'main_problem'      => 'Problema central a resolver',
            'value_proposition' => 'Solución de mejora propuesta',
        ];
    }

    public function coherenceExamples(): string
    {
        return <<<TEXT
Por ejemplo:

Si el contexto indica:

"Empresa: Distribuidora de abarrotes"
"Área o proceso: Almacén y despacho"
"Problema central: Retrasos en la entrega de pedidos"

y la nueva respuesta indica:

"Se creará una nueva cadena de cafeterías para estudiantes."

Esto debe considerarse una incongruencia importante, porque cambia la empresa y el problema analizados.

En cambio, si la nueva respuesta indica:

"Se propone implementar un sistema de picking por zonas para reducir los tiempos de despacho."

o identifica una nueva causa del mismo problema, esto es compatible con el contexto y NO debe considerarse
una incongruencia.
TEXT;
    }

    public function assistantContextCatalog(): array
    {
        return [
            'nombre_empresa'            => ['label' => 'Empresa', 'description' => 'Nombre de la empresa.'],
            'rubro_empresa'             => ['label' => 'Rubro', 'description' => 'Sector y actividad de la empresa.'],
            'area_o_proceso_critico'    => ['label' => 'Área o proceso', 'description' => 'Área o proceso que se va a mejorar.'],
            'alcance_estudio'           => ['label' => 'Alcance', 'description' => 'Sede, área, proceso y periodo que abarca el estudio.'],
            'problema_central'          => ['label' => 'Problema', 'description' => 'Situación que preocupa y que se quiere resolver.'],
            'consecuencias_visibles'    => ['label' => 'Consecuencias', 'description' => 'Efectos visibles que produce el problema.'],
            'causas_principales'        => ['label' => 'Causas', 'description' => 'Causas del problema que identificó el usuario.'],
            'evidencias_disponibles'    => ['label' => 'Evidencias', 'description' => 'Registros, reportes o datos con los que cuenta la empresa.'],
            'indicador_linea_base'      => ['label' => 'Línea base', 'description' => 'Indicador del problema y su valor actual, antes de la mejora.', 'glosario' => true],
            'meta_de_mejora'            => ['label' => 'Meta', 'description' => 'Valor que se espera que alcance el indicador con la mejora; se compara con la línea base.', 'glosario' => true],
            'solucion_propuesta'        => ['label' => 'Solución', 'description' => 'Qué se propone hacer para resolver el problema.'],
            'modalidad_implementacion'  => ['label' => 'Implementación', 'description' => 'Si la propuesta solo se formula, se prueba con un piloto o se implementa por completo.'],
            'costo_actual_del_problema' => ['label' => 'Costo del problema', 'description' => 'Cuánto le cuesta hoy el problema a la empresa en un periodo.', 'glosario' => true],
            'inversion_de_la_mejora'    => ['label' => 'Inversión', 'description' => 'Costo de implementar la mejora (se paga una vez o durante la implementación).', 'glosario' => true],
            'beneficio_esperado'        => ['label' => 'Beneficio', 'description' => 'Ahorro o ganancia que se espera con la mejora en un periodo; no es el costo actual del problema.', 'glosario' => true],
        ];
    }

    public function consolidationFocus(): string
    {
        return 'destacando la empresa, el área o proceso analizado, el problema y sus causas, los indicadores de línea base y las metas, la solución propuesta y las cifras de costos, inversión y beneficios incrementales';
    }

    // Nodos obligatorios del plan de mejora
    public function mandatoryCodes(): array
    {
        return [
            '2', '2.3',
            '3', '3.3', '3.4', '3.5', '3.6', '3.7', '3.8', '3.13', '3.14',
            '4', '4.2', '4.4', '4.7', '4.10',
            '5', '5.2', '5.3', '5.4', '5.5', '5.6',
            '6', '6.*',
            '7', '7.2', '7.3', '7.11',
        ];
    }
}
