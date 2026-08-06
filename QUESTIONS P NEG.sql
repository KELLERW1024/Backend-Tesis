-- PREGUNTAS DIAGNOSTICO PLAN NEGOCIO
INSERT INTO 
questions (
    plan_node_id , 
    question_text, 
    question_detail, 
    question_example,  
    validation_detail 
) VALUES (
    128,
    'Cuente brevemente su idea: qué ofrecerá, a quién, cómo funcionará y dónde.',
    'Explique la idea en sus propias palabras. Incluya oferta, cliente, forma de venta o entrega y etapa actual.',
    'Deseo ofrecer productos personalizados en mi ciudad, recibir pedidos por medios digitales y entregar mediante recojo o reparto.',
    'No necesita usar términos técnicos. No invente datos de mercado; basta con describir la idea.'
),
(
    128,
    '¿Su negocio es una idea, está en preparación, realiza pruebas o ya funciona?',
    'Elija la etapa que mejor representa la situación y mencione ventas, clientes o pruebas reales si existen.',
    'El negocio ya realiza ventas ocasionales, pero aún no opera de manera formal ni cuenta con procesos estandarizados.',
    'Distinga hechos de planes futuros. No presente una prueba limitada como operación consolidada.'
),
(
    128,
    '¿Qué hará principalmente: producir, revender, distribuir, prestar servicios o conectar partes?',
    'Seleccione una o varias actividades y explique cuál genera el valor principal.',
    'Producirá el bien principal y lo venderá directamente al consumidor final.',
    'ValiPlan propondrá una clasificación, pero el usuario debe confirmarla.'
),
(
    128,
    '¿Quién compra, quién paga y quién utiliza o consume finalmente?',
    'Identifique por separado estos roles si no corresponden a la misma persona.',
    'Una empresa realiza el pedido y paga; sus colaboradores reciben y utilizan el servicio.',
    'No confunda comprador con usuario final. Esta diferencia afecta marketing, precio y atención.'
),
(
    128,
    '¿Cómo venderá, cobrará y entregará?',
    'Mencione canales de contacto, confirmación, pago y entrega.',
    'El cliente consulta por medios digitales, confirma mediante un adelanto y recibe por recojo o entrega programada.',
    'Seleccione solo canales que puedan operarse con los recursos iniciales.'
),
(
    128,
    '¿Dónde funcionará y qué alcance tendrá al inicio?',
    'Use una descripción genérica o la ubicación real confirmada por el usuario. Delimite cobertura.',
    'Funcionará en mi ciudad y atenderá inicialmente las zonas cercanas al punto de operación.',
    'No suponga una ciudad. La cobertura debe coincidir con costos y capacidad.'
),
(
    128,
    '¿El negocio atenderá personas, empresas, instituciones públicas o una combinación?',
    'Seleccione B2C, B2B, B2G o combinación y explique el grupo prioritario.',
    'Atenderá principalmente personas y, de manera secundaria, pequeños negocios.',
    'No mezcle segmentos con necesidades muy diferentes sin definir propuestas específicas.'
),
(
    128,
    '¿Qué requisitos institucionales o cambios de estructura necesita el documento?',
    'Indique plantilla, capítulos obligatorios y modificaciones solicitadas.',
    'Se usará la estructura base y se agregará un subtítulo de impacto social.',
    'Eliminar un apartado esencial debe generar advertencia y análisis de impacto.'
),

-- Formulacion ide de negocio = 2 
(
    2,
    '¿Qué nombre provisional tendrá el negocio y qué desea comunicar?',
    'Para responder qué nombre provisional tendrá el negocio y qué desea comunicar, mencione los elementos indispensables para formulación de la idea de negocio y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Origen Vivo” como nombre provisional, porque comunica cercanía, identidad y productos naturales. En esta respuesta, el foco es: qué nombre provisional tendrá el negocio y qué desea comunicar.',
    'ValiPlan puede proponer opciones, pero el usuario debe elegir y aprobar. Verifique disponibilidad legal cuando corresponda. Control específico: verifique que la respuesta trate qué nombre provisional tendrá el negocio y qué desea comunicar y no sustituya este dato con información de otro apartado.'
),
(
    2,
    '¿Qué producto o servicio ofrecerá, a quién y qué resultado entregará?',
    'Para precisar qué producto o servicio ofrecerá, a quién y qué resultado entregará, identifique al actor concreto y explique su función, necesidad o responsabilidad dentro de formulación de la idea de negocio. Evite respuestas amplias como “todos” o “el público en general”.',
    '“Ofreceré productos personalizados por pedido, con opciones estándar y premium, mediante atención presencial y digital.” En esta respuesta, el foco es: qué producto o servicio ofrecerá, a quién y qué resultado entregará.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué producto o servicio ofrecerá, a quién y qué resultado entregará y no sustituya este dato con información de otro apartado.'
),
(
    2,
    '¿Cómo funcionará el negocio y cómo generará ingresos?',
    'Para explicar cómo funcionará el negocio y cómo generará ingresos, describa la secuencia en orden: quién actúa, qué recibe, qué hace y qué resultado entrega. Incluya las excepciones relevantes para formulación de la idea de negocio.',
    '“Consulta, validación de necesidad, cotización, confirmación, preparación, control, entrega y seguimiento.” En esta respuesta, el foco es: cómo funcionará el negocio y cómo generará ingresos.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cómo funcionará el negocio y cómo generará ingresos y no sustituya este dato con información de otro apartado.'
),
-- Problema o necesidad = 3
(
    3,
    '¿Qué problema, necesidad o deseo concreto atenderá el negocio?',
    'Para explicar qué problema, necesidad o deseo concreto atenderá el negocio, describa la secuencia en orden: quién actúa, qué recibe, qué hace y qué resultado entrega. Incluya las excepciones relevantes para problema o necesidad identificada.',
    '“Los clientes pierden tiempo porque las alternativas actuales no ofrecen información clara ni entrega dentro del plazo acordado.” En esta respuesta, el foco es: qué problema, necesidad o deseo concreto atenderá el negocio.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué problema, necesidad o deseo concreto atenderá el negocio y no sustituya este dato con información de otro apartado.'
),
(
    3,
    '¿Quiénes lo experimentan, con qué frecuencia y qué consecuencias produce?',
    'Para responder sobre quiénes lo experimentan, con qué frecuencia y qué consecuencias produce, registre una cifra, unidad y período. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en problema o necesidad identificada.',
    '“El cliente principal será una persona adulta de mi ciudad que busca una solución confiable, compara opciones por Internet y valora la puntualidad.” En esta respuesta, el foco es: quiénes lo experimentan, con qué frecuencia y qué consecuencias produce.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate quiénes lo experimentan, con qué frecuencia y qué consecuencias produce y no sustituya este dato con información de otro apartado.'
),
(
    3,
    '¿Qué evidencias demuestran que la necesidad existe?',
    'Para responder qué evidencias demuestran que la necesidad existe, mencione los elementos indispensables para problema o necesidad identificada y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Los clientes pierden tiempo porque las alternativas actuales no ofrecen información clara ni entrega dentro del plazo acordado.” En esta respuesta, el foco es: qué evidencias demuestran que la necesidad existe.',
    'No generalice resultados si la muestra o el método no lo permiten. Registre sesgos, alcance y limitaciones. Control específico: verifique que la respuesta trate qué evidencias demuestran que la necesidad existe y no sustituya este dato con información de otro apartado.'
),
-- analisi de oprpotunidad = 4
(
    4,
    '¿Qué condiciones actuales hacen oportuno desarrollar este negocio?',
    'Para fundamentar qué condiciones actuales hacen oportuno desarrollar este negocio, explique la causa y vincúlela con un beneficio, problema o evidencia del proyecto. Si es una opinión, márquela como hipótesis pendiente de validación.',
    '“Para análisis de la oportunidad, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué condiciones actuales hacen oportuno desarrollar este negocio.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué condiciones actuales hacen oportuno desarrollar este negocio y no sustituya este dato con información de otro apartado.'
),
(
    4,
    '¿Qué señales muestran interés y capacidad de pago?',
    'Para responder qué señales muestran interés y capacidad de pago, mencione los elementos indispensables para análisis de la oportunidad y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“En una prueba de dos semanas, 20 personas solicitaron información, 8 pidieron cotización y 4 realizaron una compra.” En esta respuesta, el foco es: qué señales muestran interés y capacidad de pago.',
    'No generalice resultados si la muestra o el método no lo permiten. Registre sesgos, alcance y limitaciones. Control específico: verifique que la respuesta trate qué señales muestran interés y capacidad de pago y no sustituya este dato con información de otro apartado.'
),
(
    4,
    '¿Qué espacio no cubren las alternativas y qué barreras debe superar?',
    'Para responder qué espacio no cubren las alternativas y qué barreras debe superar, mencione los elementos indispensables para análisis de la oportunidad y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Las alternativas ofrecen un servicio básico; queda espacio para una opción con seguimiento, personalización y garantía clara.” En esta respuesta, el foco es: qué espacio no cubren las alternativas y qué barreras debe superar.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué espacio no cubren las alternativas y qué barreras debe superar y no sustituya este dato con información de otro apartado.'
),
-- descripcion del producto os ervicio = 5
(
    5,
    '¿Qué recibirá exactamente el cliente y en qué presentaciones o modalidades?',
    'Para precisar qué recibirá exactamente el cliente y en qué presentaciones o modalidades, identifique al actor concreto y explique su función, necesidad o responsabilidad dentro de descripción del producto o servicio. Evite respuestas amplias como “todos” o “el público en general”.',
    '“El cliente principal será una persona adulta de mi ciudad que busca una solución confiable, compara opciones por Internet y valora la puntualidad.” En esta respuesta, el foco es: qué recibirá exactamente el cliente y en qué presentaciones o modalidades.',
    'Solicite solo datos necesarios y evite estereotipos o características que no influyan en la compra. Proteja información personal. Control específico: verifique que la respuesta trate qué recibirá exactamente el cliente y en qué presentaciones o modalidades y no sustituya este dato con información de otro apartado.'
),
(
    5,
    '¿Qué características y beneficios tendrá la oferta?',
    'Para responder qué características y beneficios tendrá la oferta, mencione los elementos indispensables para descripción del producto o servicio y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para descripción del producto o servicio, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué características y beneficios tendrá la oferta.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué características y beneficios tendrá la oferta y no sustituya este dato con información de otro apartado.'
),
(
    5,
    '¿Qué requisitos de calidad, garantía, seguridad o legalidad cumplirá?',
    'Para responder qué requisitos de calidad, garantía, seguridad o legalidad cumplirá, mencione los elementos indispensables para descripción del producto o servicio y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“La calidad se comprobará mediante especificaciones, entrega puntual, tasa de reclamos y satisfacción del cliente.” En esta respuesta, el foco es: qué requisitos de calidad, garantía, seguridad o legalidad cumplirá.',
    'Toda afirmación externa debe incluir fuente verificable y fecha. ValiPlan no debe inventar estadísticas, normas ni referencias. Control específico: verifique que la respuesta trate qué requisitos de calidad, garantía, seguridad o legalidad cumplirá y no sustituya este dato con información de otro apartado.'
),
-- Propuesta de valor 
(
    6,
    '¿Qué resultado valioso obtendrá el cliente?',
    'Para precisar qué resultado valioso obtendrá el cliente, identifique al actor concreto y explique su función, necesidad o responsabilidad dentro de propuesta de valor. Evite respuestas amplias como “todos” o “el público en general”.',
    '“El cliente principal será una persona adulta de mi ciudad que busca una solución confiable, compara opciones por Internet y valora la puntualidad.” En esta respuesta, el foco es: qué resultado valioso obtendrá el cliente.',
    'Solicite solo datos necesarios y evite estereotipos o características que no influyan en la compra. Proteja información personal. Control específico: verifique que la respuesta trate qué resultado valioso obtendrá el cliente y no sustituya este dato con información de otro apartado.'
),
(
    6,
    '¿Qué diferencia relevante ofrecerá frente a las alternativas?',
    'Para responder qué diferencia relevante ofrecerá frente a las alternativas, mencione los elementos indispensables para propuesta de valor y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Ofreceré productos personalizados por pedido, con opciones estándar y premium, mediante atención presencial y digital.” En esta respuesta, el foco es: qué diferencia relevante ofrecerá frente a las alternativas.',
    'Evite afirmaciones absolutas o promocionales sin comparación y evidencia. Explique cómo se demostrará la diferencia. Control específico: verifique que la respuesta trate qué diferencia relevante ofrecerá frente a las alternativas y no sustituya este dato con información de otro apartado.'
),
(
    6,
    '¿Qué capacidades respaldan la promesa y cómo se medirá?',
    'Para explicar qué capacidades respaldan la promesa y cómo se medirá, describa la secuencia en orden: quién actúa, qué recibe, qué hace y qué resultado entrega. Incluya las excepciones relevantes para propuesta de valor.',
    '“Puntualidad: confirmar plazos realistas, comunicar cambios y cumplir la fecha acordada.” En esta respuesta, el foco es: qué capacidades respaldan la promesa y cómo se medirá.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué capacidades respaldan la promesa y cómo se medirá y no sustituya este dato con información de otro apartado.'
),
-- Segmentos de clientes 
(
    8,
    '¿Qué grupos podrían comprar y cuál será prioritario al inicio?',
    'Para responder qué grupos podrían comprar y cuál será prioritario al inicio, mencione los elementos indispensables para segmentos de clientes y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para segmentos de clientes, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué grupos podrían comprar y cuál será prioritario al inicio.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué grupos podrían comprar y cuál será prioritario al inicio y no sustituya este dato con información de otro apartado.'
),
(
    8,
    '¿Cómo son, dónde están y cómo deciden una compra?',
    'Para explicar cómo son, dónde están y cómo deciden una compra, describa la secuencia en orden: quién actúa, qué recibe, qué hace y qué resultado entrega. Incluya las excepciones relevantes para segmentos de clientes.',
    '“El negocio funcionará en mi ciudad y atenderá inicialmente las zonas cercanas mediante recojo y entrega programada.” En esta respuesta, el foco es: cómo son, dónde están y cómo deciden una compra.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cómo son, dónde están y cómo deciden una compra y no sustituya este dato con información de otro apartado.'
),
(
    8,
    '¿Qué valoran, con qué frecuencia compran y qué puede impedir la compra?',
    'Para responder sobre qué valoran, con qué frecuencia compran y qué puede impedir la compra, registre una cifra, unidad y período. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en segmentos de clientes.',
    '“Para segmentos de clientes, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué valoran, con qué frecuencia compran y qué puede impedir la compra.',
    'ValiPlan puede proponer opciones, pero el usuario debe elegir y aprobar. Verifique disponibilidad legal cuando corresponda. Control específico: verifique que la respuesta trate qué valoran, con qué frecuencia compran y qué puede impedir la compra y no sustituya este dato con información de otro apartado.'
),

-- Canales  
(
    10,
    '¿Por qué medios conocerá y contactará el negocio el cliente?',
    'Para precisar por qué medios conocerá y contactará el negocio el cliente, identifique al actor concreto y explique su función, necesidad o responsabilidad dentro de canales. Evite respuestas amplias como “todos” o “el público en general”.',
    '“El cliente principal será una persona adulta de mi ciudad que busca una solución confiable, compara opciones por Internet y valora la puntualidad.” En esta respuesta, el foco es: por qué medios conocerá y contactará el negocio el cliente.',
    'Solicite solo datos necesarios y evite estereotipos o características que no influyan en la compra. Proteja información personal. Control específico: verifique que la respuesta trate por qué medios conocerá y contactará el negocio el cliente y no sustituya este dato con información de otro apartado.'
),
(
    10,
    '¿Cómo realizará, confirmará y pagará la compra?',
    'Para explicar cómo realizará, confirmará y pagará la compra, describa la secuencia en orden: quién actúa, qué recibe, qué hace y qué resultado entrega. Incluya las excepciones relevantes para canales.',
    '“El precio preliminar será de S/ 80 por unidad, sujeto a costo, personalización y validación con clientes.” En esta respuesta, el foco es: cómo realizará, confirmará y pagará la compra.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cómo realizará, confirmará y pagará la compra y no sustituya este dato con información de otro apartado.'
),
(
    10,
    '¿Cómo se entregará, se dará seguimiento y se atenderá después?',
    'Para explicar cómo se entregará, se dará seguimiento y se atenderá después, describa la secuencia en orden: quién actúa, qué recibe, qué hace y qué resultado entrega. Incluya las excepciones relevantes para canales.',
    '“Para canales, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: cómo se entregará, se dará seguimiento y se atenderá después.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cómo se entregará, se dará seguimiento y se atenderá después y no sustituya este dato con información de otro apartado.'
),
-- Relacion con clientes 
(
    11,
    '¿Cómo será la atención antes, durante y después de la compra?',
    'Para explicar cómo será la atención antes, durante y después de la compra, describa la secuencia en orden: quién actúa, qué recibe, qué hace y qué resultado entrega. Incluya las excepciones relevantes para relaciones con clientes.',
    '“Para relaciones con clientes, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: cómo será la atención antes, durante y después de la compra.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cómo será la atención antes, durante y después de la compra y no sustituya este dato con información de otro apartado.'
),
(
    11,
    '¿Qué generará confianza y cómo se manejarán cambios o reclamos?',
    'Para explicar qué generará confianza y cómo se manejarán cambios o reclamos, describa la secuencia en orden: quién actúa, qué recibe, qué hace y qué resultado entrega. Incluya las excepciones relevantes para relaciones con clientes.',
    '“Para relaciones con clientes, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué generará confianza y cómo se manejarán cambios o reclamos.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué generará confianza y cómo se manejarán cambios o reclamos y no sustituya este dato con información de otro apartado.'
),
(
    11,
    '¿Cómo se medirá satisfacción, recompra y recomendación?',
    'Para explicar cómo se medirá satisfacción, recompra y recomendación, describa la secuencia en orden: quién actúa, qué recibe, qué hace y qué resultado entrega. Incluya las excepciones relevantes para relaciones con clientes.',
    '“Para relaciones con clientes, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: cómo se medirá satisfacción, recompra y recomendación.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cómo se medirá satisfacción, recompra y recomendación y no sustituya este dato con información de otro apartado.'
),

-- Fuentes de ingreso
(
    12,
    '¿Por qué pagará el cliente y cómo se calculará el cobro?',
    'Para precisar por qué pagará el cliente y cómo se calculará el cobro, identifique al actor concreto y explique su función, necesidad o responsabilidad dentro de fuentes de ingresos. Evite respuestas amplias como “todos” o “el público en general”.',
    '“El cliente principal será una persona adulta de mi ciudad que busca una solución confiable, compara opciones por Internet y valora la puntualidad.” En esta respuesta, el foco es: por qué pagará el cliente y cómo se calculará el cobro.',
    'Solicite solo datos necesarios y evite estereotipos o características que no influyan en la compra. Proteja información personal. Control específico: verifique que la respuesta trate por qué pagará el cliente y cómo se calculará el cobro y no sustituya este dato con información de otro apartado.'
),
(
    12,
    '¿Cuándo y por qué medios pagará, y habrá adelantos, crédito o descuentos?',
    'Para fundamentar cuándo y por qué medios pagará, y habrá adelantos, crédito o descuentos, explique la causa y vínculela con un beneficio, problema o evidencia del proyecto. Si es una opinión, márquela como hipótesis pendiente de validación.',
    '“El precio preliminar será de S/ 80 por unidad, sujeto a costo, personalización y validación con clientes.” En esta respuesta, el foco es: cuándo y por qué medios pagará, y habrá adelantos, crédito o descuentos.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cuándo y por qué medios pagará, y habrá adelantos, crédito o descuentos y no sustituya este dato con información de otro apartado.'
),
(
    12,
    '¿Qué ingresos complementarios, comisiones y temporadas deben considerarse?',
    'Para responder qué ingresos complementarios, comisiones y temporadas deben considerarse, mencione los elementos indispensables para fuentes de ingresos y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para fuentes de ingresos, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué ingresos complementarios, comisiones y temporadas deben considerarse.',
    'ValiPlan puede proponer opciones, pero el usuario debe elegir y aprobar. Verifique disponibilidad legal cuando corresponda. Control específico: verifique que la respuesta trate qué ingresos complementarios, comisiones y temporadas deben considerarse y no sustituya este dato con información de otro apartado.'
),
-- recursos clave 
(
    13,
    '¿Qué personas, conocimientos, espacios, equipos y dinero son indispensables?',
    'Para precisar qué personas, conocimientos, espacios, equipos y dinero son indispensables, identifique al actor concreto y explique su función, necesidad o responsabilidad dentro de recursos clave. Evite respuestas amplias como “todos” o “el público en general”.',
    '“Se cuenta con conocimientos técnicos y una computadora; faltan equipos operativos y capital de trabajo.” En esta respuesta, el foco es: qué personas, conocimientos, espacios, equipos y dinero son indispensables.',
    'Solicite solo datos necesarios y evite estereotipos o características que no influyan en la compra. Proteja información personal. Control específico: verifique que la respuesta trate qué personas, conocimientos, espacios, equipos y dinero son indispensables y no sustituya este dato con información de otro apartado.'
),
(
    13,
    '¿Qué recursos ya posee y cuáles debe conseguir?',
    'Para responder qué recursos ya posee y cuáles debe conseguir, mencione los elementos indispensables para recursos clave y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Se cuenta con conocimientos técnicos y una computadora; faltan equipos operativos y capital de trabajo.” En esta respuesta, el foco es: qué recursos ya posee y cuáles debe conseguir.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué recursos ya posee y cuáles debe conseguir y no sustituya este dato con información de otro apartado.'
),
(
    13,
    '¿Qué recursos son críticos, qué capacidad tienen y qué alternativa existe si fallan?',
    'Para responder qué recursos son críticos, qué capacidad tienen y qué alternativa existe si fallan, mencione los elementos indispensables para recursos clave y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Las alternativas ofrecen un servicio básico; queda espacio para una opción con seguimiento, personalización y garantía clara.” En esta respuesta, el foco es: qué recursos son críticos, qué capacidad tienen y qué alternativa existe si fallan.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué recursos son críticos, qué capacidad tienen y qué alternativa existe si fallan y no sustituya este dato con información de otro apartado.'
),
-- Actividades clave 
(
    14,
    '¿Qué actividades debe realizar desde captar hasta atender al cliente?',
    'Para precisar qué actividades debe realizar desde captar hasta atender al cliente, identifique al actor concreto y explique su función, necesidad o responsabilidad dentro de actividades clave. Evite respuestas amplias como “todos” o “el público en general”.',
    '“El cliente principal será una persona adulta de mi ciudad que busca una solución confiable, compara opciones por Internet y valora la puntualidad.” En esta respuesta, el foco es: qué actividades debe realizar desde captar hasta atender al cliente.',
    'Solicite solo datos necesarios y evite estereotipos o características que no influyan en la compra. Proteja información personal. Control específico: verifique que la respuesta trate qué actividades debe realizar desde captar hasta atender al cliente y no sustituya este dato con información de otro apartado.'
),
(
    14,
    '¿Cuáles son críticas, en qué orden se ejecutan y quién responde?',
    'Para precisar cuáles son críticas, en qué orden se ejecutan y quién responde, identifique al actor concreto y explique su función, necesidad o responsabilidad dentro de actividades clave. Evite respuestas amplias como “todos” o “el público en general”.',
    '“El cliente principal será una persona adulta de mi ciudad que busca una solución confiable, compara opciones por Internet y valora la puntualidad.” En esta respuesta, el foco es: cuáles son críticas, en qué orden se ejecutan y quién responde.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cuáles son críticas, en qué orden se ejecutan y quién responde y no sustituya este dato con información de otro apartado.'
),
(
    14,
    '¿Qué controles, tiempos, riesgos y actividades tercerizadas existen?',
    'Para responder sobre qué controles, tiempos, riesgos y actividades tercerizadas existen, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en actividades clave.',
    '“El principal riesgo es depender de un solo proveedor; se mitigará homologando una segunda alternativa.” En esta respuesta, el foco es: qué controles, tiempos, riesgos y actividades tercerizadas existen.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate qué controles, tiempos, riesgos y actividades tercerizadas existen y no sustituya este dato con información de otro apartado.'
),
-- Socios clave 
(
    15,
    '¿Qué proveedores, aliados o instituciones necesita y qué aportará cada uno?',
    'Para precisar qué proveedores, aliados o instituciones necesita y qué aportará cada uno, identifique al actor concreto y explique su función, necesidad o responsabilidad dentro de socios clave. Evite respuestas amplias como “todos” o “el público en general”.',
    '“Un proveedor local abastecerá el insumo principal y un servicio externo realizará entregas en horas de alta demanda.” En esta respuesta, el foco es: qué proveedores, aliados o instituciones necesita y qué aportará cada uno.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué proveedores, aliados o instituciones necesita y qué aportará cada uno y no sustituya este dato con información de otro apartado.'
),
(
    15,
    '¿Bajo qué condiciones trabajarán y qué obtendrán a cambio?',
    'Para responder bajo qué condiciones trabajarán y qué obtendrán a cambio, mencione los elementos indispensables para socios clave y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para socios clave, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: bajo qué condiciones trabajarán y qué obtendrán a cambio.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate bajo qué condiciones trabajarán y qué obtendrán a cambio y no sustituya este dato con información de otro apartado.'
),
(
    15,
    '¿Qué dependencia o riesgo existe y qué alternativa tendrá?',
    'Para responder qué dependencia o riesgo existe y qué alternativa tendrá, mencione los elementos indispensables para socios clave y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Las alternativas ofrecen un servicio básico; queda espacio para una opción con seguimiento, personalización y garantía clara.” En esta respuesta, el foco es: qué dependencia o riesgo existe y qué alternativa tendrá.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué dependencia o riesgo existe y qué alternativa tendrá y no sustituya este dato con información de otro apartado.'
),

-- Estructura de costos 
(
    16,
    '¿Qué costos fijos, variables, directos e indirectos tendrá el negocio?',
    'Para responder sobre qué costos fijos, variables, directos e indirectos tendrá el negocio, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en estructura de costos.',
    '“El alquiler y la remuneración son costos fijos; los materiales y la entrega varían con cada pedido.” En esta respuesta, el foco es: qué costos fijos, variables, directos e indirectos tendrá el negocio.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate qué costos fijos, variables, directos e indirectos tendrá el negocio y no sustituya este dato con información de otro apartado.'
),
(
    16,
    '¿Qué inversiones y gastos iniciales se requieren?',
    'Para responder qué inversiones y gastos iniciales se requieren, mencione los elementos indispensables para estructura de costos y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“El alquiler y la remuneración son costos fijos; los materiales y la entrega varían con cada pedido.” En esta respuesta, el foco es: qué inversiones y gastos iniciales se requieren.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué inversiones y gastos iniciales se requieren y no sustituya este dato con información de otro apartado.'
),
(
    16,
    '¿Qué costos suelen olvidarse y cómo cambiarán al crecer?',
    'Para responder sobre qué costos suelen olvidarse y cómo cambiarán al crecer, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en estructura de costos.',
    '“El alquiler y la remuneración son costos fijos; los materiales y la entrega varían con cada pedido.” En esta respuesta, el foco es: qué costos suelen olvidarse y cómo cambiarán al crecer.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate qué costos suelen olvidarse y cómo cambiarán al crecer y no sustituya este dato con información de otro apartado.'
),

-- capitulo II
-- Descripcion de la empresa 
(
    0,
    '¿Cómo describiría la empresa en un párrafo claro?',
    'Para explicar cómo describiría la empresa en un párrafo claro, describa la secuencia en orden: quién actúa, qué recibe, qué hace y qué resultado entrega. Incluya las excepciones relevantes para descripción de la empresa.',
    '“Para descripción de la empresa, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: cómo describiría la empresa en un párrafo claro.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cómo describiría la empresa en un párrafo claro y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Cuál será su actividad, ubicación y alcance inicial?',
    'Para delimitar cuál será su actividad, ubicación y alcance inicial, use una descripción genérica o la ubicación confirmada por el usuario: “mi ciudad”, “mi región” o “la zona atendida”. Indique cobertura y restricciones.',
    '“El negocio funcionará en mi ciudad y atenderá inicialmente las zonas cercanas mediante recojo y entrega programada.” En esta respuesta, el foco es: cuál será su actividad, ubicación y alcance inicial.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cuál será su actividad, ubicación y alcance inicial y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué líneas operarán desde el inicio y cuáles se incorporarán después?',
    'Para responder qué líneas operarán desde el inicio y cuáles se incorporarán después, mencione los elementos indispensables para descripción de la empresa y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para descripción de la empresa, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué líneas operarán desde el inicio y cuáles se incorporarán después.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué líneas operarán desde el inicio y cuáles se incorporarán después y no sustituya este dato con información de otro apartado.'
),
-- Datos generales del a empresa 
(
    0,
    '¿Qué nombre comercial, razón social, domicilio y actividad se consideran?',
    'Para fundamentar qué nombre comercial, razón social, domicilio y actividad se consideran, explique la causa y vínculela con un beneficio, problema o evidencia del proyecto. Si es una opinión, márquela como hipótesis pendiente de validación.',
    '“Origen Vivo” como nombre provisional, porque comunica cercanía, identidad y productos naturales. En esta respuesta, el foco es: qué nombre comercial, razón social, domicilio y actividad se consideran.',
    'ValiPlan puede proponer opciones, pero el usuario debe elegir y aprobar. Verifique disponibilidad legal cuando corresponda. Control específico: verifique que la respuesta trate qué nombre comercial, razón social, domicilio y actividad se consideran y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Quiénes participarán y qué aportará cada uno?',
    'Para precisar quiénes participarán y qué aportará cada uno, identifique al actor concreto y explique su función, necesidad o responsabilidad dentro de datos generales de la empresa. Evite respuestas amplias como “todos” o “el público en general”.',
    '“El cliente principal será una persona adulta de mi ciudad que busca una solución confiable, compara opciones por Internet y valora la puntualidad.” En esta respuesta, el foco es: quiénes participarán y qué aportará cada uno.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate quiénes participarán y qué aportará cada uno y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué forma jurídica y clasificación económica deben verificarse?',
    'Para responder qué forma jurídica y clasificación económica deben verificarse, mencione los elementos indispensables para datos generales de la empresa y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para datos generales de la empresa, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué forma jurídica y clasificación económica deben verificarse.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué forma jurídica y clasificación económica deben verificarse y no sustituya este dato con información de otro apartado.'
),
-- Fortalezas 
(
    0,
    '¿Qué capacidades internas ayudan realmente a competir?',
    'Para responder qué capacidades internas ayudan realmente a competir, mencione los elementos indispensables para fortalezas y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Las alternativas ofrecen un servicio básico; queda espacio para una opción con seguimiento, personalización y garantía clara.” En esta respuesta, el foco es: qué capacidades internas ayudan realmente a competir.',
    'El cálculo debe ejecutarse con fórmulas deterministas y entradas aprobadas. La IA solo explica resultados; no reemplaza el motor financiero. Control específico: verifique que la respuesta trate qué capacidades internas ayudan realmente a competir y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué evidencia demuestra cada fortaleza?',
    'Para responder qué evidencia demuestra cada fortaleza, mencione los elementos indispensables para fortalezas y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“En una prueba de dos semanas, 20 personas solicitaron información, 8 pidieron cotización y 4 realizaron una compra.” En esta respuesta, el foco es: qué evidencia demuestra cada fortaleza.',
    'No generalice resultados si la muestra o el método no lo permiten. Registre sesgos, alcance y limitaciones. Control específico: verifique que la respuesta trate qué evidencia demuestra cada fortaleza y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Cómo se aprovechará cada fortaleza?',
    'Para explicar cómo se aprovechará cada fortaleza, describa la secuencia en orden: quién actúa, qué recibe, qué hace y qué resultado entrega. Incluya las excepciones relevantes para fortalezas.',
    '“Para fortalezas, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: cómo se aprovechará cada fortaleza.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cómo se aprovechará cada fortaleza y no sustituya este dato con información de otro apartado.'
),
--  Debilidades 
(
    0,
    '¿Qué limitaciones internas dificultan cumplir la propuesta?',
    'Para responder qué limitaciones internas dificultan cumplir la propuesta, mencione los elementos indispensables para debilidades y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“El principal riesgo es depender de un solo proveedor; se mitigará homologando una segunda alternativa.” En esta respuesta, el foco es: qué limitaciones internas dificultan cumplir la propuesta.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué limitaciones internas dificultan cumplir la propuesta y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué consecuencia produce cada debilidad?',
    'Para responder qué consecuencia produce cada debilidad, mencione los elementos indispensables para debilidades y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para debilidades, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué consecuencia produce cada debilidad.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué consecuencia produce cada debilidad y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué puede hacer la empresa para reducirla?',
    'Para responder qué puede hacer la empresa para reducirla, mencione los elementos indispensables para debilidades y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para debilidades, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué puede hacer la empresa para reducirla.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué puede hacer la empresa para reducirla y no sustituya este dato con información de otro apartado.'
),
-- matriz MEFI
(
    0,
    '¿Qué fortalezas y debilidades son más determinantes?',
    'Para responder qué fortalezas y debilidades son más determinantes, mencione los elementos indispensables para matriz MEFI y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para matriz MEFI, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué fortalezas y debilidades son más determinantes.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué fortalezas y debilidades son más determinantes y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué peso y calificación justificable tiene cada factor?',
    'Para fundamentar qué peso y calificación justificable tiene cada factor, explique la causa y vincúlela con un beneficio, problema o evidencia del proyecto. Si es una opinión, márquela como hipótesis pendiente de validación.',
    '“Para matriz MEFI, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué peso y calificación justificable tiene cada factor.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué peso y calificación justificable tiene cada factor y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué significa el resultado y qué decisiones orienta?',
    'Para responder qué significa el resultado y qué decisiones orienta, mencione los elementos indispensables para matriz MEFI y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para matriz MEFI, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué significa el resultado y qué decisiones orienta.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué significa el resultado y qué decisiones orienta y no sustituya este dato con información de otro apartado.'
),
-- Vision
(
    0,
    '¿Cómo desea que sea reconocida la empresa en un horizonte definido?',
    'Para explicar cómo desea que sea reconocida la empresa en un horizonte definido, describa la secuencia en orden: quién actúa, qué recibe, qué hace y qué resultado entrega. Incluya las excepciones relevantes para visión.',
    '“Ser una empresa reconocida en mi región por su confiabilidad, innovación y aporte sostenible al 2030.” En esta respuesta, el foco es: cómo desea que sea reconocida la empresa en un horizonte definido.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cómo desea que sea reconocida la empresa en un horizonte definido y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿En qué mercado y por qué logro distintivo?',
    'Para fundamentar en qué mercado y por qué logro distintivo, explique la causa y vincúlela con un beneficio, problema o evidencia del proyecto. Si es una opinión, márquela como hipótesis pendiente de validación.',
    '“Ser una empresa reconocida en mi región por su confiabilidad, innovación y aporte sostenible al 2030.” En esta respuesta, el foco es: en qué mercado y por qué logro distintivo.',
    'Toda afirmación externa debe incluir fuente verificable y fecha. ValiPlan no debe inventar estadísticas, normas ni referencias. Control específico: verifique que la respuesta trate en qué mercado y por qué logro distintivo y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Cómo se comprobará que la visión se alcanzó?',
    'Para explicar cómo se comprobará que la visión se alcanzó, describa la secuencia en orden: quién actúa, qué recibe, qué hace y qué resultado entrega. Incluya las excepciones relevantes para visión.',
    '“Ser una empresa reconocida en mi región por su confiabilidad, innovación y aporte sostenible al 2030.” En esta respuesta, el foco es: cómo se comprobará que la visión se alcanzó.',
    'ValiPlan puede proponer opciones, pero el usuario debe elegir y aprobar. Verifique disponibilidad legal cuando corresponda. Control específico: verifique que la respuesta trate cómo se comprobará que la visión se alcanzó y no sustituya este dato con información de otro apartado.'
),

-- Mision
(
    0,
    '¿A quién sirve la empresa y qué necesidad atiende?',
    'Para precisar a quién sirve la empresa y qué necesidad atiende, identifique al actor concreto y explique su función, necesidad o responsabilidad dentro de misión. Evite respuestas amplias como “todos” o “el público en general”.',
    '“El cliente principal será una persona adulta de mi ciudad que busca una solución confiable, compara opciones por Internet y valora la puntualidad.” En esta respuesta, el foco es: a quién sirve la empresa y qué necesidad atiende.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate a quién sirve la empresa y qué necesidad atiende y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué entrega y cómo crea valor?',
    'Para explicar qué entrega y cómo crea valor, describa la secuencia en orden: quién actúa, qué recibe, qué hace y qué resultado entrega. Incluya las excepciones relevantes para misión.',
    '“Brindar soluciones útiles y confiables a nuestros clientes mediante atención cercana y procesos responsables.” En esta respuesta, el foco es: qué entrega y cómo crea valor.',
    'ValiPlan puede proponer opciones, pero el usuario debe elegir y aprobar. Verifique disponibilidad legal cuando corresponda. Control específico: verifique que la respuesta trate qué entrega y cómo crea valor y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué principios deben reflejarse en su actuación?',
    'Para responder qué principios deben reflejarse en su actuación, mencione los elementos indispensables para misión y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Brindar soluciones útiles y confiables a nuestros clientes mediante atención cercana y procesos responsables.” En esta respuesta, el foco es: qué principios deben reflejarse en su actuación.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué principios deben reflejarse en su actuación y no sustituya este dato con información de otro apartado.'
),
-- Valores 
(
    0,
    '¿Qué valores son indispensables para operar?',
    'Para responder qué valores son indispensables para operar, mencione los elementos indispensables para valores y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Puntualidad: confirmar plazos realistas, comunicar cambios y cumplir la fecha acordada.” En esta respuesta, el foco es: qué valores son indispensables para operar.',
    'ValiPlan puede proponer opciones, pero el usuario debe elegir y aprobar. Verifique disponibilidad legal cuando corresponda. Control específico: verifique que la respuesta trate qué valores son indispensables para operar y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué conducta observable representa cada valor?',
    'Para responder qué conducta observable representa cada valor, mencione los elementos indispensables para valores y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Puntualidad: confirmar plazos realistas, comunicar cambios y cumplir la fecha acordada.” En esta respuesta, el foco es: qué conducta observable representa cada valor.',
    'ValiPlan puede proponer opciones, pero el usuario debe elegir y aprobar. Verifique disponibilidad legal cuando corresponda. Control específico: verifique que la respuesta trate qué conducta observable representa cada valor y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Cómo se reforzará y evaluará cada conducta?',
    'Para explicar cómo se reforzará y evaluará cada conducta, describa la secuencia en orden: quién actúa, qué recibe, qué hace y qué resultado entrega. Incluya las excepciones relevantes para valores.',
    '“Puntualidad: confirmar plazos realistas, comunicar cambios y cumplir la fecha acordada.” En esta respuesta, el foco es: cómo se reforzará y evaluará cada conducta.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cómo se reforzará y evaluará cada conducta y no sustituya este dato con información de otro apartado.'
),
-- Objetivo general y objetivos especificos
(
    0,
    '¿Qué resultado general busca conseguir el plan?',
    'Para responder qué resultado general busca conseguir el plan, mencione los elementos indispensables para objetivo general y objetivos específicos y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Incrementar la recompra del 20 % al 30 % durante el primer año, medida mensualmente por el responsable comercial.” En esta respuesta, el foco es: qué resultado general busca conseguir el plan.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué resultado general busca conseguir el plan y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué resultados concretos deben lograrse en mercado, operaciones y finanzas?',
    'Para delimitar qué resultados concretos deben lograrse en mercado, operaciones y finanzas, use una descripción genérica o la ubicación confirmada por el usuario: “mi ciudad”, “mi región” o “la zona atendida”. Indique cobertura y restricciones.',
    '“Incrementar la recompra del 20 % al 30 % durante el primer año, medida mensualmente por el responsable comercial.” En esta respuesta, el foco es: qué resultados concretos deben lograrse en mercado, operaciones y finanzas.',
    'Toda afirmación externa debe incluir fuente verificable y fecha. ValiPlan no debe inventar estadísticas, normas ni referencias. Control específico: verifique que la respuesta trate qué resultados concretos deben lograrse en mercado, operaciones y finanzas y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Con qué indicador, meta, plazo y responsable se medirá cada resultado?',
    'Para responder sobre con qué indicador, meta, plazo y responsable se medirá cada resultado, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en objetivo general y objetivos específicos.',
    '“Incrementar la recompra del 20 % al 30 % durante el primer año, medida mensualmente por el responsable comercial.” En esta respuesta, el foco es: con qué indicador, meta, plazo y responsable se medirá cada resultado.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate con qué indicador, meta, plazo y responsable se medirá cada resultado y no sustituya este dato con información de otro apartado.'
),
-- Foda y foda cruzado 

(
    0,
    '¿Qué factores internos y externos son prioritarios?',
    'Para responder qué factores internos y externos son prioritarios, mencione los elementos indispensables para FODA y FODA cruzado y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para FODA y FODA cruzado, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué factores internos y externos son prioritarios.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué factores internos y externos son prioritarios y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué estrategias surgen de combinar F-O, F-A, D-O y D-A?',
    'Para responder qué estrategias surgen de combinar F-O, F-A, D-O y D-A, mencione los elementos indispensables para FODA y FODA cruzado y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para FODA y FODA cruzado, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué estrategias surgen de combinar F-O, F-A, D-O y D-A.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué estrategias surgen de combinar F-O, F-A, D-O y D-A y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Cuáles son viables y prioritarias?',
    'Para responder cuáles son viables y prioritarias, mencione los elementos indispensables para FODA y FODA cruzado y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para FODA y FODA cruzado, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: cuáles son viables y prioritarias.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cuáles son viables y prioritarias y no sustituya este dato con información de otro apartado.'
),

-- Ventaja competitiva, estrategias e indicadores
(
    0,
    '¿Qué hará mejor o distinto de forma relevante para el cliente?',
    'Para precisar qué hará mejor o distinto de forma relevante para el cliente, identifique al actor concreto y explique su función, necesidad o responsabilidad dentro de ventaja competitiva, estrategias e indicadores. Evite respuestas amplias como “todos” o “el público en general”.',
    '“El cliente principal será una persona adulta de mi ciudad que busca una solución confiable, compara opciones por Internet y valora la puntualidad.” En esta respuesta, el foco es: qué hará mejor o distinto de forma relevante para el cliente.',
    'Evite afirmaciones absolutas o promocionales sin comparación y evidencia. Explique cómo se demostrará la diferencia. Control específico: verifique que la respuesta trate qué hará mejor o distinto de forma relevante para el cliente y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué estrategias sostendrán esa ventaja?',
    'Para responder qué estrategias sostendrán esa ventaja, mencione los elementos indispensables para ventaja competitiva, estrategias e indicadores y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para ventaja competitiva, estrategias e indicadores, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué estrategias sostendrán esa ventaja.',
    'Evite afirmaciones absolutas o promocionales sin comparación y evidencia. Explique cómo se demostrará la diferencia. Control específico: verifique que la respuesta trate qué estrategias sostendrán esa ventaja y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué indicadores y metas permitirán controlar las estrategias?',
    'Para responder qué indicadores y metas permitirán controlar las estrategias, mencione los elementos indispensables para ventaja competitiva, estrategias e indicadores y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Incrementar la recompra del 20 % al 30 % durante el primer año, medida mensualmente por el responsable comercial.” En esta respuesta, el foco es: qué indicadores y metas permitirán controlar las estrategias.',
    'El cálculo debe ejecutarse con fórmulas deterministas y entradas aprobadas. La IA solo explica resultados; no reemplaza el motor financiero. Control específico: verifique que la respuesta trate qué indicadores y metas permitirán controlar las estrategias y no sustituya este dato con información de otro apartado.'
),

-- CAPITYULO III
-- factores politicos y legales 
(
    0,
    '¿Qué normas, permisos o decisiones públicas afectan el negocio?',
    'Para responder qué normas, permisos o decisiones públicas afectan el negocio, mencione los elementos indispensables para factores políticos y legales y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para factores políticos y legales, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué normas, permisos o decisiones públicas afectan el negocio.',
    'Toda afirmación externa debe incluir fuente verificable y fecha. ValiPlan no debe inventar estadísticas, normas ni referencias. Control específico: verifique que la respuesta trate qué normas, permisos o decisiones públicas afectan el negocio y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Cómo representan una oportunidad o amenaza?',
    'Para explicar cómo representan una oportunidad o amenaza, describa la secuencia en orden: quién actúa, qué recibe, qué hace y qué resultado entrega. Incluya las excepciones relevantes para factores políticos y legales.',
    '“Para factores políticos y legales, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: cómo representan una oportunidad o amenaza.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cómo representan una oportunidad o amenaza y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué acción debe adoptar la empresa?',
    'Para responder qué acción debe adoptar la empresa, mencione los elementos indispensables para factores políticos y legales y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para factores políticos y legales, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué acción debe adoptar la empresa.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué acción debe adoptar la empresa y no sustituya este dato con información de otro apartado.'
),

-- factores economicos 
(
    0,
    '¿Qué variables económicas afectan demanda, precios, costos o financiamiento?',
    'Para responder sobre qué variables económicas afectan demanda, precios, costos o financiamiento, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en factores económicos.',
    '“El precio preliminar será de S/ 80 por unidad, sujeto a costo, personalización y validación con clientes.” En esta respuesta, el foco es: qué variables económicas afectan demanda, precios, costos o financiamiento.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate qué variables económicas afectan demanda, precios, costos o financiamiento y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Cuál es su tendencia y fuente?',
    'Para responder cuál es su tendencia y fuente, mencione los elementos indispensables para factores económicos y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para factores económicos, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: cuál es su tendencia y fuente.',
    'Toda afirmación externa debe incluir fuente verificable y fecha. ValiPlan no debe inventar estadísticas, normas ni referencias. Control específico: verifique que la respuesta trate cuál es su tendencia y fuente y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué escenario económico usará el plan?',
    'Para responder qué escenario económico usará el plan, mencione los elementos indispensables para factores económicos y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para factores económicos, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué escenario económico usará el plan.',
    'Toda afirmación externa debe incluir fuente verificable y fecha. ValiPlan no debe inventar estadísticas, normas ni referencias. Control específico: verifique que la respuesta trate qué escenario económico usará el plan y no sustituya este dato con información de otro apartado.'
),
 -- fcatores sociales ,  culturales y demografocos 
  (
    0,
    '¿Qué cambios sociales o demográficos influyen en la demanda?',
    'Para responder qué cambios sociales o demográficos influyen en la demanda, mencione los elementos indispensables para factores sociales, culturales y demográficos y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“La proyección parte de 40 ventas mensuales, precio promedio de S/ 80 y crecimiento sujeto a capacidad y evidencia.” En esta respuesta, el foco es: qué cambios sociales o demográficos influyen en la demanda.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate qué cambios sociales o demográficos influyen en la demanda y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué hábitos, valores o preferencias son relevantes?',
    'Para responder qué hábitos, valores o preferencias son relevantes, mencione los elementos indispensables para factores sociales, culturales y demográficos y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“El motor calculará el resultado con entradas verificadas y mostrará fórmulas, supuestos y escenarios.” En esta respuesta, el foco es: qué hábitos, valores o preferencias son relevantes.',
    'El cálculo debe ejecutarse con fórmulas deterministas y entradas aprobadas. La IA solo explica resultados; no reemplaza el motor financiero. Control específico: verifique que la respuesta trate qué hábitos, valores o preferencias son relevantes y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Cómo se adaptará la oferta y comunicación?',
    'Para explicar cómo se adaptará la oferta y comunicación, describa la secuencia en orden: quién actúa, qué recibe, qué hace y qué resultado entrega. Incluya las excepciones relevantes para factores sociales, culturales y demográficos.',
    '“Para factores sociales, culturales y demográficos, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: cómo se adaptará la oferta y comunicación.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cómo se adaptará la oferta y comunicación y no sustituya este dato con información de otro apartado.'
),

-- factopres tecnologicos 
(
    0,
    '¿Qué tecnología necesita el negocio y qué tecnología cambia el sector?',
    'Para responder qué tecnología necesita el negocio y qué tecnología cambia el sector, mencione los elementos indispensables para factores tecnológicos y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para factores tecnológicos, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué tecnología necesita el negocio y qué tecnología cambia el sector.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué tecnología necesita el negocio y qué tecnología cambia el sector y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué costo, madurez y adopción tiene?',
    'Para responder sobre qué costo, madurez y adopción tiene, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en factores tecnológicos.',
    '“El alquiler y la remuneración son costos fijos; los materiales y la entrega varían con cada pedido.” En esta respuesta, el foco es: qué costo, madurez y adopción tiene.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate qué costo, madurez y adopción tiene y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué riesgo de falla, seguridad u obsolescencia existe?',
    'Para responder qué riesgo de falla, seguridad u obsolescencia existe, mencione los elementos indispensables para factores tecnológicos y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“El principal riesgo es depender de un solo proveedor; se mitigará homologando una segunda alternativa.” En esta respuesta, el foco es: qué riesgo de falla, seguridad u obsolescencia existe.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué riesgo de falla, seguridad u obsolescencia existe y no sustituya este dato con información de otro apartado.'
),

-- factores ambientales 
(
    0,
    '¿Qué factores ambientales afectan demanda u operación?',
    'Para responder qué factores ambientales afectan demanda u operación, mencione los elementos indispensables para factores ambientales y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“La proyección parte de 40 ventas mensuales, precio promedio de S/ 80 y crecimiento sujeto a capacidad y evidencia.” En esta respuesta, el foco es: qué factores ambientales afectan demanda u operación.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate qué factores ambientales afectan demanda u operación y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué recursos, residuos o impactos genera el negocio?',
    'Para responder qué recursos, residuos o impactos genera el negocio, mencione los elementos indispensables para factores ambientales y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Se cuenta con conocimientos técnicos y una computadora; faltan equipos operativos y capital de trabajo.” En esta respuesta, el foco es: qué recursos, residuos o impactos genera el negocio.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué recursos, residuos o impactos genera el negocio y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué medidas preventivas o sostenibles aplicará?',
    'Para responder qué medidas preventivas o sostenibles aplicará, mencione los elementos indispensables para factores ambientales y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para factores ambientales, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué medidas preventivas o sostenibles aplicará.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué medidas preventivas o sostenibles aplicará y no sustituya este dato con información de otro apartado.'
),

-- mercado meta 
(
    0,
    '¿Qué mercado geográfico y tipo de cliente analizará?',
    'Para precisar qué mercado geográfico y tipo de cliente analizará, identifique al actor concreto y explique su función, necesidad o responsabilidad dentro de mercado meta. Evite respuestas amplias como “todos” o “el público en general”.',
    '“El cliente principal será una persona adulta de mi ciudad que busca una solución confiable, compara opciones por Internet y valora la puntualidad.” En esta respuesta, el foco es: qué mercado geográfico y tipo de cliente analizará.',
    'Toda afirmación externa debe incluir fuente verificable y fecha. ValiPlan no debe inventar estadísticas, normas ni referencias. Control específico: verifique que la respuesta trate qué mercado geográfico y tipo de cliente analizará y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué filtros permiten delimitarlo y localizarlo?',
    'Para responder qué filtros permiten delimitarlo y localizarlo, mencione los elementos indispensables para mercado meta y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para mercado meta, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué filtros permiten delimitarlo y localizarlo.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué filtros permiten delimitarlo y localizarlo y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Por qué ese mercado es prioritario y alcanzable?',
    'Para fundamentar por qué ese mercado es prioritario y alcanzable, explique la causa y vincúlela con un beneficio, problema o evidencia del proyecto. Si es una opinión, márquela como hipótesis pendiente de validación.',
    '“Para mercado meta, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: por qué ese mercado es prioritario y alcanzable.',
    'Toda afirmación externa debe incluir fuente verificable y fecha. ValiPlan no debe inventar estadísticas, normas ni referencias. Control específico: verifique que la respuesta trate por qué ese mercado es prioritario y alcanzable y no sustituya este dato con información de otro apartado.'
),

-- Competidores\
 (
    0,
    '¿Quiénes compiten directa e indirectamente?',
    'Para precisar quiénes compiten directa e indirectamente, identifique al actor concreto y explique su función, necesidad o responsabilidad dentro de competidores. Evite respuestas amplias como “todos” o “el público en general”.',
    '“El cliente principal será una persona adulta de mi ciudad que busca una solución confiable, compara opciones por Internet y valora la puntualidad.” En esta respuesta, el foco es: quiénes compiten directa e indirectamente.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate quiénes compiten directa e indirectamente y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué ofrecen, a qué precio, por qué canal y con qué reputación?',
    'Para responder sobre qué ofrecen, a qué precio, por qué canal y con qué reputación, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en competidores.',
    '“Ofreceré productos personalizados por pedido, con opciones estándar y premium, mediante atención presencial y digital.” En esta respuesta, el foco es: qué ofrecen, a qué precio, por qué canal y con qué reputación.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate qué ofrecen, a qué precio, por qué canal y con qué reputación y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿En qué son fuertes, débiles y dónde queda espacio?',
    'Para delimitar en qué son fuertes, débiles y dónde queda espacio, use una descripción genérica o la ubicación confirmada por el usuario: “mi ciudad”, “mi región” o “la zona atendida”. Indique cobertura y restricciones.',
    '“El negocio funcionará en mi ciudad y atenderá inicialmente las zonas cercanas mediante recojo y entrega programada.” En esta respuesta, el foco es: en qué son fuertes, débiles y dónde queda espacio.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate en qué son fuertes, débiles y dónde queda espacio y no sustituya este dato con información de otro apartado.'
),

-- Proveedores 
(
    0,
    '¿Qué proveedores existen para cada insumo crítico?',
    'Para precisar qué proveedores existen para cada insumo crítico, identifique al actor concreto y explique su función, necesidad o responsabilidad dentro de proveedores. Evite respuestas amplias como “todos” o “el público en general”.',
    '“Un proveedor local abastecerá el insumo principal y un servicio externo realizará entregas en horas de alta demanda.” En esta respuesta, el foco es: qué proveedores existen para cada insumo crítico.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué proveedores existen para cada insumo crítico y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué condiciones, calidad, precio y plazo ofrecen?',
    'Para responder sobre qué condiciones, calidad, precio y plazo ofrecen, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en proveedores.',
    '“Ofreceré productos personalizados por pedido, con opciones estándar y premium, mediante atención presencial y digital.” En esta respuesta, el foco es: qué condiciones, calidad, precio y plazo ofrecen.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate qué condiciones, calidad, precio y plazo ofrecen y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué dependencia existe y qué alternativa tendrá?',
    'Para responder qué dependencia existe y qué alternativa tendrá, mencione los elementos indispensables para proveedores y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Las alternativas ofrecen un servicio básico; queda espacio para una opción con seguimiento, personalización y garantía clara.” En esta respuesta, el foco es: qué dependencia existe y qué alternativa tendrá.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué dependencia existe y qué alternativa tendrá y no sustituya este dato con información de otro apartado.'
),

-- productos sustitutos
(
    0,
    '¿Qué puede usar el cliente en lugar de la oferta?',
    'Para precisar qué puede usar el cliente en lugar de la oferta, identifique al actor concreto y explique su función, necesidad o responsabilidad dentro de productos sustitutos. Evite respuestas amplias como “todos” o “el público en general”.',
    '“El cliente principal será una persona adulta de mi ciudad que busca una solución confiable, compara opciones por Internet y valora la puntualidad.” En esta respuesta, el foco es: qué puede usar el cliente en lugar de la oferta.',
    'Solicite solo datos necesarios y evite estereotipos o características que no influyan en la compra. Proteja información personal. Control específico: verifique que la respuesta trate qué puede usar el cliente en lugar de la oferta y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué ventaja y costo de cambio tiene cada sustituto?',
    'Para responder sobre qué ventaja y costo de cambio tiene cada sustituto, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en productos sustitutos.',
    '“El alquiler y la remuneración son costos fijos; los materiales y la entrega varían con cada pedido.” En esta respuesta, el foco es: qué ventaja y costo de cambio tiene cada sustituto.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate qué ventaja y costo de cambio tiene cada sustituto y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Cómo responderá la empresa?',
    'Para explicar cómo responderá la empresa, describa la secuencia en orden: quién actúa, qué recibe, qué hace y qué resultado entrega. Incluya las excepciones relevantes para productos sustitutos.',
    '“Para productos sustitutos, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: cómo responderá la empresa.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cómo responderá la empresa y no sustituya este dato con información de otro apartado.'
),

-- nuevos competidopres 
(
    0,
    '¿Qué tan fácil es ingresar al mercado?',
    'Para delimitar qué tan fácil es ingresar al mercado, use una descripción genérica o la ubicación confirmada por el usuario: “mi ciudad”, “mi región” o “la zona atendida”. Indique cobertura y restricciones.',
    '“Para nuevos competidores, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué tan fácil es ingresar al mercado.',
    'Toda afirmación externa debe incluir fuente verificable y fecha. ValiPlan no debe inventar estadísticas, normas ni referencias. Control específico: verifique que la respuesta trate qué tan fácil es ingresar al mercado y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué barreras, capacidades o regulaciones influyen?',
    'Para responder qué barreras, capacidades o regulaciones influyen, mencione los elementos indispensables para nuevos competidores y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“El principal riesgo es depender de un solo proveedor; se mitigará homologando una segunda alternativa.” En esta respuesta, el foco es: qué barreras, capacidades o regulaciones influyen.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué barreras, capacidades o regulaciones influyen y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué defensa legítima puede construir la empresa?',
    'Para responder qué defensa legítima puede construir la empresa, mencione los elementos indispensables para nuevos competidores y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para nuevos competidores, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué defensa legítima puede construir la empresa.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué defensa legítima puede construir la empresa y no sustituya este dato con información de otro apartado.'
),

-- Cinco fuerzas de porter 
(
    0,
    '¿Qué factores determinan cada fuerza competitiva?',
    'Para responder qué factores determinan cada fuerza competitiva, mencione los elementos indispensables para cinco fuerzas de Porter y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Las alternativas ofrecen un servicio básico; queda espacio para una opción con seguimiento, personalización y garantía clara.” En esta respuesta, el foco es: qué factores determinan cada fuerza competitiva.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué factores determinan cada fuerza competitiva y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué peso y calificación obtiene cada factor?',
    'Para responder qué peso y calificación obtiene cada factor, mencione los elementos indispensables para cinco fuerzas de Porter y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para cinco fuerzas de Porter, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué peso y calificación obtiene cada factor.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué peso y calificación obtiene cada factor y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué muestra el resultado sobre el atractivo del sector?',
    'Para responder qué muestra el resultado sobre el atractivo del sector, mencione los elementos indispensables para cinco fuerzas de Porter y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Se aplicará una encuesta breve al segmento definido y entrevistas para comprender razones y objeciones.” En esta respuesta, el foco es: qué muestra el resultado sobre el atractivo del sector.',
    'No generalice resultados si la muestra o el método no lo permiten. Registre sesgos, alcance y limitaciones. Control específico: verifique que la respuesta trate qué muestra el resultado sobre el atractivo del sector y no sustituya este dato con información de otro apartado.'
),

-- matriz MEFE
(
    0,
    '¿Cuáles son las oportunidades y amenazas prioritarias?',
    'Para responder sobre cuáles son las oportunidades y amenazas prioritarias, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en matriz mefe.',
    '“Para matriz mefe, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: cuáles son las oportunidades y amenazas prioritarias.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cuáles son las oportunidades y amenazas prioritarias y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué importancia y capacidad de respuesta tiene cada una?',
    'Para responder qué importancia y capacidad de respuesta tiene cada una, mencione los elementos indispensables para matriz mefe y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para matriz mefe, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué importancia y capacidad de respuesta tiene cada una.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué importancia y capacidad de respuesta tiene cada una y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué significa el puntaje y qué acciones prioriza?',
    'Para responder qué significa el puntaje y qué acciones prioriza, mencione los elementos indispensables para matriz mefe y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para matriz mefe, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué significa el puntaje y qué acciones prioriza.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué significa el puntaje y qué acciones prioriza y no sustituya este dato con información de otro apartado.'
),

-- Metodo de inverstigacion
(
    0,
    '¿Qué necesita comprobar sobre clientes y demanda?',
    'Para precisar qué necesita comprobar sobre clientes y demanda, identifique al actor concreto y explique su función, necesidad o responsabilidad dentro de método de investigación. Evite respuestas amplias como “todos” o “el público en general”.',
    '“El cliente principal será una persona adulta de mi ciudad que busca una solución confiable, compara opciones por Internet y valora la puntualidad.” En esta respuesta, el foco es: qué necesita comprobar sobre clientes y demanda.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate qué necesita comprobar sobre clientes y demanda y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué método, técnica y muestra usará?',
    'Para responder qué método, técnica y muestra usará, mencione los elementos indispensables para método de investigación y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Se aplicará una encuesta breve al segmento definido y entrevistas para comprender razones y objeciones.” En esta respuesta, el foco es: qué método, técnica y muestra usará.',
    'No generalice resultados si la muestra o el método no lo permiten. Registre sesgos, alcance y limitaciones. Control específico: verifique que la respuesta trate qué método, técnica y muestra usará y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Cómo controlará calidad, sesgos y consentimiento?',
    'Para explicar cómo controlará calidad, sesgos y consentimiento, describa la secuencia en orden: quién actúa, qué recibe, qué hace y qué resultado entrega. Incluya las excepciones relevantes para método de investigación.',
    '“La calidad se comprobará mediante especificaciones, entrega puntual, tasa de reclamos y satisfacción del cliente.” En esta respuesta, el foco es: cómo controlará calidad, sesgos y consentimiento.',
    'Evite afirmaciones absolutas o promocionales sin comparación y evidencia. Explique cómo se demostrará la diferencia. Control específico: verifique que la respuesta trate cómo controlará calidad, sesgos y consentimiento y no sustituya este dato con información de otro apartado.'
),

-- Poblacion y muestra 
(
    0,
    '¿Quiénes forman la población y cómo se cuantifican?',
    'Para precisar quiénes forman la población y cómo se cuantifican, identifique al actor concreto y explique su función, necesidad o responsabilidad dentro de población y muestra. Evite respuestas amplias como “todos” o “el público en general”.',
    '“El cliente principal será una persona adulta de mi ciudad que busca una solución confiable, compara opciones por Internet y valora la puntualidad.” En esta respuesta, el foco es: quiénes forman la población y cómo se cuantifican.',
    'Toda afirmación externa debe incluir fuente verificable y fecha. ValiPlan no debe inventar estadísticas, normas ni referencias. Control específico: verifique que la respuesta trate quiénes forman la población y cómo se cuantifican y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué nivel de confianza, error y muestreo se usarán?',
    'Para responder qué nivel de confianza, error y muestreo se usarán, mencione los elementos indispensables para población y muestra y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para población y muestra, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué nivel de confianza, error y muestreo se usarán.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué nivel de confianza, error y muestreo se usarán y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué muestra resulta y cómo se distribuirá?',
    'Para explicar qué muestra resulta y cómo se distribuirá, describa la secuencia en orden: quién actúa, qué recibe, qué hace y qué resultado entrega. Incluya las excepciones relevantes para población y muestra.',
    '“Se aplicará una encuesta breve al segmento definido y entrevistas para comprender razones y objeciones.” En esta respuesta, el foco es: qué muestra resulta y cómo se distribuirá.',
    'No generalice resultados si la muestra o el método no lo permiten. Registre sesgos, alcance y limitaciones. Control específico: verifique que la respuesta trate qué muestra resulta y cómo se distribuirá y no sustituya este dato con información de otro apartado.'
),

-- Instrumento de recoleccion 
(
    0,
    '¿Qué decisiones debe apoyar el instrumento?',
    'Para responder qué decisiones debe apoyar el instrumento, mencione los elementos indispensables para instrumento de recolección y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para instrumento de recolección, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué decisiones debe apoyar el instrumento.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué decisiones debe apoyar el instrumento y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué preguntas medirán necesidad, conducta, frecuencia y pago?',
    'Para responder sobre qué preguntas medirán necesidad, conducta, frecuencia y pago, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en instrumento de recolección.',
    '“Los clientes pierden tiempo porque las alternativas actuales no ofrecen información clara ni entrega dentro del plazo acordado.” En esta respuesta, el foco es: qué preguntas medirán necesidad, conducta, frecuencia y pago.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué preguntas medirán necesidad, conducta, frecuencia y pago y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Cómo se piloteará y validará?',
    'Para explicar cómo se piloteará y validará, describa la secuencia en orden: quién actúa, qué recibe, qué hace y qué resultado entrega. Incluya las excepciones relevantes para instrumento de recolección.',
    '“Para instrumento de recolección, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: cómo se piloteará y validará.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cómo se piloteará y validará y no sustituya este dato con información de otro apartado.'
),

-- resultados y tamna de mercado 
(
    0,
    '¿Cuántas respuestas válidas obtuvo y cómo limpio los datos?',
    'Para explicar cuántas respuestas válidas obtuvo y cómo limpio los datos, describa la secuencia en orden: quién actúa, qué recibe, qué hace y qué resultado entrega. Incluya las excepciones relevantes para resultados y tamaño del mercado.',
    '“Para resultados y tamaño del mercado, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: cuántas respuestas válidas obtuvo y cómo limpio los datos.',
    'Solicite solo datos necesarios y evite estereotipos o características que no influyan en la compra. Proteja información personal. Control específico: verifique que la respuesta trate cuántas respuestas válidas obtuvo y cómo limpio los datos y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué resultados y cruces son relevantes?',
    'Para responder qué resultados y cruces son relevantes, mencione los elementos indispensables para resultados y tamaño del mercado y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“El motor calculará el resultado con entradas verificadas y mostrará fórmulas, supuestos y escenarios.” En esta respuesta, el foco es: qué resultados y cruces son relevantes.',
    'El cálculo debe ejecutarse con fórmulas deterministas y entradas aprobadas. La IA solo explica resultados; no reemplaza el motor financiero. Control específico: verifique que la respuesta trate qué resultados y cruces son relevantes y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué mercado potencial, disponible y objetivo resulta?',
    'Para delimitar qué mercado potencial, disponible y objetivo resulta, use una descripción genérica o la ubicación confirmada por el usuario: “mi ciudad”, “mi región” o “la zona atendida”. Indique cobertura y restricciones.',
    '“Para resultados y tamaño del mercado, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué mercado potencial, disponible y objetivo resulta.',
    'Toda afirmación externa debe incluir fuente verificable y fecha. ValiPlan no debe inventar estadísticas, normas ni referencias. Control específico: verifique que la respuesta trate qué mercado potencial, disponible y objetivo resulta y no sustituya este dato con información de otro apartado.'
),

-- proyeccion de demandas y muestras 
(
    0,
    '¿Qué demanda base, productos y precios proyectará?',
    'Para responder sobre qué demanda base, productos y precios proyectará, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en proyección de demanda y ventas.',
    '“Ofreceré productos personalizados por pedido, con opciones estándar y premium, mediante atención presencial y digital.” En esta respuesta, el foco es: qué demanda base, productos y precios proyectará.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate qué demanda base, productos y precios proyectará y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué crecimiento, estacionalidad y mezcla sustentan la proyección?',
    'Para responder qué crecimiento, estacionalidad y mezcla sustentan la proyección, mencione los elementos indispensables para proyección de demanda y ventas y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para proyección de demanda y ventas, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué crecimiento, estacionalidad y mezcla sustentan la proyección.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué crecimiento, estacionalidad y mezcla sustentan la proyección y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Cómo cambian los resultados en escenarios?',
    'Para explicar cómo cambian los resultados en escenarios, describa la secuencia en orden: quién actúa, qué recibe, qué hace y qué resultado entrega. Incluya las excepciones relevantes para proyección de demanda y ventas.',
    '“Para proyección de demanda y ventas, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: cómo cambian los resultados en escenarios.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cómo cambian los resultados en escenarios y no sustituya este dato con información de otro apartado.'
),

-- CAPITULO 4
 -- producto o servicio 
 (
    0,
    '¿Qué oferta comercializará inicialmente y para qué segmento?',
    'Para responder qué oferta comercializará inicialmente y para qué segmento, mencione los elementos indispensables para producto o servicio y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para producto o servicio, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué oferta comercializará inicialmente y para qué segmento.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué oferta comercializará inicialmente y para qué segmento y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué atributos, beneficios, marca y presentación tendrá?',
    'Para responder qué atributos, beneficios, marca y presentación tendrá, mencione los elementos indispensables para producto o servicio y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para producto o servicio, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué atributos, beneficios, marca y presentación tendrá.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué atributos, beneficios, marca y presentación tendrá y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué garantía, complemento y evolución se prevén?',
    'Para responder qué garantía, complemento y evolución se prevén, mencione los elementos indispensables para producto o servicio y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para producto o servicio, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué garantía, complemento y evolución se prevén.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué garantía, complemento y evolución se prevén y no sustituya este dato con información de otro apartado.'
),
-- Precio 
(
    0,
    '¿Qué costo, margen y referencia de mercado tiene cada oferta?',
    'Para responder sobre qué costo, margen y referencia de mercado tiene cada oferta, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en precio.',
    '“El alquiler y la remuneración son costos fijos; los materiales y la entrega varían con cada pedido.” En esta respuesta, el foco es: qué costo, margen y referencia de mercado tiene cada oferta.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate qué costo, margen y referencia de mercado tiene cada oferta y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué precio y estrategia aplicará a cada segmento?',
    'Para responder sobre qué precio y estrategia aplicará a cada segmento, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en precio.',
    '“El precio preliminar será de S/ 80 por unidad, sujeto a costo, personalización y validación con clientes.” En esta respuesta, el foco es: qué precio y estrategia aplicará a cada segmento.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate qué precio y estrategia aplicará a cada segmento y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué descuentos y condiciones respetarán el margen?',
    'Para responder qué descuentos y condiciones respetarán el margen, mencione los elementos indispensables para precio y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para precio, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué descuentos y condiciones respetarán el margen.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué descuentos y condiciones respetarán el margen y no sustituya este dato con información de otro apartado.'
),
-- Plaza y canales 
(
    0,
    '¿Dónde y por qué canal comprará cada segmento?',
    'Para fundamentar dónde y por qué canal comprará cada segmento, explique la causa y vincúlela con un beneficio, problema o evidencia del proyecto. Si es una opinión, márquela como hipótesis pendiente de validación.',
    '“El negocio funcionará en mi ciudad y atenderá inicialmente las zonas cercanas mediante recojo y entrega programada.” En esta respuesta, el foco es: dónde y por qué canal comprará cada segmento.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate dónde y por qué canal comprará cada segmento y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Cómo se entregará, con qué cobertura, tiempo y costo?',
    'Para responder sobre cómo se entregará, con qué cobertura, tiempo y costo, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en plaza y canales.',
    '“El alquiler y la remuneración son costos fijos; los materiales y la entrega varían con cada pedido.” En esta respuesta, el foco es: cómo se entregará, con qué cobertura, tiempo y costo.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate cómo se entregará, con qué cobertura, tiempo y costo y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué intermediarios y capacidad requiere?',
    'Para responder qué intermediarios y capacidad requiere, mencione los elementos indispensables para plaza y canales y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para plaza y canales, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué intermediarios y capacidad requiere.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué intermediarios y capacidad requiere y no sustituya este dato con información de otro apartado.'
),
-- promocion 
(
    0,
    '¿Qué comportamiento desea generar en cada segmento?',
    'Para responder qué comportamiento desea generar en cada segmento, mencione los elementos indispensables para promoción y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para promoción, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué comportamiento desea generar en cada segmento.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué comportamiento desea generar en cada segmento y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué mensaje, medio y acción utilizará?',
    'Para responder qué mensaje, medio y acción utilizará, mencione los elementos indispensables para promoción y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para promoción, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué mensaje, medio y acción utilizará.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué mensaje, medio y acción utilizará y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Cuándo, cuánto costará y cómo medirá resultados?',
    'Para responder sobre cuándo, cuánto costará y cómo medirá resultados, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en promoción.',
    '“Para promoción, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: cuándo, cuánto costará y cómo medirá resultados.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cuándo, cuánto costará y cómo medirá resultados y no sustituya este dato con información de otro apartado.'
),

-- Personas 
(
    0,
    '¿Quiénes interactúan con el cliente y en qué momentos?',
    'Para precisar quiénes interactúan con el cliente y en qué momentos, identifique al actor concreto y explique su función, necesidad o responsabilidad dentro de personas. Evite respuestas amplias como “todos” o “el público en general”.',
    '“El cliente principal será una persona adulta de mi ciudad que busca una solución confiable, compara opciones por Internet y valora la puntualidad.” En esta respuesta, el foco es: quiénes interactúan con el cliente y en qué momentos.',
    'Solicite solo datos necesarios y evite estereotipos o características que no influyan en la compra. Proteja información personal. Control específico: verifique que la respuesta trate quiénes interactúan con el cliente y en qué momentos y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué competencias y protocolo de servicio necesitan?',
    'Para responder qué competencias y protocolo de servicio necesitan, mencione los elementos indispensables para personas y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Ofreceré productos personalizados por pedido, con opciones estándar y premium, mediante atención presencial y digital.” En esta respuesta, el foco es: qué competencias y protocolo de servicio necesitan.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué competencias y protocolo de servicio necesitan y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Cómo se capacitarán, evaluarán e incentivarán?',
    'Para explicar cómo se capacitarán, evaluarán e incentivarán, describa la secuencia en orden: quién actúa, qué recibe, qué hace y qué resultado entrega. Incluya las excepciones relevantes para personas.',
    '“Para personas, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: cómo se capacitarán, evaluarán e incentivarán.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cómo se capacitarán, evaluarán e incentivarán y no sustituya este dato con información de otro apartado.'
),

-- Procesos 
(
    0,
    '¿Qué pasos sigue el cliente desde descubrir hasta posventa?',
    'Para precisar qué pasos sigue el cliente desde descubrir hasta posventa, identifique al actor concreto y explique su función, necesidad o responsabilidad dentro de procesos. Evite respuestas amplias como “todos” o “el publico en general”.',
    '“El cliente principal será una persona adulta de mi ciudad que busca una solución confiable, compara opciones por Internet y valora la puntualidad.” En esta respuesta, el foco es: qué pasos sigue el cliente desde descubrir hasta posventa.',
    'Solicite solo datos necesarios y evite estereotipos o características que no influyan en la compra. Proteja información personal. Control específico: verifique que la respuesta trate qué pasos sigue el cliente desde descubrir hasta posventa y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Dónde puede frustrarse o abandonar?',
    'Para delimitar dónde puede frustrarse o abandonar, use una descripción genérica o la ubicación confirmada por el usuario: “mi ciudad”, “mi región” o “la zona atendida”. Indique cobertura y restricciones.',
    '“El negocio funcionará en mi ciudad y atenderá inicialmente las zonas cercanas mediante recojo y entrega programada.” En esta respuesta, el foco es: dónde puede frustrarse o abandonar.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate dónde puede frustrarse o abandonar y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué estándar y mejora se aplicará en cada punto?',
    'Para responder qué estándar y mejora se aplicará en cada punto, mencione los elementos indispensables para procesos y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para procesos, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué estándar y mejora se aplicará en cada punto.',
    'Evite afirmaciones absolutas o promocionales sin comparación y evidencia. Explique cómo se demostrará la diferencia. Control específico: verifique que la respuesta trate qué estándar y mejora se aplicará en cada punto y no sustituya este dato con información de otro apartado.'
),
-- Evidencia fisica 
(
    0,
    '¿Qué elementos visibles demostrarán calidad y confianza?',
    'Para responder qué elementos visibles demostrarán calidad y confianza, mencione los elementos indispensables para evidencia física y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“La calidad se comprobará mediante especificaciones, entrega puntual, tasa de reclamos y satisfacción del cliente.” En esta respuesta, el foco es: qué elementos visibles demostrarán calidad y confianza.',
    'Evite afirmaciones absolutas o promocionales sin comparación y evidencia. Explique cómo se demostrará la diferencia. Control específico: verifique que la respuesta trate qué elementos visibles demostrarán calidad y confianza y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Cómo se presentarán local, empaque, personal y canales digitales?',
    'Para precisar cómo se presentarán local, empaque, personal y canales digitales, identifique al actor concreto y explique su función, necesidad o responsabilidad dentro de evidencia física. Evite respuestas amplias como “todos” o “el publico en general”.',
    '“Para evidencia física, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: cómo se presentarán local, empaque, personal y canales digitales.',
    'Solicite solo datos necesarios y evite estereotipos o características que no influyan en la compra. Proteja información personal. Control específico: verifique que la respuesta trate cómo se presentarán local, empaque, personal y canales digitales y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué evidencia necesita cada segmento?',
    'Para responder qué evidencia necesita cada segmento, mencione los elementos indispensables para evidencia física y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“En una prueba de dos semanas, 20 personas solicitaron información, 8 pidieron cotización y 4 realizaron una compra.” En esta respuesta, el foco es: qué evidencia necesita cada segmento.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué evidencia necesita cada segmento y no sustituya este dato con información de otro apartado.'
),

-- Experiencia del cliente 
(
    0,
    '¿Qué etapas recorre el cliente y qué espera en cada una?',
    'Para precisar qué etapas recorre el cliente y qué espera en cada una, identifique al actor concreto y explique su función, necesidad o responsabilidad dentro de experiencia del cliente. Evite respuestas amplias como “todos” o “el publico en general”.',
    '“El cliente principal será una persona adulta de mi ciudad que busca una solución confiable, compara opciones por Internet y valora la puntualidad.” En esta respuesta, el foco es: qué etapas recorre el cliente y qué espera en cada una.',
    'Solicite solo datos necesarios y evite estereotipos o características que no influyan en la compra. Proteja información personal. Control específico: verifique que la respuesta trate qué etapas recorre el cliente y qué espera en cada una y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué dificultades puede encontrar y cómo se resolverán?',
    'Para explicar qué dificultades puede encontrar y cómo se resolverán, describa la secuencia en orden: quién actúa, qué recibe, qué hace y qué resultado entrega. Incluya las excepciones relevantes para experiencia del cliente.',
    '“El principal riesgo es depender de un solo proveedor; se mitigará homologando una segunda alternativa.” En esta respuesta, el foco es: qué dificultades puede encontrar y cómo se resolverán.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué dificultades puede encontrar y cómo se resolverán y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué experiencia se asegurará y cómo se medirá?',
    'Para explicar qué experiencia se asegurará y cómo se medirá, describa la secuencia en orden: quién actúa, qué recibe, qué hace y qué resultado entrega. Incluya las excepciones relevantes para experiencia del cliente.',
    '“Para experiencia del cliente, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué experiencia se asegurará y cómo se medirá.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué experiencia se asegurará y cómo se medirá y no sustituya este dato con información de otro apartado.'
),
-- Marketing MIx integrado 
(
    0,
    '¿Las decisiones de producto, precio, plaza y promoción apuntan al mismo cliente?',
    'Para responder sobre si las decisiones de producto, precio, plaza y promoción apuntan al mismo cliente, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en marketing mix integrado.',
    '“Ofreceré productos personalizados por pedido, con opciones estándar y premium, mediante atención presencial y digital.” En esta respuesta, el foco es: las decisiones de producto, precio, plaza y promoción apuntan al mismo cliente.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate si las decisiones de producto, precio, plaza y promoción apuntan al mismo cliente y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Personas, procesos y evidencia respaldan la promesa?',
    'Para precisar si personas, procesos y evidencia respaldan la promesa, identifique al actor concreto y explique su función, necesidad o responsabilidad dentro de marketing mix integrado. Evite respuestas amplias como “todos” o “el publico en general”.',
    '“En una prueba de dos semanas, 20 personas solicitaron información, 8 pidieron cotización y 4 realizaron una compra.” En esta respuesta, el foco es: personas, procesos y evidencia respaldan la promesa.',
    'Solicite solo datos necesarios y evite estereotipos o características que no influyan en la compra. Proteja información personal. Control específico: verifique que la respuesta trate si personas, procesos y evidencia respaldan la promesa y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué componente es más débil y qué ajuste requiere?',
    'Para responder qué componente es más débil y qué ajuste requiere, mencione los elementos indispensables para marketing mix integrado y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para marketing mix integrado, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué componente es más débil y qué ajuste requiere.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué componente es más débil y qué ajuste requiere y no sustituya este dato con información de otro apartado.'
),

-- Programa de acciones atacticas 
(
    0,
    '¿Qué acciones concretas ejecutarán cada estrategia?',
    'Para responder qué acciones concretas ejecutarán cada estrategia, mencione los elementos indispensables para programa de acciones tácticas y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para programa de acciones tácticas, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué acciones concretas ejecutarán cada estrategia.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué acciones concretas ejecutarán cada estrategia y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Quién, cuándo, con qué presupuesto y dependencia?',
    'Para precisar quién, cuándo, con qué presupuesto y dependencia, identifique al actor concreto y explique su función, necesidad o responsabilidad dentro de programa de acciones tácticas. Evite respuestas amplias como “todos” o “el publico en general”.',
    '“El cliente principal será una persona adulta de mi ciudad que busca una solución confiable, compara opciones por Internet y valora la puntualidad.” En esta respuesta, el foco es: quién, cuándo, con qué presupuesto y dependencia.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate quién, cuándo, con qué presupuesto y dependencia y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué entregable y meta cierran cada acción?',
    'Para responder qué entregable y meta cierran cada acción, mencione los elementos indispensables para programa de acciones tácticas y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Incrementar la recompra del 20 % al 30 % durante el primer año, medida mensualmente por el responsable comercial.” En esta respuesta, el foco es: qué entregable y meta cierran cada acción.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué entregable y meta cierran cada acción y no sustituya este dato con información de otro apartado.'
),

-- Prwesupuesto de marketing 
(
    0,
    '¿Cuánto cuesta cada acción y con qué frecuencia se realiza?',
    'Para responder sobre cuánto cuesta cada acción y con qué frecuencia se realiza, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en presupuesto de marketing.',
    '“Para presupuesto de marketing, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: cuánto cuesta cada acción y con qué frecuencia se realiza.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cuánto cuesta cada acción y con qué frecuencia se realiza y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué costo mensual, anual y contingencia resultan?',
    'Para responder sobre qué costo mensual, anual y contingencia resultan, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en presupuesto de marketing.',
    '“El alquiler y la remuneración son costos fijos; los materiales y la entrega varían con cada pedido.” En esta respuesta, el foco es: qué costo mensual, anual y contingencia resultan.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate qué costo mensual, anual y contingencia resultan y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué ventas o indicadores justificarán la inversión?',
    'Para fundamentar qué ventas o indicadores justificarán la inversión, explique la causa y vincúlela con un beneficio, problema o evidencia del proyecto. Si es una opinión, márquela como hipótesis pendiente de validación.',
    '“Incrementar la recompra del 20 % al 30 % durante el primer año, medida mensualmente por el responsable comercial.” En esta respuesta, el foco es: qué ventas o indicadores justificarán la inversión.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate qué ventas o indicadores justificarán la inversión y no sustituya este dato con información de otro apartado.'
),

-- Programa y pronostico de ventas 
(
    0,
    '¿Cuántas unidades espera vender por producto, canal y periodo?',
    'Para responder sobre cuántas unidades espera vender por producto, canal y periodo, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en programa y pronóstico de ventas.',
    '“Ofreceré productos personalizados por pedido, con opciones estándar y premium, mediante atención presencial y digital.” En esta respuesta, el foco es: cuántas unidades espera vender por producto, canal y periodo.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cuántas unidades espera vender por producto, canal y periodo y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué clientes, frecuencia y conversión sustentan la meta?',
    'Para responder sobre qué clientes, frecuencia y conversión sustentan la meta, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en programa y pronóstico de ventas.',
    '“El cliente principal será una persona adulta de mi ciudad que busca una solución confiable, compara opciones por Internet y valora la puntualidad.” En esta respuesta, el foco es: qué clientes, frecuencia y conversión sustentan la meta.',
    'Solicite solo datos necesarios y evite estereotipos o características que no influyan en la compra. Proteja información personal. Control específico: verifique que la respuesta trate qué clientes, frecuencia y conversión sustentan la meta y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué responsables, acciones y escenarios requiere?',
    'Para precisar qué responsables, acciones y escenarios requiere, identifique al actor concreto y explique su función, necesidad o responsabilidad dentro de programa y pronóstico de ventas. Evite respuestas amplias como “todos” o “el publico en general”.',
    '“Para programa y pronóstico de ventas, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué responsables, acciones y escenarios requiere.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué responsables, acciones y escenarios requiere y no sustituya este dato con información de otro apartado.'
),


-- CAPITULO V 
-- Ubicacion del negocio
(
    0,
    '¿Qué ubicaciones evalúa y qué necesita el negocio de una ubicación?',
    'Para responder sobre qué ubicaciones evalúa y qué necesita el negocio de una ubicación, indique las alternativas de ubicación consideradas, describa las necesidades que debe cumplir cada una y señale la cobertura geográfica y las restricciones relevantes. Puede utilizar una descripción genérica como “mi ciudad”, “mi región” o “la zona atendida”, o la ubicación confirmada por el usuario. Distinga si la información es real, propuesta o estimada.',
    '“Se evaluarán dos ubicaciones en mi ciudad. La primera está cerca de la zona comercial y facilita el acceso de clientes; la segunda ofrece un menor costo de alquiler y mejor espacio para almacenamiento. El negocio requiere una ubicación con fácil acceso, servicios básicos y cobertura para atender las zonas cercanas.” En esta respuesta, el foco es: qué ubicaciones evalúa y qué necesita el negocio de una ubicación.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué ubicaciones evalúa y qué necesita el negocio de una ubicación y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué criterios, costos y riesgos tiene cada alternativa?',
    'Para responder sobre qué criterios, costos y riesgos tiene cada alternativa, describa los criterios utilizados para evaluar cada opción, registre los costos con su cifra, unidad y periodo, e identifique los riesgos asociados. Indique si los datos son reales, cotizados, calculados o estimados, y anote la fuente o supuesto utilizado en ubicación del negocio.',
    '“La primera alternativa tiene un alquiler de S/ 1,500 por mes y ofrece mayor afluencia de clientes, aunque presenta un mayor riesgo de congestión vehicular. La segunda alternativa cuesta S/ 1,000 por mes, dispone de mayor espacio y menor costo operativo, pero tiene menor visibilidad comercial.” En esta respuesta, el foco es: qué criterios, costos y riesgos tiene cada alternativa.',
    'No use cifras sin unidad, periodo y origen. Toda estimación debe identificarse claramente y no presentarse como un dato comprobado. Control específico: verifique que la respuesta trate qué criterios, costos y riesgos tiene cada alternativa y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Cuál se selecciona y qué evidencia la sustenta?',
    'Para responder sobre cuál se selecciona y qué evidencia la sustenta, indique la alternativa elegida, explique las razones de la selección y describa las evidencias que respaldan la decisión. Distinga entre datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Se seleccionó la primera ubicación porque ofrece mayor flujo de clientes, mejor accesibilidad y un costo compatible con el presupuesto. La decisión se sustenta en cotizaciones de alquiler, visitas al lugar, análisis del tránsito peatonal y la comparación de costos operativos entre las alternativas.” En esta respuesta, el foco es: cuál se selecciona y qué evidencia la sustenta.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cuál se selecciona y qué evidencia la sustenta y no sustituya este dato con información de otro apartado.'
),(
    0,
    '¿Qué ubicaciones evalúa y qué necesita el negocio de una ubicación?',
    'Para responder sobre qué ubicaciones evalúa y qué necesita el negocio de una ubicación, indique las alternativas de ubicación consideradas, describa las necesidades que debe cumplir cada una y señale la cobertura geográfica y las restricciones relevantes. Puede utilizar una descripción genérica como “mi ciudad”, “mi región” o “la zona atendida”, o la ubicación confirmada por el usuario. Distinga si la información es real, propuesta o estimada.',
    '“Se evaluarán dos ubicaciones en mi ciudad. La primera está cerca de la zona comercial y facilita el acceso de clientes; la segunda ofrece un menor costo de alquiler y mejor espacio para almacenamiento. El negocio requiere una ubicación con fácil acceso, servicios básicos y cobertura para atender las zonas cercanas.” En esta respuesta, el foco es: qué ubicaciones evalúa y qué necesita el negocio de una ubicación.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué ubicaciones evalúa y qué necesita el negocio de una ubicación y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué criterios, costos y riesgos tiene cada alternativa?',
    'Para responder sobre qué criterios, costos y riesgos tiene cada alternativa, describa los criterios utilizados para evaluar cada opción, registre los costos con su cifra, unidad y periodo, e identifique los riesgos asociados. Indique si los datos son reales, cotizados, calculados o estimados, y anote la fuente o supuesto utilizado en ubicación del negocio.',
    '“La primera alternativa tiene un alquiler de S/ 1,500 por mes y ofrece mayor afluencia de clientes, aunque presenta un mayor riesgo de congestión vehicular. La segunda alternativa cuesta S/ 1,000 por mes, dispone de mayor espacio y menor costo operativo, pero tiene menor visibilidad comercial.” En esta respuesta, el foco es: qué criterios, costos y riesgos tiene cada alternativa.',
    'No use cifras sin unidad, periodo y origen. Toda estimación debe identificarse claramente y no presentarse como un dato comprobado. Control específico: verifique que la respuesta trate qué criterios, costos y riesgos tiene cada alternativa y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Cuál se selecciona y qué evidencia la sustenta?',
    'Para responder sobre cuál se selecciona y qué evidencia la sustenta, indique la alternativa elegida, explique las razones de la selección y describa las evidencias que respaldan la decisión. Distinga entre datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Se seleccionó la primera ubicación porque ofrece mayor flujo de clientes, mejor accesibilidad y un costo compatible con el presupuesto. La decisión se sustenta en cotizaciones de alquiler, visitas al lugar, análisis del tránsito peatonal y la comparación de costos operativos entre las alternativas.” En esta respuesta, el foco es: cuál se selecciona y qué evidencia la sustenta.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cuál se selecciona y qué evidencia la sustenta y no sustituya este dato con información de otro apartado.'
),
-- Produccion de bienes o prestacion de servicios
(
    0,
    '¿Qué bienes o servicios debe entregar y en qué volumen?',
    'Para responder sobre qué bienes o servicios debe entregar y en qué volumen, describa los bienes o servicios que ofrecerá, indique el volumen previsto de producción o prestación utilizando una cifra, unidad y periodo, y distinga si la información corresponde a datos confirmados, calculados, estimados o propuestas de ValiPlan.',
    '“Se producirán 300 unidades de polos personalizados por mes para venta directa y pedidos empresariales. El volumen se estimó considerando la capacidad instalada y la demanda proyectada.” En esta respuesta, el foco es: qué bienes o servicios debe entregar y en qué volumen.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué bienes o servicios debe entregar y en qué volumen y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué estándar de calidad y nivel de servicio cumplirá?',
    'Para responder sobre qué estándar de calidad y nivel de servicio cumplirá, describa los criterios de calidad que aplicará a los bienes o servicios y explique los compromisos de atención, tiempos de respuesta, entrega o garantía. Distinga entre datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Todos los productos serán revisados antes de la entrega para verificar su estado y cumplimiento de las especificaciones del cliente. Los pedidos se entregarán en un plazo máximo de 48 horas dentro de la zona de atención y contarán con garantía por defectos de fabricación.” En esta respuesta, el foco es: qué estándar de calidad y nivel de servicio cumplirá.',
    'Evite afirmaciones absolutas o promocionales sin comparación ni evidencia. Explique cómo se demostrará el cumplimiento del estándar de calidad y del nivel de servicio. Control específico: verifique que la respuesta trate qué estándar de calidad y nivel de servicio cumplirá y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué merma, error o devolución es tolerable?',
    'Para responder sobre qué merma, error o devolución es tolerable, indique los niveles máximos aceptables utilizando cifras, unidades y periodos cuando corresponda. Explique las causas previstas, las medidas de control y distinga entre datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Se considera aceptable una merma de hasta el 2 % mensual en materias primas y una tasa máxima de devoluciones del 1 % de los pedidos entregados. Estos valores se controlarán mediante inspecciones de calidad y seguimiento de incidencias.” En esta respuesta, el foco es: qué merma, error o devolución es tolerable.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué merma, error o devolución es tolerable y no sustituya este dato con información de otro apartado.'
),
-- Procesos operativos
(
    0,
    '¿Cuál es el proceso completo desde entrada hasta entrega?',
    'Para responder sobre cuál es el proceso completo desde entrada hasta entrega, describa la secuencia de actividades en orden cronológico indicando quién interviene, qué recibe, qué actividad realiza y qué resultado entrega en cada etapa. Incluya las excepciones o situaciones especiales relevantes para los procesos operativos y distinga entre procedimientos confirmados, propuestos o estimados.',
    '“El proceso inicia con la recepción del pedido, continúa con la validación de la información, la preparación del producto, el control de calidad, el despacho y la entrega al cliente. Si se detecta un error durante el control de calidad, el producto regresa a la etapa de preparación antes de ser despachado.” En esta respuesta, el foco es: cuál es el proceso completo desde entrada hasta entrega.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cuál es el proceso completo desde entrada hasta entrega y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué entra, quién actúa, cuánto tarda y qué sale en cada etapa?',
    'Para responder sobre qué entra, quién actúa, cuánto tarda y qué sale en cada etapa, describa los insumos o información de entrada, identifique al responsable, indique el tiempo requerido utilizando una cifra, unidad y periodo cuando corresponda, y especifique el resultado generado en cada etapa. Distinga si los tiempos son reales, calculados o estimados e indique la fuente o supuesto utilizado en procesos operativos.',
    '“En la etapa de recepción ingresa el pedido del cliente, el asesor comercial verifica la información en aproximadamente 10 minutos y genera la orden de trabajo. En la etapa de producción, el operario recibe la orden, fabrica el producto en un plazo estimado de 2 horas y entrega el producto terminado para control de calidad.” En esta respuesta, el foco es: qué entra, quién actúa, cuánto tarda y qué sale en cada etapa.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué entra, quién actúa, cuánto tarda y qué sale en cada etapa y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué control y cuello de botella existe?',
    'Para responder sobre qué control y cuello de botella existe, describa los controles aplicados para asegurar el correcto funcionamiento del proceso e identifique las etapas que podrían limitar la capacidad, generar retrasos o afectar la calidad. Explique brevemente cada uno y distinga entre datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Se realizará un control de calidad antes del despacho para verificar que el producto cumpla las especificaciones del cliente. El principal cuello de botella se presenta en la etapa de producción cuando coinciden varios pedidos, por lo que se priorizarán los pedidos según su fecha de entrega y se programará capacidad adicional en periodos de alta demanda.” En esta respuesta, el foco es: qué control y cuello de botella existe.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué control y cuello de botella existe y no sustituya este dato con información de otro apartado.'
),
-- Capacidad instalada y utilizada
(
    0,
    '¿Qué capacidad tiene cada recurso por hora, día o mes?',
    'Para responder sobre qué capacidad tiene cada recurso por hora, día o mes, identifique cada recurso relevante (personal, maquinaria, equipos, instalaciones u otros), indique su capacidad utilizando una cifra, unidad y periodo, y explique brevemente cómo contribuye al proceso. Distinga entre datos confirmados, calculados, estimados y propuestas de ValiPlan.',
    '“La máquina principal puede producir 50 unidades por hora, el operario procesa hasta 350 unidades por día y el almacén tiene capacidad para 2,000 unidades. Estos valores se basan en pruebas de operación y especificaciones técnicas.” En esta respuesta, el foco es: qué capacidad tiene cada recurso por hora, día o mes.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué capacidad tiene cada recurso por hora, día o mes y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué restricciones reducen la capacidad teórica?',
    'Para responder sobre qué restricciones reducen la capacidad teórica, identifique los factores que limitan el aprovechamiento máximo de los recursos, como horarios, mantenimiento, disponibilidad de personal, abastecimiento, espacio, tiempos de preparación u otras restricciones operativas. Explique brevemente cada una y distinga entre datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“La capacidad teórica se reduce por los tiempos de mantenimiento preventivo de la maquinaria, la disponibilidad de personal durante un solo turno de trabajo y los tiempos de espera por abastecimiento de insumos. Estas restricciones disminuyen las horas efectivas de producción.” En esta respuesta, el foco es: qué restricciones reducen la capacidad teórica.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué restricciones reducen la capacidad teórica y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué porcentaje se utilizará y existe holgura?',
    'Para responder sobre qué porcentaje se utilizará y si existe holgura, indique el porcentaje estimado o real de utilización de la capacidad, utilizando una cifra, unidad y periodo. Explique el nivel de capacidad disponible para absorber incrementos de demanda e indique si los datos son reales, calculados o estimados, registrando la fuente o supuesto utilizado en capacidad instalada y utilizada.',
    '“Durante el primer año se utilizará aproximadamente el 70 % de la capacidad instalada, lo que dejará una holgura del 30 % para atender incrementos de demanda sin necesidad de ampliar los recursos disponibles.” En esta respuesta, el foco es: qué porcentaje se utilizará y si existe holgura.',
    'No use cifras sin unidad, periodo y origen. Toda estimación debe identificarse claramente y no presentarse como un dato comprobado. Control específico: verifique que la respuesta trate qué porcentaje se utilizará y si existe holgura y no sustituya este dato con información de otro apartado.'
),
-- Plan de produccion o prestacion
(
    0,
    '¿Qué volumen debe producir o atender por periodo?',
    'Para responder sobre qué volumen debe producir o atender por periodo, indique el volumen previsto de producción o prestación utilizando una cifra, unidad y periodo. Explique brevemente cómo se determinó dicho volumen y distinga entre datos confirmados, calculados, estimados y propuestas de ValiPlan.',
    '“Se proyecta producir 1,200 unidades por mes durante el primer año. Este volumen se calculó considerando la demanda estimada, la capacidad instalada y el programa de producción previsto.” En esta respuesta, el foco es: qué volumen debe producir o atender por periodo.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué volumen debe producir o atender por periodo y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Cómo se programarán lotes, turnos, inventario y recursos?',
    'Para responder sobre cómo se programarán lotes, turnos, inventario y recursos, describa la secuencia de planificación indicando quién es responsable, qué información recibe, cómo organiza los lotes de producción o prestación, los turnos de trabajo, el inventario y la asignación de recursos, y qué resultado genera. Incluya las excepciones relevantes para el plan de producción o prestación.',
    '“El responsable de operaciones revisa el pronóstico de ventas semanal, programa los lotes de producción según la demanda, asigna el personal y los equipos disponibles, verifica el inventario de insumos y genera el cronograma de trabajo. Si existe escasez de materiales, se ajusta el plan y se priorizan los pedidos con fecha de entrega más próxima.” En esta respuesta, el foco es: cómo se programarán lotes, turnos, inventario y recursos.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cómo se programarán lotes, turnos, inventario y recursos y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué cambiará en temporada alta o contingencia?',
    'Para responder sobre qué cambiará en temporada alta o contingencia, describa los ajustes previstos en producción o prestación, personal, turnos, inventarios, recursos o logística para atender incrementos de demanda o situaciones imprevistas. Distinga entre datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Durante la temporada alta se incrementará la producción mediante un turno adicional y un mayor nivel de inventario de insumos. En caso de contingencias, se priorizarán los pedidos pendientes, se reprogramarán las actividades y se utilizarán proveedores alternativos para mantener la continuidad de las operaciones.” En esta respuesta, el foco es: qué cambiará en temporada alta o contingencia.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué cambiará en temporada alta o contingencia y no sustituya este dato con información de otro apartado.'
),
-- Equipos, infraestructura y tecnologia
(
    0,
    '¿Qué equipo e infraestructura necesita cada proceso?',
    'Para responder sobre qué equipo e infraestructura necesita cada proceso, describa cada etapa del proceso e identifique los equipos, instalaciones, herramientas o tecnologías requeridas para su ejecución. Indique quién utiliza cada recurso, cuál es su función dentro del proceso y el resultado que permite obtener. Incluya las excepciones relevantes para equipos, infraestructura y tecnología y distinga entre recursos existentes, propuestos o estimados.',
    '“En la etapa de producción, el operario utiliza una máquina de corte y una mesa de trabajo para elaborar el producto. En la etapa de control de calidad se emplean equipos de medición para verificar el cumplimiento de las especificaciones. Finalmente, en la etapa de despacho se utilizan equipos de embalaje y transporte para preparar la entrega al cliente.” En esta respuesta, el foco es: qué equipo e infraestructura necesita cada proceso.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué equipo e infraestructura necesita cada proceso y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué cantidad, capacidad, especificación y costo requiere?',
    'Para responder sobre qué cantidad, capacidad, especificación y costo requiere, identifique cada equipo, infraestructura o recurso tecnológico necesario e indique su cantidad, capacidad, especificaciones técnicas y costo utilizando una cifra, unidad y periodo cuando corresponda. Distinga si los valores son reales, cotizados, calculados o estimados e indique la fuente o supuesto utilizado en equipos, infraestructura y tecnología.',
    '“Se requiere una máquina de producción con capacidad de 100 unidades por hora, dos computadoras para gestión administrativa y un área de trabajo de 80 m². La máquina tiene un costo cotizado de S/ 18,000 por unidad y las computadoras un costo estimado de S/ 2,500 cada una.” En esta respuesta, el foco es: qué cantidad, capacidad, especificación y costo requiere.',
    'No use cifras sin unidad, periodo y origen. Toda estimación debe identificarse claramente y no presentarse como un dato comprobado. Control específico: verifique que la respuesta trate qué cantidad, capacidad, especificación y costo requiere y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué mantenimiento, vida útil y respaldo tendrá?',
    'Para responder sobre qué mantenimiento, vida útil y respaldo tendrá, describa el mantenimiento previsto para cada equipo o infraestructura, indique su vida útil estimada o confirmada y explique las medidas de respaldo, continuidad o reemplazo en caso de fallas. Distinga entre datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“La maquinaria recibirá mantenimiento preventivo cada seis meses y mantenimiento correctivo cuando sea necesario. Se estima una vida útil de 10 años según las especificaciones del fabricante. Como respaldo, se contará con copias de seguridad de la información, equipos alternativos y proveedores de servicio técnico autorizados.” En esta respuesta, el foco es: qué mantenimiento, vida útil y respaldo tendrá.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué mantenimiento, vida útil y respaldo tendrá y no sustituya este dato con información de otro apartado.'
),
-- Gestion de calidad
(
    0,
    '¿Qué significa calidad en cada etapa?',
    'Para responder qué significa calidad en cada etapa, mencione los elementos indispensables para gestión de calidad y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“La calidad se comprobará mediante especificaciones, entrega puntual, tasa de reclamos y satisfacción del cliente.” En esta respuesta, el foco es: qué significa calidad en cada etapa.',
    'Evite afirmaciones absolutas o promocionales sin comparación y evidencia. Explique cómo se demostrará la diferencia. Control específico: verifique que la respuesta trate qué significa calidad en cada etapa y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿En qué puntos, con qué método y responsable se controlará?',
    'Para precisar en qué puntos, con qué método y responsable se controlará, identifique al actor concreto y explique su función, necesidad o responsabilidad dentro de gestión de calidad. Evite respuestas amplias como “todos” o “el público en general”.',
    '“Para gestión de calidad, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: en qué puntos, con qué método y responsable se controlará.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate en qué puntos, con qué método y responsable se controlará y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Cómo se registrarán y corregirán errores o reclamos?',
    'Para explicar cómo se registrarán y corregirán errores o reclamos, describa la secuencia en orden: quién actúa, qué recibe, qué hace y qué resultado entrega. Incluya las excepciones relevantes para gestión de calidad.',
    '“Para gestión de calidad, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: cómo se registrarán y corregirán errores o reclamos.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cómo se registrarán y corregirán errores o reclamos y no sustituya este dato con información de otro apartado.'
),
-- Compras
(
    0,
    '¿Qué se compra, cuánto y cuándo se repone?',
    'Para responder sobre qué se compra, cuánto y cuándo se repone, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en compras.',
    '“Para compras, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué se compra, cuánto y cuándo se repone.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué se compra, cuánto y cuándo se repone y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Cómo se cotiza, selecciona, aprueba y paga?',
    'Para explicar cómo se cotiza, selecciona, aprueba y paga, describa la secuencia en orden: quién actúa, qué recibe, qué hace y qué resultado entrega. Incluya las excepciones relevantes para compras.',
    '“Para compras, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: cómo se cotiza, selecciona, aprueba y paga.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cómo se cotiza, selecciona, aprueba y paga y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Cómo se recibe y verifica cantidad, calidad y documentos?',
    'Para responder sobre cómo se recibe y verifica cantidad, calidad y documentos, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en compras.',
    '“La calidad se comprobará mediante especificaciones, entrega puntual, tasa de reclamos y satisfacción del cliente.” En esta respuesta, el foco es: cómo se recibe y verifica cantidad, calidad y documentos.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate cómo se recibe y verifica cantidad, calidad y documentos y no sustituya este dato con información de otro apartado.'
),
-- Proveedores operativos
(
    0,
    '¿Qué proveedor abastece cada recurso o servicio?',
    'Para precisar qué proveedor abastece cada recurso o servicio, identifique al actor concreto y explique su función, necesidad o responsabilidad dentro de proveedores operativos. Evite respuestas amplias como “todos” o “el público en general”.',
    '“Ofreceré productos personalizados por pedido, con opciones estándar y premium, mediante atención presencial y digital.” En esta respuesta, el foco es: qué proveedor abastece cada recurso o servicio.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué proveedor abastece cada recurso o servicio y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Cómo se evaluará precio, calidad, plazo y cumplimiento?',
    'Para responder sobre cómo se evaluará precio, calidad, plazo y cumplimiento, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en proveedores operativos.',
    '“El precio preliminar será de S/ 80 por unidad, sujeto a costo, personalización y validación con clientes.” En esta respuesta, el foco es: cómo se evaluará precio, calidad, plazo y cumplimiento.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate cómo se evaluará precio, calidad, plazo y cumplimiento y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué proveedor alternativo existe?',
    'Para precisar qué proveedor alternativo existe, identifique al actor concreto y explique su función, necesidad o responsabilidad dentro de proveedores operativos. Evite respuestas amplias como “todos” o “el público en general”.',
    '“Un proveedor local abastecerá el insumo principal y un servicio externo realizará entregas en horas de alta demanda.” En esta respuesta, el foco es: qué proveedor alternativo existe.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué proveedor alternativo existe y no sustituya este dato con información de otro apartado.'
),
-- Inventarios y almacenamiento
(
    0,
    '¿Qué se almacenará y bajo qué condiciones?',
    'Para responder qué se almacenará y bajo qué condiciones, mencione los elementos indispensables para inventarios y almacenamiento y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para inventarios y almacenamiento, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué se almacenará y bajo qué condiciones.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué se almacenará y bajo qué condiciones y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué stock mínimo, máximo y de seguridad necesita?',
    'Para responder qué stock mínimo, máximo y de seguridad necesita, mencione los elementos indispensables para inventarios y almacenamiento y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para inventarios y almacenamiento, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué stock mínimo, máximo y de seguridad necesita.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué stock mínimo, máximo y de seguridad necesita y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Cómo se registrará, rotará y controlará la merma?',
    'Para explicar cómo se registrará, rotará y controlará la merma, describa la secuencia en orden: quién actúa, qué recibe, qué hace y qué resultado entrega. Incluya las excepciones relevantes para inventarios y almacenamiento.',
    '“Para inventarios y almacenamiento, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: cómo se registrará, rotará y controlará la merma.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cómo se registrará, rotará y controlará la merma y no sustituya este dato con información de otro apartado.'
),
-- Logistica de salida
(
    0,
    '¿Cuándo se considera listo el producto o servicio?',
    'Para responder cuándo se considera listo el producto o servicio, use datos concretos del proyecto vinculados con logística de salida. Si falta información, ValiPlan puede proponer opciones, investigarla con fuentes o dejarla pendiente.',
    '“Ofreceré productos personalizados por pedido, con opciones estándar y premium, mediante atención presencial y digital.” En esta respuesta, el foco es: cuándo se considera listo el producto o servicio.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cuándo se considera listo el producto o servicio y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Cómo se preparará, verificará y despachará?',
    'Para explicar cómo se preparará, verificará y despachará, describa la secuencia en orden: quién actúa, qué recibe, qué hace y qué resultado entrega. Incluya las excepciones relevantes para logística de salida.',
    '“Para logística de salida, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: cómo se preparará, verificará y despachará.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cómo se preparará, verificará y despachará y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Cómo se confirmará la entrega y se resolverán incidencias?',
    'Para explicar cómo se confirmará la entrega y se resolverán incidencias, describa la secuencia en orden: quién actúa, qué recibe, qué hace y qué resultado entrega. Incluya las excepciones relevantes para logística de salida.',
    '“Para logística de salida, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: cómo se confirmará la entrega y se resolverán incidencias.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cómo se confirmará la entrega y se resolverán incidencias y no sustituya este dato con información de otro apartado.'
),
-- Costos operativos y logisticos
(
    0,
    '¿Qué costos genera cada proceso y recurso?',
    'Para responder sobre qué costos genera cada proceso y recurso, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en costos operativos y logísticos.',
    '“El alquiler y la remuneración son costos fijos; los materiales y la entrega varían con cada pedido.” En esta respuesta, el foco es: qué costos genera cada proceso y recurso.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate qué costos genera cada proceso y recurso y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Cómo se calcula el costo por unidad o servicio?',
    'Para responder sobre cómo se calcula el costo por unidad o servicio, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en costos operativos y logísticos.',
    '“Ofreceré productos personalizados por pedido, con opciones estándar y premium, mediante atención presencial y digital.” En esta respuesta, el foco es: cómo se calcula el costo por unidad o servicio.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate cómo se calcula el costo por unidad o servicio y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué mermas, mantenimiento y distribución deben incluirse?',
    'Para responder qué mermas, mantenimiento y distribución deben incluirse, mencione los elementos indispensables para costos operativos y logísticos y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para costos operativos y logísticos, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué mermas, mantenimiento y distribución deben incluirse.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué mermas, mantenimiento y distribución deben incluirse y no sustituya este dato con información de otro apartado.'
),


-- Capitulo VI. Organizacion de la Empresa====================
-- Estructura organizacional
(
    0,
    '¿Qué áreas necesita la empresa al inicio?',
    'Para responder qué áreas necesita la empresa al inicio, mencione los elementos indispensables para estructura organizacional y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para estructura organizacional, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué áreas necesita la empresa al inicio.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué áreas necesita la empresa al inicio y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Quién decide, coordina y reporta a quién?',
    'Para precisar quién decide, coordina y reporta a quién, identifique al actor concreto y explique su función, necesidad o responsabilidad dentro de estructura organizacional. Evite respuestas amplias como “todos” o “el público en general”.',
    '“El cliente principal será una persona adulta de mi ciudad que busca una solución confiable, compara opciones por Internet y valora la puntualidad.” En esta respuesta, el foco es: quién decide, coordina y reporta a quién.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate quién decide, coordina y reporta a quién y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué se terceriza y cómo cambiará al crecer?',
    'Para explicar qué se terceriza y cómo cambiará al crecer, describa la secuencia en orden: quién actúa, qué recibe, qué hace y qué resultado entrega. Incluya las excepciones relevantes para estructura organizacional.',
    '“Para estructura organizacional, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué se terceriza y cómo cambiará al crecer.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué se terceriza y cómo cambiará al crecer y no sustituya este dato con información de otro apartado.'
),

-- Organigrama
(
    0,
    '¿Qué puestos existen desde el inicio y cuáles después?',
    'Para responder qué puestos existen desde el inicio y cuáles después, mencione los elementos indispensables para organigrama y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para organigrama, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué puestos existen desde el inicio y cuáles después.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué puestos existen desde el inicio y cuáles después y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿A quién reporta cada puesto?',
    'Para precisar a quién reporta cada puesto, identifique al actor concreto y explique su función, necesidad o responsabilidad dentro de organigrama. Evite respuestas amplias como “todos” o “el público en general”.',
    '“El cliente principal será una persona adulta de mi ciudad que busca una solución confiable, compara opciones por Internet y valora la puntualidad.” En esta respuesta, el foco es: a quién reporta cada puesto.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate a quién reporta cada puesto y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿El organigrama coincide con funciones y planilla?',
    'Para responder si el organigrama coincide con funciones y planilla, use datos concretos del proyecto vinculados con organigrama. Si falta información, ValiPlan puede proponer opciones, investigarla con fuentes o dejarla pendiente.',
    '“Para organigrama, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: el organigrama coincide con funciones y planilla.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate si el organigrama coincide con funciones y planilla y no sustituya este dato con información de otro apartado.'
),
-- Puestos requeridos
(
    0,
    '¿Qué puestos son indispensables para operar?',
    'Para responder qué puestos son indispensables para operar, mencione los elementos indispensables para puestos requeridos y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para puestos requeridos, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué puestos son indispensables para operar.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué puestos son indispensables para operar y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué cantidad y jornada requiere cada puesto?',
    'Para responder sobre qué cantidad y jornada requiere cada puesto, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en puestos requeridos.',
    '“Para puestos requeridos, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué cantidad y jornada requiere cada puesto.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate qué cantidad y jornada requiere cada puesto y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿En qué etapa se incorporará cada uno?',
    'Para responder en qué etapa se incorporará cada uno, mencione los elementos indispensables para puestos requeridos y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para puestos requeridos, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: en qué etapa se incorporará cada uno.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate en qué etapa se incorporará cada uno y no sustituya este dato con información de otro apartado.'
),
-- Perfiles, funciones y responsabilidades
(
    0,
    '¿Qué propósito y funciones tiene cada puesto?',
    'Para responder qué propósito y funciones tiene cada puesto, mencione los elementos indispensables para perfiles, funciones y responsabilidades y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para perfiles, funciones y responsabilidades, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué propósito y funciones tiene cada puesto.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué propósito y funciones tiene cada puesto y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué estudios, experiencia y competencias necesita?',
    'Para responder qué estudios, experiencia y competencias necesita, mencione los elementos indispensables para perfiles, funciones y responsabilidades y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Las alternativas ofrecen un servicio básico; queda espacio para una opción con seguimiento, personalización y garantía clara.” En esta respuesta, el foco es: qué estudios, experiencia y competencias necesita.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué estudios, experiencia y competencias necesita y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Con qué indicador se evaluará?',
    'Para responder con qué indicador se evaluará, mencione los elementos indispensables para perfiles, funciones y responsabilidades y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Incrementar la recompra del 20 % al 30 % durante el primer año, medida mensualmente por el responsable comercial.” En esta respuesta, el foco es: con qué indicador se evaluará.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate con qué indicador se evaluará y no sustituya este dato con información de otro apartado.'
),
-- Reclutamiento y seleccion
(
    0,
    '¿Cómo encontrará candidatos y qué requisitos filtrará?',
    'Para explicar cómo encontrará candidatos y qué requisitos filtrará, describa la secuencia en orden: quién actúa, qué recibe, qué hace y qué resultado entrega. Incluya las excepciones relevantes para reclutamiento y selección.',
    '“Para reclutamiento y selección, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: cómo encontrará candidatos y qué requisitos filtrará.',
    'Solicite solo datos necesarios y evite estereotipos o características que no influyan en la compra. Proteja información personal. Control específico: verifique que la respuesta trate cómo encontrará candidatos y qué requisitos filtrará y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué entrevista o prueba comprobará competencias?',
    'Para responder qué entrevista o prueba comprobará competencias, mencione los elementos indispensables para reclutamiento y selección y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Las alternativas ofrecen un servicio básico; queda espacio para una opción con seguimiento, personalización y garantía clara.” En esta respuesta, el foco es: qué entrevista o prueba comprobará competencias.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué entrevista o prueba comprobará competencias y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Quién decide y qué documentos se solicitarán?',
    'Para precisar quién decide y qué documentos se solicitarán, identifique al actor concreto y explique su función, necesidad o responsabilidad dentro de reclutamiento y selección. Evite respuestas amplias como “todos” o “el público en general”.',
    '“El cliente principal será una persona adulta de mi ciudad que busca una solución confiable, compara opciones por Internet y valora la puntualidad.” En esta respuesta, el foco es: quién decide y qué documentos se solicitarán.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate quién decide y qué documentos se solicitarán y no sustituya este dato con información de otro apartado.'
),
-- Contratacion e induccion
(
    0,
    '¿Qué modalidad y condiciones se aplicarán?',
    'Para responder qué modalidad y condiciones se aplicarán, mencione los elementos indispensables para contratación e inducción y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para contratación e inducción, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué modalidad y condiciones se aplicarán.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué modalidad y condiciones se aplicarán y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué documentos y obligaciones deben cumplirse?',
    'Para responder qué documentos y obligaciones deben cumplirse, mencione los elementos indispensables para contratación e inducción y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para contratación e inducción, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué documentos y obligaciones deben cumplirse.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué documentos y obligaciones deben cumplirse y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué inducción recibirá el personal?',
    'Para precisar qué inducción recibirá el personal, identifique al actor concreto y explique su función, necesidad o responsabilidad dentro de contratación e inducción. Evite respuestas amplias como “todos” o “el público en general”.',
    '“Para contratación e inducción, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué inducción recibirá el personal.',
    'Solicite solo datos necesarios y evite estereotipos o características que no influyan en la compra. Proteja información personal. Control específico: verifique que la respuesta trate qué inducción recibirá el personal y no sustituya este dato con información de otro apartado.'
),
--  Capacitacion y evaluacion
(
    0,
    '¿Qué conocimientos debe fortalecer cada puesto?',
    'Para responder qué conocimientos debe fortalecer cada puesto, mencione los elementos indispensables para capacitación y evaluación y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para capacitación y evaluación, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué conocimientos debe fortalecer cada puesto.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué conocimientos debe fortalecer cada puesto y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué capacitación, frecuencia, costo y responsable tendrá?',
    'Para responder sobre qué capacitación, frecuencia, costo y responsable tendrá, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en capacitación y evaluación.',
    '“El alquiler y la remuneración son costos fijos; los materiales y la entrega varían con cada pedido.” En esta respuesta, el foco es: qué capacitación, frecuencia, costo y responsable tendrá.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate qué capacitación, frecuencia, costo y responsable tendrá y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Cómo se evaluará aprendizaje y desempeño?',
    'Para explicar cómo se evaluará aprendizaje y desempeño, describa la secuencia en orden: quién actúa, qué recibe, qué hace y qué resultado entrega. Incluya las excepciones relevantes para capacitación y evaluación.',
    '“Para capacitación y evaluación, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: cómo se evaluará aprendizaje y desempeño.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cómo se evaluará aprendizaje y desempeño y no sustituya este dato con información de otro apartado.'
),
-- Motivacion y retencion
(
    0,
    '¿Qué factores motivan al personal?',
    'Para precisar qué factores motivan al personal, identifique al actor concreto y explique su función, necesidad o responsabilidad dentro de motivación y retención. Evite respuestas amplias como “todos” o “el público en general”.',
    '“El motor calculará el resultado con entradas verificadas y mostrará fórmulas, supuestos y escenarios.” En esta respuesta, el foco es: qué factores motivan al personal.',
    'El cálculo debe ejecutarse con fórmulas deterministas y entradas aprobadas. La IA solo explica resultados; no reemplaza el motor financiero. Control específico: verifique que la respuesta trate qué factores motivan al personal y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué reconocimiento, comunicación y desarrollo se aplicarán?',
    'Para responder qué reconocimiento, comunicación y desarrollo se aplicarán, mencione los elementos indispensables para motivación y retención y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para motivación y retención, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué reconocimiento, comunicación y desarrollo se aplicarán.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué reconocimiento, comunicación y desarrollo se aplicarán y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Cómo se medirá clima, productividad y permanencia?',
    'Para explicar cómo se medirá clima, productividad y permanencia, describa la secuencia en orden: quién actúa, qué recibe, qué hace y qué resultado entrega. Incluya las excepciones relevantes para motivación y retención.',
    '“Para motivación y retención, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: cómo se medirá clima, productividad y permanencia.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cómo se medirá clima, productividad y permanencia y no sustituya este dato con información de otro apartado.'
),
-- Planilla y remuneraciones
(
    0,
    '¿Cuántas personas requiere y cuándo ingresan?',
    'Para precisar cuántas personas requiere y cuándo ingresan, identifique al actor concreto y explique su función, necesidad o responsabilidad dentro de planilla y remuneraciones. Evite respuestas amplias como “todos” o “el público en general”.',
    '“Para planilla y remuneraciones, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: cuántas personas requiere y cuándo ingresan.',
    'Solicite solo datos necesarios y evite estereotipos o características que no influyan en la compra. Proteja información personal. Control específico: verifique que la respuesta trate cuántas personas requiere y cuándo ingresan y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué remuneración, beneficios y cargas corresponden?',
    'Para responder qué remuneración, beneficios y cargas corresponden, mencione los elementos indispensables para planilla y remuneraciones y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para planilla y remuneraciones, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué remuneración, beneficios y cargas corresponden.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué remuneración, beneficios y cargas corresponden y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Cuál es el costo mensual y anual?',
    'Para responder sobre cuál es el costo mensual y anual, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en planilla y remuneraciones.',
    '“El alquiler y la remuneración son costos fijos; los materiales y la entrega varían con cada pedido.” En esta respuesta, el foco es: cuál es el costo mensual y anual.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate cuál es el costo mensual y anual y no sustituya este dato con información de otro apartado.'
),
-- Constitucion de la empresa
(
    0,
    '¿Qué forma legal se evalúa y por qué?',
    'Para fundamentar qué forma legal se evalúa y por qué, explique la causa y vínculela con un beneficio, problema o evidencia del proyecto. Si es una opinión, márquela como hipótesis pendiente de validación.',
    '“Para constitución de la empresa, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué forma legal se evalúa y por qué.',
    'Toda afirmación externa debe incluir fuente verificable y fecha. ValiPlan no debe inventar estadísticas, normas ni referencias. Control específico: verifique que la respuesta trate qué forma legal se evalúa y por qué y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Quiénes son socios, qué aportan y cómo deciden?',
    'Para precisar quiénes son socios, qué aportan y cómo deciden, identifique al actor concreto y explique su función, necesidad o responsabilidad dentro de constitución de la empresa. Evite respuestas amplias como “todos” o “el público en general”.',
    '“El cliente principal será una persona adulta de mi ciudad que busca una solución confiable, compara opciones por Internet y valora la puntualidad.” En esta respuesta, el foco es: quiénes son socios, qué aportan y cómo deciden.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate quiénes son socios, qué aportan y cómo deciden y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué trámites, costos y plazos existen?',
    'Para responder sobre qué trámites, costos y plazos existen, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en constitución de la empresa.',
    '“El alquiler y la remuneración son costos fijos; los materiales y la entrega varían con cada pedido.” En esta respuesta, el foco es: qué trámites, costos y plazos existen.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate qué trámites, costos y plazos existen y no sustituya este dato con información de otro apartado.'
),
-- Aspectos legales, laborales y tributarios
(
    0,
    '¿Qué normas sectoriales y contratos afectan la actividad?',
    'Para responder qué normas sectoriales y contratos afectan la actividad, mencione los elementos indispensables para aspectos legales, laborales y tributarios y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para aspectos legales, laborales y tributarios, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué normas sectoriales y contratos afectan la actividad.',
    'Toda afirmación externa debe incluir fuente verificable y fecha. ValiPlan no debe inventar estadísticas, normas ni referencias. Control específico: verifique que la respuesta trate qué normas sectoriales y contratos afectan la actividad y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué obligaciones laborales y tributarias corresponden?',
    'Para responder qué obligaciones laborales y tributarias corresponden, mencione los elementos indispensables para aspectos legales, laborales y tributarios y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para aspectos legales, laborales y tributarios, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué obligaciones laborales y tributarias corresponden.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué obligaciones laborales y tributarias corresponden y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Quién verificará vigencia y cumplimiento?',
    'Para precisar quién verificará vigencia y cumplimiento, identifique al actor concreto y explique su función, necesidad o responsabilidad dentro de aspectos legales, laborales y tributarios. Evite respuestas amplias como “todos” o “el público en general”.',
    '“El cliente principal será una persona adulta de mi ciudad que busca una solución confiable, compara opciones por Internet y valora la puntualidad.” En esta respuesta, el foco es: quién verificará vigencia y cumplimiento.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate quién verificará vigencia y cumplimiento y no sustituya este dato con información de otro apartado.'
),
-- Permisos y licencias
(
    0,
    '¿Qué licencias, registros o certificaciones necesita?',
    'Para responder qué licencias, registros o certificaciones necesita, mencione los elementos indispensables para permisos y licencias y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para permisos y licencias, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué licencias, registros o certificaciones necesita.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué licencias, registros o certificaciones necesita y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué entidad, requisitos, costo, plazo y vigencia tiene cada uno?',
    'Para responder sobre qué entidad, requisitos, costo, plazo y vigencia tiene cada uno, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en permisos y licencias.',
    '“El alquiler y la remuneración son costos fijos; los materiales y la entrega varían con cada pedido.” En esta respuesta, el foco es: qué entidad, requisitos, costo, plazo y vigencia tiene cada uno.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate qué entidad, requisitos, costo, plazo y vigencia tiene cada uno y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué permisos bloquean el inicio y quién los gestionará?',
    'Para precisar qué permisos bloquean el inicio y quién los gestionará, identifique al actor concreto y explique su función, necesidad o responsabilidad dentro de permisos y licencias. Evite respuestas amplias como “todos” o “el público en general”.',
    '“El cliente principal será una persona adulta de mi ciudad que busca una solución confiable, compara opciones por Internet y valora la puntualidad.” En esta respuesta, el foco es: qué permisos bloquean el inicio y quién los gestionará.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué permisos bloquean el inicio y quién los gestionará y no sustituya este dato con información de otro apartado.'
),

-- CAPITULO VII : PLAN FNANCIOER 
-- Supuestos financieros
(
    0,
    '¿Qué horizonte, moneda y periodicidad usará?',
    'Para responder qué horizonte, moneda y periodicidad usará, mencione los elementos indispensables para supuestos financieros y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“El motor calculará el resultado con entradas verificadas y mostrará fórmulas, supuestos y escenarios.” En esta respuesta, el foco es: qué horizonte, moneda y periodicidad usará.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué horizonte, moneda y periodicidad usará y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué supuestos de precios, ventas, costos, inflación e impuestos necesita?',
    'Para responder sobre qué supuestos de precios, ventas, costos, inflación e impuestos necesita, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en supuestos financieros.',
    '“El precio preliminar será de S/ 80 por unidad, sujeto a costo, personalización y validación con clientes.” En esta respuesta, el foco es: qué supuestos de precios, ventas, costos, inflación e impuestos necesita.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate qué supuestos de precios, ventas, costos, inflación e impuestos necesita y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué fuente y confianza tiene cada supuesto?',
    'Para responder qué fuente y confianza tiene cada supuesto, mencione los elementos indispensables para supuestos financieros y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“El motor calculará el resultado con entradas verificadas y mostrará fórmulas, supuestos y escenarios.” En esta respuesta, el foco es: qué fuente y confianza tiene cada supuesto.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué fuente y confianza tiene cada supuesto y no sustituya este dato con información de otro apartado.'
),
-- Plan de inversiones
(
    0,
    '¿Qué activos, intangibles y gastos preoperativos requiere?',
    'Para responder qué activos, intangibles y gastos preoperativos requiere, mencione los elementos indispensables para plan de inversiones y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“El alquiler y la remuneración son costos fijos; los materiales y la entrega varían con cada pedido.” En esta respuesta, el foco es: qué activos, intangibles y gastos preoperativos requiere.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué activos, intangibles y gastos preoperativos requiere y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué cantidad, costo, vida útil y fecha tiene cada uno?',
    'Para responder sobre qué cantidad, costo, vida útil y fecha tiene cada uno, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en plan de inversiones.',
    '“El alquiler y la remuneración son costos fijos; los materiales y la entrega varían con cada pedido.” En esta respuesta, el foco es: qué cantidad, costo, vida útil y fecha tiene cada uno.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate qué cantidad, costo, vida útil y fecha tiene cada uno y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué cotización o fuente respalda el monto?',
    'Para responder sobre qué cotización o fuente respalda el monto, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en plan de inversiones.',
    '“Para plan de inversiones, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué cotización o fuente respalda el monto.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate qué cotización o fuente respalda el monto y no sustituya este dato con información de otro apartado.'
),
-- Capital de trabajo
(
    0,
    '¿Cuánto necesita para operar antes de cobrar suficiente?',
    'Para responder sobre cuánto necesita para operar antes de cobrar suficiente, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en capital de trabajo.',
    '“Para capital de trabajo, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: cuánto necesita para operar antes de cobrar suficiente.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cuánto necesita para operar antes de cobrar suficiente y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué inventario, caja, cobranza y pagos forman el ciclo?',
    'Para responder qué inventario, caja, cobranza y pagos forman el ciclo, mencione los elementos indispensables para capital de trabajo y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para capital de trabajo, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué inventario, caja, cobranza y pagos forman el ciclo.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué inventario, caja, cobranza y pagos forman el ciclo y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Cuántos meses y qué reserva requiere?',
    'Para responder sobre cuántos meses y qué reserva requiere, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en capital de trabajo.',
    '“Para capital de trabajo, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: cuántos meses y qué reserva requiere.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cuántos meses y qué reserva requiere y no sustituya este dato con información de otro apartado.'
),
-- Fuentes de financiamiento
(
    0,
    '¿Cuánto aportarán los socios y cuánto se financiará?',
    'Para responder sobre cuánto aportarán los socios y cuánto se financiará, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en fuentes de financiamiento.',
    '“El motor calculará el resultado con entradas verificadas y mostrará fórmulas, supuestos y escenarios.” En esta respuesta, el foco es: cuánto aportarán los socios y cuánto se financiará.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cuánto aportarán los socios y cuánto se financiará y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué tasa, plazo, gracia, cuota y costos tiene la deuda?',
    'Para responder sobre qué tasa, plazo, gracia, cuota y costos tiene la deuda, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en fuentes de financiamiento.',
    '“El alquiler y la remuneración son costos fijos; los materiales y la entrega varían con cada pedido.” En esta respuesta, el foco es: qué tasa, plazo, gracia, cuota y costos tiene la deuda.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate qué tasa, plazo, gracia, cuota y costos tiene la deuda y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Cómo se comparan alternativas y puede pagarse?',
    'Para explicar cómo se comparan alternativas y puede pagarse, describa la secuencia en orden: quién actúa, qué recibe, qué hace y qué resultado entrega. Incluya las excepciones relevantes para fuentes de financiamiento.',
    '“Las alternativas ofrecen un servicio básico; queda espacio para una opción con seguimiento, personalización y garantía clara.” En esta respuesta, el foco es: cómo se comparan alternativas y puede pagarse.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cómo se comparan alternativas y puede pagarse y no sustituya este dato con información de otro apartado.'
),
-- Estructura de costos
(
    0,
    '¿Qué costos son fijos, variables, directos e indirectos?',
    'Para responder sobre qué costos son fijos, variables, directos e indirectos, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en estructura de costos.',
    '“El alquiler y la remuneración son costos fijos; los materiales y la entrega varían con cada pedido.” En esta respuesta, el foco es: qué costos son fijos, variables, directos e indirectos.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate qué costos son fijos, variables, directos e indirectos y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué gastos comerciales, administrativos y financieros existen?',
    'Para responder qué gastos comerciales, administrativos y financieros existen, mencione los elementos indispensables para estructura de costos y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“El alquiler y la remuneración son costos fijos; los materiales y la entrega varían con cada pedido.” En esta respuesta, el foco es: qué gastos comerciales, administrativos y financieros existen.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué gastos comerciales, administrativos y financieros existen y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué periodo, fuente y regla de asignación se usarán?',
    'Para responder qué periodo, fuente y regla de asignación se usarán, mencione los elementos indispensables para estructura de costos y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para estructura de costos, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué periodo, fuente y regla de asignación se usarán.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué periodo, fuente y regla de asignación se usarán y no sustituya este dato con información de otro apartado.'
),
-- Costos fijos y variables
(
    0,
    '¿Qué costos permanecen aunque no haya ventas?',
    'Para responder sobre qué costos permanecen aunque no haya ventas, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en costos fijos y variables.',
    '“El alquiler y la remuneración son costos fijos; los materiales y la entrega varían con cada pedido.” En esta respuesta, el foco es: qué costos permanecen aunque no haya ventas.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate qué costos permanecen aunque no haya ventas y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué costos cambian con cada unidad o servicio?',
    'Para responder sobre qué costos cambian con cada unidad o servicio, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en costos fijos y variables.',
    '“Ofreceré productos personalizados por pedido, con opciones estándar y premium, mediante atención presencial y digital.” En esta respuesta, el foco es: qué costos cambian con cada unidad o servicio.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate qué costos cambian con cada unidad o servicio y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué comportamiento tendrán al crecer?',
    'Para responder qué comportamiento tendrán al crecer, mencione los elementos indispensables para costos fijos y variables y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para costos fijos y variables, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué comportamiento tendrán al crecer.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué comportamiento tendrán al crecer y no sustituya este dato con información de otro apartado.'
),
-- Costos unitarios
(
    0,
    '¿Qué materiales y mano de obra consume cada unidad?',
    'Para responder qué materiales y mano de obra consume cada unidad, mencione los elementos indispensables para costos unitarios y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para costos unitarios, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué materiales y mano de obra consume cada unidad.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué materiales y mano de obra consume cada unidad y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Cómo se asignarán costos indirectos y merma?',
    'Para responder sobre cómo se asignarán costos indirectos y merma, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en costos unitarios.',
    '“El alquiler y la remuneración son costos fijos; los materiales y la entrega varían con cada pedido.” En esta respuesta, el foco es: cómo se asignarán costos indirectos y merma.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate cómo se asignarán costos indirectos y merma y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué costo unitario resulta a cada volumen?',
    'Para responder sobre qué costo unitario resulta a cada volumen, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en costos unitarios.',
    '“El alquiler y la remuneración son costos fijos; los materiales y la entrega varían con cada pedido.” En esta respuesta, el foco es: qué costo unitario resulta a cada volumen.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate qué costo unitario resulta a cada volumen y no sustituya este dato con información de otro apartado.'
),
-- Presupuesto operativo
(
    0,
    '¿Qué ingresos y costos se proyectan por periodo?',
    'Para responder sobre qué ingresos y costos se proyectan por periodo, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en presupuesto operativo.',
    '“El alquiler y la remuneración son costos fijos; los materiales y la entrega varían con cada pedido.” En esta respuesta, el foco es: qué ingresos y costos se proyectan por periodo.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate qué ingresos y costos se proyectan por periodo y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué gastos cambian por etapa?',
    'Para responder qué gastos cambian por etapa, mencione los elementos indispensables para presupuesto operativo y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“El alquiler y la remuneración son costos fijos; los materiales y la entrega varían con cada pedido.” En esta respuesta, el foco es: qué gastos cambian por etapa.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué gastos cambian por etapa y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué calendario de pagos y cobros se aplicará?',
    'Para responder qué calendario de pagos y cobros se aplicará, mencione los elementos indispensables para presupuesto operativo y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para presupuesto operativo, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué calendario de pagos y cobros se aplicará.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué calendario de pagos y cobros se aplicará y no sustituya este dato con información de otro apartado.'
),
-- Proyeccion de ventas
(
    0,
    '¿Qué unidades y precios corresponden a cada producto y canal?',
    'Para responder sobre qué unidades y precios corresponden a cada producto y canal, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en proyección de ventas.',
    '“Ofreceré productos personalizados por pedido, con opciones estándar y premium, mediante atención presencial y digital.” En esta respuesta, el foco es: qué unidades y precios corresponden a cada producto y canal.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate qué unidades y precios corresponden a cada producto y canal y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué estacionalidad, crecimiento y devoluciones considera?',
    'Para responder qué estacionalidad, crecimiento y devoluciones considera, mencione los elementos indispensables para proyección de ventas y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para proyección de ventas, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué estacionalidad, crecimiento y devoluciones considera.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué estacionalidad, crecimiento y devoluciones considera y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Cómo se conectan demanda, capacidad y venta?',
    'Para explicar cómo se conectan demanda, capacidad y venta, describa la secuencia en orden: quién actúa, qué recibe, qué hace y qué resultado entrega. Incluya las excepciones relevantes para proyección de ventas.',
    '“La proyección parte de 40 ventas mensuales, precio promedio de S/ 80 y crecimiento sujeto a capacidad y evidencia.” En esta respuesta, el foco es: cómo se conectan demanda, capacidad y venta.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate cómo se conectan demanda, capacidad y venta y no sustituya este dato con información de otro apartado.'
),
-- Punto de equilibrio
(
    0,
    '¿Cuál es el precio promedio y costo variable unitario?',
    'Para responder sobre cuál es el precio promedio y costo variable unitario, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en punto de equilibrio.',
    '“El precio preliminar será de S/ 80 por unidad, sujeto a costo, personalización y validación con clientes.” En esta respuesta, el foco es: cuál es el precio promedio y costo variable unitario.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate cuál es el precio promedio y costo variable unitario y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué costos fijos deben cubrirse?',
    'Para responder sobre qué costos fijos deben cubrirse, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en punto de equilibrio.',
    '“El alquiler y la remuneración son costos fijos; los materiales y la entrega varían con cada pedido.” En esta respuesta, el foco es: qué costos fijos deben cubrirse.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate qué costos fijos deben cubrirse y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Cuántas unidades y ventas alcanzan el equilibrio?',
    'Para responder sobre cuántas unidades y ventas alcanzan el equilibrio, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en punto de equilibrio.',
    '“La proyección parte de 40 ventas mensuales, precio promedio de S/ 80 y crecimiento sujeto a capacidad y evidencia.” En esta respuesta, el foco es: cuántas unidades y ventas alcanzan el equilibrio.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate cuántas unidades y ventas alcanzan el equilibrio y no sustituya este dato con información de otro apartado.'
),
-- Estado de resultados proyectado
(
    0,
    '¿Las ventas y costos provienen de presupuestos aprobados?',
    'Para responder sobre si las ventas y costos provienen de presupuestos aprobados, registre una cifra, unidad y periodo. Indique si el valor es real, cotizado, calculado o estimado, y anote la fuente o supuesto utilizado en estado de resultados proyectado.',
    '“El alquiler y la remuneración son costos fijos; los materiales y la entrega varían con cada pedido.” En esta respuesta, el foco es: si las ventas y costos provienen de presupuestos aprobados.',
    'No use cifras sin unidad, periodo y origen. Una estimación debe quedar identificada y no presentarse como dato comprobado. Control específico: verifique que la respuesta trate si las ventas y costos provienen de presupuestos aprobados y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué depreciación, intereses e impuestos corresponden?',
    'Para responder qué depreciación, intereses e impuestos corresponden, mencione los elementos indispensables para estado de resultados proyectado y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“En una prueba de dos semanas, 20 personas solicitaron información, 8 pidieron cotización y 4 realizaron una compra.” En esta respuesta, el foco es: qué depreciación, intereses e impuestos corresponden.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué depreciación, intereses e impuestos corresponden y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué utilidad y márgenes resultan por periodo?',
    'Para responder qué utilidad y márgenes resultan por periodo, mencione los elementos indispensables para estado de resultados proyectado y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para estado de resultados proyectado, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué utilidad y márgenes resultan por periodo.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué utilidad y márgenes resultan por periodo y no sustituya este dato con información de otro apartado.'
),

-- Balance general proyectado
(
    0,
    '¿Qué activos, pasivos y patrimonio existen al cierre?',
    'Para responder qué activos, pasivos y patrimonio existen al cierre, mencione los elementos indispensables para balance general proyectado y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para balance general proyectado, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué activos, pasivos y patrimonio existen al cierre.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué activos, pasivos y patrimonio existen al cierre y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Cómo se conectan caja, inventarios, deuda y utilidad?',
    'Para explicar cómo se conectan caja, inventarios, deuda y utilidad, describa la secuencia en orden: quién actúa, qué recibe, qué hace y qué resultado entrega. Incluya las excepciones relevantes para balance general proyectado.',
    '“Para balance general proyectado, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: cómo se conectan caja, inventarios, deuda y utilidad.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cómo se conectan caja, inventarios, deuda y utilidad y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Se cumple activo igual a pasivo más patrimonio?',
    'Para responder si se cumple activo igual a pasivo más patrimonio, use datos concretos del proyecto vinculados con balance general proyectado. Si falta información, ValiPlan puede proponer opciones, investigarla con fuentes o dejarla pendiente.',
    '“Para balance general proyectado, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: si se cumple activo igual a pasivo más patrimonio.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate si se cumple activo igual a pasivo más patrimonio y no sustituya este dato con información de otro apartado.'
),
-- Flujo de caja
(
    0,
    '¿Qué entradas y salidas reales ocurren por periodo?',
    'Para responder qué entradas y salidas reales ocurren por periodo, mencione los elementos indispensables para flujo de caja y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para flujo de caja, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué entradas y salidas reales ocurren por periodo.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué entradas y salidas reales ocurren por periodo y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Cuándo se desembolsa inversión, deuda y capital de trabajo?',
    'Para responder cuándo se desembolsa inversión, deuda y capital de trabajo, use datos concretos del proyecto vinculados con flujo de caja. Si falta información, ValiPlan puede proponer opciones, investigarla con fuentes o dejarla pendiente.',
    '“Para flujo de caja, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: cuándo se desembolsa inversión, deuda y capital de trabajo.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cuándo se desembolsa inversión, deuda y capital de trabajo y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Existe déficit y cómo se financiará?',
    'Para explicar si existe déficit y cómo se financiará, describa la secuencia en orden: quién actúa, qué recibe, qué hace y qué resultado entrega. Incluya las excepciones relevantes para flujo de caja.',
    '“Para flujo de caja, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: si existe déficit y cómo se financiará.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate si existe déficit y cómo se financiará y no sustituya este dato con información de otro apartado.'
),
-- VAN, TIR y periodo de recuperacion

(
    0,
    '¿Qué flujo y tasa de descuento corresponden?',
    'Para responder qué flujo y tasa de descuento corresponden, mencione los elementos indispensables para VAN, TIR y periodo de recuperación y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“El motor calculará el resultado con entradas verificadas y mostrará fórmulas, supuestos y escenarios.” En esta respuesta, el foco es: qué flujo y tasa de descuento corresponden.',
    'El cálculo debe ejecutarse con fórmulas deterministas y entradas aprobadas. La IA solo explica resultados; no reemplaza el motor financiero. Control específico: verifique que la respuesta trate qué flujo y tasa de descuento corresponden y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué VAN, TIR y recuperación calcula el motor?',
    'Para responder qué VAN, TIR y recuperación calcula el motor, mencione los elementos indispensables para VAN, TIR y periodo de recuperación y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“El motor calculará el resultado con entradas verificadas y mostrará fórmulas, supuestos y escenarios.” En esta respuesta, el foco es: qué VAN, TIR y recuperación calcula el motor.',
    'El cálculo debe ejecutarse con fórmulas deterministas y entradas aprobadas. La IA solo explica resultados; no reemplaza el motor financiero. Control específico: verifique que la respuesta trate qué VAN, TIR y recuperación calcula el motor y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué criterio de decisión y limitaciones se aplican?',
    'Para responder qué criterio de decisión y limitaciones se aplican, mencione los elementos indispensables para VAN, TIR y periodo de recuperación y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para VAN, TIR y periodo de recuperación, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué criterio de decisión y limitaciones se aplican.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué criterio de decisión y limitaciones se aplican y no sustituya este dato con información de otro apartado.'
),
-- Ratios financieros
(
    0,
    '¿Qué ratios son pertinentes al tipo de negocio?',
    'Para responder qué ratios son pertinentes al tipo de negocio, mencione los elementos indispensables para ratios financieros y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“El motor calculará el resultado con entradas verificadas y mostrará fórmulas, supuestos y escenarios.” En esta respuesta, el foco es: qué ratios son pertinentes al tipo de negocio.',
    'El cálculo debe ejecutarse con fórmulas deterministas y entradas aprobadas. La IA solo explica resultados; no reemplaza el motor financiero. Control específico: verifique que la respuesta trate qué ratios son pertinentes al tipo de negocio y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué fórmula y valor presentan por periodo?',
    'Para responder qué fórmula y valor presentan por periodo, mencione los elementos indispensables para ratios financieros y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“El motor calculará el resultado con entradas verificadas y mostrará fórmulas, supuestos y escenarios.” En esta respuesta, el foco es: qué fórmula y valor presentan por periodo.',
    'ValiPlan puede proponer opciones, pero el usuario debe elegir y aprobar. Verifique disponibilidad legal cuando corresponda. Control específico: verifique que la respuesta trate qué fórmula y valor presentan por periodo y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Con qué referencia se comparan y qué decisión generan?',
    'Para responder con qué referencia se comparan y qué decisión generan, mencione los elementos indispensables para ratios financieros y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“El motor calculará el resultado con entradas verificadas y mostrará fórmulas, supuestos y escenarios.” En esta respuesta, el foco es: con qué referencia se comparan y qué decisión generan.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate con qué referencia se comparan y qué decisión generan y no sustituya este dato con información de otro apartado.'
),
-- Analisis de sensibilidad, escenarios y viabilidad
(
    0,
    '¿Qué variables pueden cambiar de manera material?',
    'Para responder qué variables pueden cambiar de manera material, mencione los elementos indispensables para análisis de sensibilidad, escenarios y viabilidad y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para análisis de sensibilidad, escenarios y viabilidad, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué variables pueden cambiar de manera material.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué variables pueden cambiar de manera material y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué escenarios pesimista, probable y optimista se evaluarán?',
    'Para responder qué escenarios pesimista, probable y optimista se evaluarán, mencione los elementos indispensables para análisis de sensibilidad, escenarios y viabilidad y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para análisis de sensibilidad, escenarios y viabilidad, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué escenarios pesimista, probable y optimista se evaluarán.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué escenarios pesimista, probable y optimista se evaluarán y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿En qué punto deja de ser viable y qué conclusión integral corresponde?',
    'Para responder en qué punto deja de ser viable y qué conclusión integral corresponde, mencione los elementos indispensables para análisis de sensibilidad, escenarios y viabilidad y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para análisis de sensibilidad, escenarios y viabilidad, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: en qué punto deja de ser viable y qué conclusión integral corresponde.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate en qué punto deja de ser viable y qué conclusión integral corresponde y no sustituya este dato con información de otro apartado.'
),
-- ========================================================================================================================================================
-- Resumen ejecutivo
(
    0,
    '¿Qué problema, solución y mercado resumen el proyecto?',
    'Para delimitar qué problema, solución y mercado resumen el proyecto, use una descripción genérica o la ubicación confirmada por el usuario: “mi ciudad”, “mi región” o “la zona atendida”. Indique cobertura y restricciones.',
    '“Los clientes pierden tiempo porque las alternativas actuales no ofrecen información clara ni entrega dentro del plazo acordado.” En esta respuesta, el foco es: qué problema, solución y mercado resumen el proyecto.',
    'Toda afirmación externa debe incluir fuente verificable y fecha. ValiPlan no debe inventar estadísticas, normas ni referencias. Control específico: verifique que la respuesta trate qué problema, solución y mercado resumen el proyecto y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué cifras principales muestran operaciones y finanzas?',
    'Para responder qué cifras principales muestran operaciones y finanzas, mencione los elementos indispensables para resumen ejecutivo y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Se aplicará una encuesta breve al segmento definido y entrevistas para comprender razones y objeciones.” En esta respuesta, el foco es: qué cifras principales muestran operaciones y finanzas.',
    'No generalice resultados si la muestra o el método no lo permiten. Registre sesgos, alcance y limitaciones. Control específico: verifique que la respuesta trate qué cifras principales muestran operaciones y finanzas y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué conclusión de viabilidad y riesgos debe conocer el lector?',
    'Para responder qué conclusión de viabilidad y riesgos debe conocer el lector, mencione los elementos indispensables para resumen ejecutivo y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“El principal riesgo es depender de un solo proveedor; se mitigará homologando una segunda alternativa.” En esta respuesta, el foco es: qué conclusión de viabilidad y riesgos debe conocer el lector.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué conclusión de viabilidad y riesgos debe conocer el lector y no sustituya este dato con información de otro apartado.'
),

-- Introduccion
(
    0,
    '¿Qué se investigó y por qué?',
    'Para fundamentar qué se investigó y por qué, explique la causa y vincúlela con un beneficio, problema o evidencia del proyecto. Si es una opinión, márquela como hipótesis pendiente de validación.',
    '“Se aplicará una encuesta breve al segmento definido y entrevistas para comprender razones y objeciones.” En esta respuesta, el foco es: qué se investigó y por qué.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué se investigó y por qué y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué objetivo, método y alcance tuvo el trabajo?',
    'Para delimitar qué objetivo, método y alcance tuvo el trabajo, use una descripción genérica o la ubicación confirmada por el usuario: “mi ciudad”, “mi región” o “la zona atendida”. Indique cobertura y restricciones.',
    '“Para introducción, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué objetivo, método y alcance tuvo el trabajo.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué objetivo, método y alcance tuvo el trabajo y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Cómo se organiza el documento?',
    'Para explicar cómo se organiza el documento, describa la secuencia en orden: quién actúa, qué recibe, qué hace y qué resultado entrega. Incluya las excepciones relevantes para introducción.',
    '“Para introducción, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: cómo se organiza el documento.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate cómo se organiza el documento y no sustituya este dato con información de otro apartado.'
),
-- Conclusiones
(
    0,
    '¿Qué hallazgo responde a cada objetivo?',
    'Para responder qué hallazgo responde a cada objetivo, mencione los elementos indispensables para conclusiones y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para conclusiones, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué hallazgo responde a cada objetivo.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué hallazgo responde a cada objetivo y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué evidencia o cálculo lo sustenta?',
    'Para responder qué evidencia o cálculo lo sustenta, mencione los elementos indispensables para conclusiones y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“En una prueba de dos semanas, 20 personas solicitaron información, 8 pidieron cotización y 4 realizaron una compra.” En esta respuesta, el foco es: qué evidencia o cálculo lo sustenta.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué evidencia o cálculo lo sustenta y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué limitación debe declararse?',
    'Para responder qué limitación debe declararse, mencione los elementos indispensables para conclusiones y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Para conclusiones, se registrará información concreta del proyecto, se indicará su fuente y se validará antes de usarla en el documento final.” En esta respuesta, el foco es: qué limitación debe declararse.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué limitación debe declararse y no sustituya este dato con información de otro apartado.'
),
-- Recomendaciones
(
    0,
    '¿Qué acción se deriva de cada conclusión o riesgo?',
    'Para responder qué acción se deriva de cada conclusión o riesgo, mencione los elementos indispensables para recomendaciones y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“El principal riesgo es depender de un solo proveedor; se mitigará homologando una segunda alternativa.” En esta respuesta, el foco es: qué acción se deriva de cada conclusión o riesgo.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué acción se deriva de cada conclusión o riesgo y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Quién, cuándo y con qué prioridad debe ejecutarla?',
    'Para precisar quién, cuándo y con qué prioridad debe ejecutarla, identifique al actor concreto y explique su función, necesidad o responsabilidad dentro de recomendaciones. Evite respuestas amplias como “todos” o “el público en general”.',
    '“El cliente principal será una persona adulta de mi ciudad que busca una solución confiable, compara opciones por Internet y valora la puntualidad.” En esta respuesta, el foco es: quién, cuándo y con qué prioridad debe ejecutarla.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate quién, cuándo y con qué prioridad debe ejecutarla y no sustituya este dato con información de otro apartado.'
),
(
    0,
    '¿Qué indicador verificará su cumplimiento?',
    'Para responder qué indicador verificará su cumplimiento, mencione los elementos indispensables para recomendaciones y explique brevemente cada uno. Distinga datos confirmados, estimaciones y propuestas de ValiPlan.',
    '“Incrementar la recompra del 20 % al 30 % durante el primer año, medida mensualmente por el responsable comercial.” En esta respuesta, el foco es: qué indicador verificará su cumplimiento.',
    'El ejemplo es solo orientativo. Debe adaptarse a la realidad del proyecto y no debe copiarse como respuesta. Control específico: verifique que la respuesta trate qué indicador verificará su cumplimiento y no sustituya este dato con información de otro apartado.'
);





            


