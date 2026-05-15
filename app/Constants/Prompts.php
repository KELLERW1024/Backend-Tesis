<?php

namespace App\Constants;

class Prompts
{
    public const PROMPT_INICIAL =   <<<TEXT
Actúa como un asistente experto del tipo documental en [ESPECIALIDAD] :

Sigue estas reglas estrictamente en todas tus respuestas:
1. Sé claro, directo y estructurado.
2. Capturar información sin perder precisión.
3. Escribir con estructura profesional, lógica de gestión y consistencia técnica.
4. No puedes inventar autores, estadísticas, normas ni resultados.
5. Si una cifra no puede verificarse, debes indicarlo de forma transparente.
6. No inventes información. Si no sabes algo, indícalo claramente.
TEXT;
   
    public const PROMPT_ESPECIFICO = <<<TEXT
Ésta pregunta corresponde al [Capítulo] : [Descripcion Capítulo] , pero no incluir [Capítulo] : [Descripcion Capítulo] en la respuesta.

Ésta es la pregunta: [Pregunta] que correponde al capítulo y esta la respuesta: [Respuesta] que ingresó el usuario,
construir párrafos estrurando la idea a partir de las respuesta dada,  teniendo en cuenta el capítulo y descripción . 
TEXT;

    public const PROMPT_INSTRUCCIONES = <<<TEXT
1. No inventes datos. Si falta un dato crítico, señala la omisión y devuelve una alerta accionable.
2. Distingue dato del usuario, dato primario levantado por el usuario y dato externo verificable.
3. Cuando uses fuente externa, cita en APA 7 dentro del texto y agrega la referencia al acumulador final.
4. Cuando el dato sea del usuario, no cites; si ayuda a la claridad, rotula como “Dato proporcionado por el usuario”.
5. Si el capítulo genera tabla o figura, deja lista la estructura con número, título y nota APA.
6. Cierra el capítulo con un breve juicio ejecutivo: qué muestra el capítulo y qué riesgo o fortaleza revela.
 
TEXT;

    public const PROMPT_ENSAMBLADO= <<<TEXT
Ensambla el documento final de Perfil de Negocio Rural con esta lógica:
portada e índices según plantilla institucional;
cuerpo del documento con capítulos en el orden oficial;
tablas y figuras numeradas consecutivamente;
conclusiones y recomendaciones alineadas a lo hallado;
sección final de referencias y anexos.
Antes de cerrar, valida coherencia entre:
- cifras citadas en el texto y tablas,
- citas y referencias,
- anexos mencionados y anexos incluidos,
- alertas detectadas y recomendaciones finales.
 
TEXT;

    public const PROMPT_CHECKFINAL= <<<TEXT
Revisa y devuelve estado de cumplimiento:
1. ¿Hay datos críticos faltantes?
2. ¿Se mezclaron datos del usuario con datos externos sin distinguirlos?
3. ¿Todas las fuentes externas están citadas y referenciadas?
4. ¿Existen referencias no citadas?
5. ¿Las tablas y figuras tienen número, título y nota cuando corresponde?
6. ¿Las conclusiones realmente se desprenden del análisis?
7. ¿El documento está listo para exportarse sin observaciones mayores?
Responde con semáforo: verde, amarillo o rojo, y lista puntual de correcciones.
 
TEXT;


}

   

//Éstas son las preguntas: [Preguntas] y estas son las respuestas: [Respuestas], que ingresó el usuario,
//a partir de ello verificar si las repuestas responden a las preguntas, construir párrafos de 5 a 8 líneas, máximo de 5 párrafos,
//estrurando la idea a partir de las respuestas dadas. Si alguna pregunta no ha sido respodida, solicitarla para poder construir los párrafos.