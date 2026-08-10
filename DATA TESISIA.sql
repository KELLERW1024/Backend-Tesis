
-- ROLES
INSERT INTO roles (name, description) VALUES
('Administrador', 'Administrador del sistema'),
('Usuario', 'Usuario');

INSERT INTO packages ( name, description, duration_months, local_price, international_price, unit_price, benefits , num_plans) 
VALUES
                ( 'ValiPlan Junior', '', 1, 0, 0, 0, 'Acceso básico, a un proyecto, 3 capítulos básicos', 1 ),

                ( 'Emprendedor', '', 3, 97, 26, 97, 'Acceso completo a 1 proyecto, con descarga de documento al completar.', 1 ),
                ( 'Productivo', '', 3, 97, 26, 97, 'Acceso completo a 1 proyecto, con descarga de documento al completar.' , 2),

                ( 'Profesional', '', 5, 349, 94, 349, 'Acceso completo a 1 proyecto tesis , con descarga de documento al completar.', 1 ),
                ( 'Profesional Duo', '', 6, 497, 133, 248.5, 'Acceso completo a 2 proyectos tesis , con descarga de documento al completar.', 2 ),

                ( 'Asesor senior', '', 6, 499, 134, 166, 'Acceso completo a 3 proyecto tesis , con descarga de documento al completar.', 3 ),
                ( 'Asesor ejecutivo', '', 12, 997, 267, 166, 'Acceso completo a 6 proyecto tesis , con descarga de documento al completar.', 6 ),

                ( 'Corporativo Emprende pro', '', 12, 69 , 18, 69, 'Acceso completo de 50 a 100 proyectos tesis , con descarga de documento al completar.', 50 ),
                ( 'Corporativo pro', '', 12, 179 , 48, 179, 'Acceso completo de 50 a 100 proyectos tesis, 2 capacitaciones, reportes por docente,  con descarga de documento al completar.', 100 );




-- PLANES
INSERT INTO plans (name, code,  price, is_active, description)
VALUES
('Tesis Plan de Negocio','PN',  0, TRUE, 'Servicio de elaboración de tesis enfocado en planes de negocio, con estructura académica completa, desarrollo metodológico y soporte de contenido basado en investigación.'),
('Tesis Plan de Mejora','PN',  0, TRUE, 'Servicio de elaboración de tesis enfocado en planes de mejora, con estructura académica completa, desarrollo metodológico y soporte de contenido basado en investigación.'),
('Tesis Plan de Suficiencia Profesional','SP',  0, TRUE, 'Desarrollo de tesis orientada a la experiencia profesional del usuario, incluyendo análisis de actividades laborales, sustento teórico y redacción académica formal.'),
('Tesis Plan de Innovación','PI',  0, TRUE, 'Elaboración de tesis enfocada en proyectos innovadores, con enfoque creativo, tecnológico o de mejora de procesos dentro de una organización.'),
('Plan Negocio Urbano', 'PNU',  0, TRUE, 'Desarrollo integral de planes de negocio orientados a entornos urbanos, considerando análisis de mercado, competencia, viabilidad financiera y estrategias de implementación.'),
('Plan Negocio Rural','PNR', 0, TRUE, 'Elaboración de planes de negocio enfocados en zonas rurales, con análisis de recursos locales, sostenibilidad, impacto comunitario y viabilidad económica.');



INSERT INTO package_plan (package_id, plan_id)
VALUES
    (1, 4),
    (1, 5),
    (2, 4),
    (2, 5),
    (3, 1),
    (3, 2),
    (3, 3),
    (3, 4),
    (3, 5),
    (4, 1),
    (4, 2),
    (4, 3),
    (4, 4),
    (4, 5),
    (5, 1),
    (5, 2),
    (5, 3),
    (5, 4),
    (5, 5),
    (6, 1),
    (6, 2),
    (6, 3),
    (6, 4),
    (6, 5),
    (7, 1),
    (7, 2),
    (7, 3),
    (7, 4),
    (7, 5),
    (8, 1),
    (8, 2),
    (8, 3),
    (8, 4),
    (8, 5),
    (9, 1),
    (9, 2),
    (9, 3),
    (9, 4),
    (9, 5);
-- CAPITULOS
-- INSERT INTO sections (title, description, type , is_active, order_index, objective)
-- VALUES ( 'Indice General', '', 'GE', TRUE, 0 , ''), 
--         ( 'Indice de Tablas', '', 'GE', TRUE, 0 , ''), 
--         ( 'Indice de Figuras', '', 'GE', TRUE , 0, ''), 
--         ( 'Resumen Ejecutivo', '', 'GE', TRUE, 0 , ''), 
--         ( 'Introducción', '', 'GE', TRUE , 0 , ''), 

--         -- Suficiencia profesional
--         ( 'Introducción', '', 'SP', FALSE, 1 , ''), 
--         ( 'Capítulo I', 'Trayectoria Profesional',  'SP', TRUE , 2 , ''), 
--         ( 'Capítulo II', 'Contexto de la Experiencia', 'SP', TRUE, 3 , ''), 
--         ( 'Capítulo III', 'Fundamentos Teóricos',  'SP', TRUE, 4 , ''), 
--         ( 'Capítulo IV', 'Aplicación Profesional', 'SP', TRUE, 5 , ''), 
--         ( 'Capítulo V', 'Análisis Crítico de la Experiencia', 'SP', TRUE , 6 , ''), 
--         ( 'Conlusiones', '', 'SP', FALSE , 7, ''), 
--         ( 'Recomendaciones', '', 'SP', FALSE , 8, ''),

--         -- Proyecto de Innovacion
--         ( 'Introducción', '', 'PI', FALSE, 1 , ''), 
--         ( 'Capítulo I', 'Problema u oportunidad e hipótesis',  'PI', TRUE , 2, 'Objetivo del capítulo: Precisar el problema/oportunidad y formular la hipótesis a validar.'), 
--         ( 'Capítulo II', 'Cliente, insights y validación inicial', 'PI', TRUE , 3, 'Objetivo del capítulo: Entender al usuario y convertir la evidencia en insights accionables.'), 
--         ( 'Capítulo III', 'Diseño de la solución innovadora',  'PI', TRUE , 4, 'Objetivo del capítulo: Desarrollar la propuesta mediante Design Thinking y criterios de deseabilidad.'), 
--         ( 'Capítulo IV', 'Modelo de negocio y prototipo', 'PI', TRUE , 5 , 'Objetivo del capítulo: Traducir la solución a un modelo operable y prototipo de lanzamiento.'), 
--         ( 'Capítulo V', 'Validación de mercado y aprendizaje', 'PI', TRUE, 6 , 'Objetivo del capítulo: Usar experimentos, métricas y Lean Startup para validar o pivotejar.'), 
--         ( 'Capítulo VI', 'Viabilidad operativa y económica', 'PI', TRUE, 7 , 'Objetivo del capítulo: Estimar recursos, costos, ingresos y umbral mínimo de rentabilidad.'), 
--         ( 'Capítulo VII', 'Cierre, decisión y anexos', 'PI', TRUE , 8, 'Objetivo del capítulo: Cerrar con decisión, hoja de ruta y respaldo documental.'),
--         ( 'Conlusiones', '', 'PI', FALSE, 9 , ''), 
--         ( 'Recomendaciones', '', 'PI', FALSE, 10 , ''),

--         -- PLAN de Negocio
--         ( 'Introducción', '', 'PN', FALSE , 1, ''), 
--         ( 'Capítulo I', 'Formulación de la idea y oportunidad de negocio',  'PN', TRUE, 2 , 'Objetivo del capítulo: Precisar la idea, el problema/oportunidad y el modelo inicial del negocio.'), 
--         ( 'Capítulo II', 'Plan estratégico y organizacional',  'PN', TRUE, 3 , 'Objetivo del capítulo: Definir identidad, objetivos, estructura y ventajas estratégicas. Campos esperados: descripcion_empresa, mision, vision, objetivos, foda. '), 
--         ( 'Capítulo III', 'Análisis del entorno, mercado y actores', 'PN', TRUE , 4, 'Objetivo del capítulo: Sustentar el mercado con datos externos y evidencia propia. Campos esperados: pesta, cliente_objetivo, competidores, proveedores, tamano_mercado.'), 
--         ( 'Capítulo IV', 'Plan de Marketing', 'PN', TRUE , 5, 'Objetivo del capítulo: Traducir la propuesta de valor en mezcla comercial y proyección de ventas.'), 
--         ( 'Capítulo V', 'Plan de operaciones','PN', TRUE, 6 , 'Objetivo del capítulo: Diseñar el proceso operativo, logística y capacidad instalada. Campos esperados: proceso, capacidad, logistica_entrada, logistica_salida, recursos.'), 
--         ( 'Capítulo VI', 'Organización legal y gestión del talento','PN', TRUE , 7, 'Objetivo del capítulo: Asegurar coherencia en estructura, personal y formalización. Campos esperados: estructura, talento, formalidad, permisos.'), 
--         ( 'Capítulo VII', 'Plan financiero y evaluación', 'PN', TRUE , 8, 'Objetivo del capítulo: Modelar inversión, resultados, flujo, sensibilidad y decisión de viabilidad.'), 
--         ( 'Conlusiones', '', 'PN', FALSE, 9 , ''), 
--         ( 'Recomendaciones', '', 'PN', FALSE , 10, ''),

--         -- PLAN de Negocio URBANO
--         ( 'Introducción', '', 'PNU', FALSE, 1 , ''), 
--         ( 'Capítulo I', 'Idea de negocio y oportunidad',  'PNU', TRUE, 2 , 'Objetivo del capítulo: Definir con precisión la idea, el problema que atiende y la propuesta de valor inicial.'), 
--         ( 'Capítulo II', 'Cliente, competencia y propuesta de valor',  'PNU', TRUE, 3 , 'Objetivo del capítulo: Delimitar el mercado objetivo, contrastar competidores y fijar la diferenciación real.'), 
--         ( 'Capítulo III', 'Modelo comercial y operación básica', 'PNU', TRUE, 4 , 'Objetivo del capítulo: Diseñar la forma de venta, atención y abastecimiento del negocio.'), 
--         ( 'Capítulo IV', 'Organización y formalización mínima', 'PNU', TRUE, 5 , 'Objetivo del capítulo: Precisar roles, permisos y estructura básica para iniciar de forma ordenada.'), 
--         ( 'Capítulo V', 'Inversión, costos y rentabilidad preliminar','PNU', TRUE, 6 , 'Objetivo del capítulo: Estimar la inversión, costos, punto de equilibrio y utilidad esperada.'), 
--         ( 'Capítulo VI', 'Riesgos, decisión y hoja de ruta','PNU', TRUE , 7, 'Objetivo del capítulo: Cerrar con riesgos, semáforo de viabilidad y plan de 90 días.'), 
--         ( 'Capítulo VII', 'Anexos y evidencias','PNU', TRUE, 8 , 'Objetivo del capítulo: Ordenar las evidencias que sostienen el perfil y su decisión.'), 
--         ( 'Conlusiones', '', 'PNU', FALSE, 9 , ''), 
--         ( 'Recomendaciones', '', 'PNU', FALSE , 10, ''),

--          -- PLAN de Negocio RURAL
--          ( 'Introducción', '', 'PNR', FALSE , 1, ''), 
--         ( 'Capítulo I', 'Idea de negocio y oportunidad territorial',  'PNR', TRUE, 2, 'Objetivo del capítulo: Definir el negocio desde el recurso, el territorio y la oportunidad comercial.'), 
--         ( 'Capítulo II', 'Recurso, oferta productiva y sostenibilidad',  'PNR', TRUE, 3 , 'Objetivo del capítulo: Comprobar disponibilidad, estacionalidad y sostenibilidad del recurso o producto.'), 
--         ( 'Capítulo III', 'Cliente, mercado y cadena comercial', 'PNR', TRUE, 4 , 'Objetivo del capítulo: Precisar quién compra, cómo circula el producto y dónde se captura el margen.'), 
--         ( 'Capítulo IV', 'Modelo de acopio, transformación y comercialización', 'PNR', TRUE, 5 , 'Objetivo del capítulo: Diseñar la ruta práctica desde el origen hasta la venta. Campos esperados: proceso_origen_destino, conservacion, logistica, transformacion.'), 
--         ( 'Capítulo V', 'Organización productiva y formalización','PNR', TRUE, 6 , 'Objetivo del capítulo: Clarificar roles, acuerdos, permisos y forma organizativa.'), 
--         ( 'Capítulo VI', 'Inversión, costos, rentabilidad y flujo preliminar','PNR', TRUE, 7 , 'Objetivo del capítulo: Estimar inversión, costos logísticos, margen y punto de equilibrio.'), 
--         ( 'Capítulo VII', 'Riesgos productivos, comerciales y territoriales','PNR', TRUE , 8, 'Objetivo del capítulo: Evaluar riesgos y cerrar con decisión y hoja de ruta.'), 
--         ( 'Capítulo VII', 'Anexos y evidencias','PNR', TRUE , 9, ''), 
--         ( 'Conlusiones', '', 'PNR', FALSE , 10, ''), 
--         ( 'Recomendaciones', '', 'PNR', FALSE , 11, ''),

--         -- Plan MEJORA
--         ( 'Introducción', '', 'PM', FALSE, 1 , ''), 
--         ( 'Capítulo I', 'Reseña de la empresa y contexto',  'PM', TRUE, 2 , 'Objetivo del capítulo: Entender la empresa actual, su situación y el alcance de la intervención.'), 
--         ( 'Capítulo II', 'Diagnóstico empresarial',  'PM', TRUE, 3, 'Objetivo del capítulo: Analizar entorno, cliente, proceso y desempeño actual.'), 
--         ( 'Capítulo III', 'Problema, causas y oportunidad de mejora',  'PM', TRUE , 4, 'Objetivo del capítulo: Priorizar el problema crítico y convertirlo en oportunidad de mejora.'), 
--         ( 'Capítulo IV', 'Propuesta de mejora',  'PM', TRUE, 5 , 'Objetivo del capítulo: Diseñar estrategias, alternativas y justificación técnica/económica.'), 
--         ( 'Capítulo V', 'Implementación',  'PM', TRUE, 6 , 'Objetivo del capítulo: Bajar la mejora a plan de acción, cronograma, responsables y recursos.'), 
--         ( 'Capítulo VI', 'Monitoreo, indicadores y control', 'PM', TRUE, 7 , 'Objetivo del capítulo: Definir seguimiento, KPIs, metas y puntos de ajuste.'), 
--         ( 'Capítulo VII', 'Cierre, conclusiones y anexos', 'PM', TRUE, 8 , 'Objetivo del capítulo: Cerrar con decisión, beneficios esperados y soporte documental.'),
--         ( 'Conlusiones', '', 'PM', FALSE, 9 , ''), 
--         ( 'Recomendaciones', '', 'PM', FALSE , 10, ''),


--         ( 'Conlusiones', '', 'GE', TRUE , 0, ''), 
--         ( 'Recomendaciones', '', 'GE', TRUE, 0 , '');

INSERT INTO questions_masters (
    question,
    detail,
    example,
    advertencia,
    order_index,
    active
) VALUES
(
    '¿Quién está solicitando el documento?',
    'Seleccione persona emprendedora, estudiante, profesional, empresa, institución, comunidad, docente o asesor.',
    'Soy estudiante y además participo en un negocio familiar.',
    'Puede seleccionar más de un rol.',
    1,
    TRUE
),
(
    '¿Para qué necesita el documento?',
    'Indique si es para decidir, implementar, financiar, titularse, sustentar, mejorar, innovar o presentar a una institución.',
    'Necesito sustentar un proyecto académico y evaluar si puede implementarse.',
    'El propósito determina profundidad y formato.',
    2,
    TRUE
),
(
    '¿Existe una idea nueva, una operación existente, un problema por mejorar, una innovación o una experiencia profesional?',
    'Seleccione alguna de las situaciones de la pregunta y descríbala brevemente.',
    'Existe un negocio en funcionamiento con un proceso que debe mejorarse.',
    'No seleccione una idea nueva si el objetivo es corregir algo existente.',
    3,
    TRUE
),
(
    '¿Qué desea lograr principalmente?',
    'Crear un negocio, validar una idea, mejorar resultados, desarrollar innovación o demostrar experiencia profesional.',
    'Quiero reducir pérdidas de un proceso ya existente.',
    'El resultado principal orienta el producto recomendado.',
    4,
    TRUE
),
(
    '¿La iniciativa ya funciona o genera resultados?',
    'Indique si no existe, está en prueba, tiene primeras ventas, opera regularmente o cuenta con resultados medibles.',
    'Opera desde hace dos años y existen registros de ventas.',
    'No declare funcionamiento si solo existe una propuesta.',
    5,
    TRUE
),
(
    '¿Qué evidencia o información tiene disponible?',
    'Marque entrevistas, registros, ventas, costos, procesos, prototipos, documentos académicos o experiencia laboral.',
    'Tengo registros de costos y evidencias de mi experiencia profesional.',
    'Diferencie disponible, estimado y pendiente.',
    6,
    TRUE
),
(
    '¿Qué nivel de profundidad necesita?',
    'Seleccione perfil inicial, documento completo, tesis/proyecto académico, implementación o financiamiento.',
    'Necesito un documento completo tipo tesis.',
    'Un perfil no sustituye un plan completo.',
    7,
    TRUE
),
(
    '¿Qué plazo, recursos y capacidad tiene para obtener información?',
    'Indique tiempo, presupuesto, acceso a personas, datos, organización y apoyo técnico.',
    'Dispongo de ocho semanas y acceso a registros internos.',
    'La recomendación debe ser realizable.',
    8,
    TRUE
),
(
    '¿El contexto principal es urbano, rural o mixto?',
    'Considere territorio, acceso, actividad, mercado, logística y organización, no solo domicilio.',
    'La producción es rural y la venta se realiza en una ciudad.',
    'Un caso mixto puede activar rutas de ambos contextos.',
    9,
    TRUE
),
(
    '¿Participan familias, comunidades, asociaciones o instituciones facilitadoras?',
    'Indique actores, forma de decisión y acompañamiento.',
    'Participan productores y una municipalidad facilita el proceso.',
    'La participación comunitaria requiere validación colectiva.',
    10,
    TRUE
),
(
    '¿La actividad implica producción, transformación, comercio, servicio, tecnología o combinación?',
    'Seleccione todas las aplicables y señale la principal.',
    'Transformación de fruta y comercialización.',
    'Producción o transformación activa ruta productiva.',
    11,
    TRUE
),
(
    '¿Se busca una solución nueva o significativamente mejorada que debe prototiparse y experimentarse?',
    'Explique novedad, incertidumbre y necesidad de prototipo o MVP.',
    'Se probará una plataforma nueva con usuarios antes de implementarla.',
    'Esto orienta al Plan de Innovación.',
    12,
    TRUE
),
(
    '¿Existe un problema o brecha concreta en algo que ya funciona?',
    'Indique situación actual, resultado insatisfactorio y dato de línea base.',
    'La merma actual es alta y se desea reducirla.',
    'Esto orienta al Plan de Mejora.',
    13,
    TRUE
),
(
    '¿Necesita demostrar competencias a partir de experiencia profesional documentada?',
    'Indique institución, grado, experiencia, intervención y lineamientos.',
    'Debo sustentar una mejora realizada durante mi experiencia laboral.',
    'Esto orienta al Trabajo de Suficiencia Profesional.',
    14,
    TRUE
),
(
    '¿Necesita evaluar integralmente la viabilidad de crear o ampliar un negocio?',
    'Considere mercado, operación, organización, inversión y finanzas.',
    'Necesito presentar un plan completo para buscar financiamiento.',
    'Esto orienta al Plan de Negocio.',
    15,
    TRUE
),
(
    '¿La idea necesita primero una validación sencilla antes de un plan completo?',
    'Indique si aún faltan cliente, precio, costos, proceso o prueba inicial.',
    'Tengo una idea, pero aún no sé si las personas comprarán.',
    'Esto orienta a un Perfil Urbano o Rural.',
    16,
    TRUE
),
(
    '¿Existen requisitos académicos o institucionales obligatorios?',
    'Cargue o describa guía, capítulos, extensión, metodología, formato y fecha.',
    'La institución exige formato APA y capítulos específicos.',
    'La estructura debe respetar reglas confirmadas.',
    17,
    TRUE
),
(
    '¿Hay riesgos o requisitos especiales?',
    'Marque salud, seguridad, población vulnerable, datos, ambiente, permisos, propiedad intelectual o recursos naturales.',
    'El producto requiere validación sanitaria.',
    'Un riesgo bloqueante puede cambiar la ruta o secuencia.',
    18,
    TRUE
),
(
    '¿Qué producto considera que necesita inicialmente?',
    'Seleccione uno de los seis productos o "No estoy seguro".',
    'Creo que necesito un Plan de Innovación, pero deseo que ValiPlan lo verifique.',
    'La preferencia del usuario es una entrada, no la decisión automática.',
    19,
    TRUE
),
(
    'Revise la recomendación de ValiPlan y confirme o corrija la ruta.',
    'Compare producto recomendado, alternativa, razones, brechas y siguiente paso.',
    'Confirmo el Perfil Rural con ruta productiva.',
    'Ninguna ruta se activa sin confirmación del usuario.',
    20,
    TRUE
);


INSERT INTO plan_node  (id, plan_id, user_plan_id, codigo, nivel, parent_id,  titulo, orden, objective)
VALUES
                        -- PLAN DE NEGOCIO
                        (1, 1, NULL, '1',     1, NULL, 'Capítulo I : Plan de Negocio', 1, NULL),

                        (2, 1, NULL, '1.1',   2, 1, 'Formulación de la idea de negocio', 1, 'Definir qué se propone y su lógica básica'),
                        (3, 1, NULL, '1.2',   2, 1, 'Problema o necesidad identificada.', 2, 'Definir el problema desde el cliente y su evidencia'),
                        (4, 1, NULL, '1.3',   2, 1, 'Análisis de la oportunidad.', 3, 'Evaluar demanda, momento y posibilidad de aprovecharla'),
                        (5, 1, NULL, '1.4',   2, 1, 'Descripción del producto o servicio.', 4, 'Precisar oferta, características, beneficios, calidad y requisitos'),
                        (6, 1, NULL, '1.5',   2, 1, 'Propuesta de valor.', 5, 'Explicar por qué el cliente elegiría la oferta'),

                        (7, 1, NULL, '1.6',   2, 1, 'Presentación del modelo de negocio.', 6, NULL),

                        (8, 1, NULL, '1.6.1', 3, 7, 'Segmentos de clientes.', 1, 'Diferenciar grupos y priorizar cliente inicial'),
                        (9, 1, NULL, '1.6.2', 3, 7, 'Propuesta de valor.', 2, NULL),
                        (10,1, NULL, '1.6.3', 3, 7, 'Canales.', 3, 'Definir comunicación, venta, pago, entrega y posventa'),
                        (11,1, NULL, '1.6.4', 3, 7, 'Relaciones con clientes.', 4, 'Definir atención, confianza, fidelización y reclamos'),
                        (12,1, NULL, '1.6.5', 3, 7, 'Fuentes de ingresos.', 5, 'Definir qué se cobra y condiciones'),
                        (13,1, NULL, '1.6.6', 3, 7, 'Recursos clave.', 6, 'Identificar recursos y capacidad'),
                        (14,1, NULL, '1.6.7', 3, 7, 'Actividades clave.', 7, 'Identificar tareas que crean y entregan valor'),
                        (15,1, NULL, '1.6.8', 3, 7, 'Socios clave.', 8, 'Definir proveedores y aliados necesarios'),
                        (16,1, NULL, '1.6.9', 3, 7, 'Estructura de costos.', 9, 'Identificar categorías principales'),

                        -- CAPÍTULO II
                        (17, 1, NULL, '2',     1, NULL, 'Capítulo II. Plan Estratégico y Organizacional', 2, NULL),

                        (18, 1, NULL, '2.1',   2, 17, 'Descripción de la empresa.', 1, 'Presentar identidad y alcance'),
                        (19, 1, NULL, '2.2',   2, 17, 'Datos generales de la empresa.', 2, 'Registrar información formal o propuesta'),

                        (20, 1, NULL, '2.3',   2, 17, 'Factores internos.', 3, NULL),
                        (21, 1, NULL, '2.3.1', 3, 20, 'Fortalezas.', 1, 'Diagnosticar factores internos comprobables'),
                        (22, 1, NULL, '2.3.2', 3, 20, 'Debilidades.', 2, 'Diagnosticar factores internos comprobables'),
                        (23, 1, NULL, '2.3.3', 3, 20, 'Matriz MEFI.', 3, 'Ponderar factores internos'),

                        (24, 1, NULL, '2.4',   2, 17, 'Direccionamiento estratégico.', 4, NULL),
                        (25, 1, NULL, '2.4.1', 3, 24, 'Visión.', 1, 'Definir aspiración futura'),
                        (26, 1, NULL, '2.4.2', 3, 24, 'Misión.', 2, 'Definir propósito actual'),
                        (27, 1, NULL, '2.4.3', 3, 24, 'Valores.', 3, 'Convertir valores en conducta'),
                        (28, 1, NULL, '2.4.4', 3, 24, 'Objetivo general.', 4, NULL),
                        (29, 1, NULL, '2.4.5', 3, 24, 'Objetivos específicos.', 5, NULL),

                        (30, 1, NULL, '2.5',   2, 17, 'Matriz FODA.', 5, NULL),
                        (31, 1, NULL, '2.6',   2, 17, 'Matriz FODA cruzada.', 6, NULL),
                        (32, 1, NULL, '2.7',   2, 17, 'Ventaja competitiva.', 7, NULL),
                        (33, 1, NULL, '2.8',   2, 17, 'Estrategias e indicadores de desempeño.', 8, NULL),

                        -- CAPÍTULO III
                        (34, 1, NULL, '3',     1, NULL, 'Capítulo III. Análisis del Entorno y del Mercado', 3, NULL),

                        (35, 1, NULL, '3.1',   2, 34, 'Análisis PESTEL.', 1, NULL),
                        (36, 1, NULL, '3.1.1', 3, 35, 'Factores políticos y legales.', 1, NULL),
                        (37, 1, NULL, '3.1.2', 3, 35, 'Factores económicos.', 2, NULL),
                        (38, 1, NULL, '3.1.3', 3, 35, 'Factores sociales, culturales y demográficos.', 3, NULL),
                        (39, 1, NULL, '3.1.4', 3, 35, 'Factores tecnológicos.', 4, NULL),
                        (40, 1, NULL, '3.1.5', 3, 35, 'Factores ambientales.', 5, NULL),

                        (41, 1, NULL, '3.2',   2, 34, 'Análisis competitivo.', 2, NULL),
                        (42, 1, NULL, '3.2.1', 3, 41, 'Clientes.', 1, NULL),
                        (43, 1, NULL, '3.2.2', 3, 41, 'Competidores.', 2, NULL),
                        (44, 1, NULL, '3.2.3', 3, 41, 'Proveedores.', 3, NULL),
                        (45, 1, NULL, '3.2.4', 3, 41, 'Productos sustitutos.', 4, NULL),
                        (46, 1, NULL, '3.2.5', 3, 41, 'Nuevos competidores.', 5, NULL),
                        (47, 1, NULL, '3.2.6', 3, 41, 'Cinco Fuerzas de Porter.', 6, NULL),
                        (48, 1, NULL, '3.2.7', 3, 41, 'Matriz MEFE.', 7, NULL),

                        (49, 1, NULL, '3.3',   2, 34, 'Investigación de mercado.', 3, NULL),
                        (50, 1, NULL, '3.3.1', 3, 49, 'Mercado meta.', 1, NULL),
                        (51, 1, NULL, '3.3.2', 3, 49, 'Segmentación.', 2, NULL),
                        (52, 1, NULL, '3.3.3', 3, 49, 'Método de investigación.', 3, NULL),
                        (53, 1, NULL, '3.3.4', 3, 49, 'Población y muestra.', 4, NULL),
                        (54, 1, NULL, '3.3.5', 3, 49, 'Instrumento de recolección.', 5, NULL),
                        (55, 1, NULL, '3.3.6', 3, 49, 'Resultados.', 6, NULL),
                        (56, 1, NULL, '3.3.7', 3, 49, 'Tamaño de mercado.', 7, NULL),
                        (57, 1, NULL, '3.3.8', 3, 49, 'Demanda potencial, disponible, efectiva y objetivo.', 8, NULL),
                        (58, 1, NULL, '3.3.9', 3, 49, 'Proyección de demanda y ventas.', 9, NULL),

                        -- CAPÍTULO IV
                        (59, 1, NULL, '4',      1, NULL, 'Capítulo IV. Plan de Marketing', 4, NULL),

                        (60, 1, NULL, '4.1',    2, 59, 'Producto o servicio.', 1, NULL),
                        (61, 1, NULL, '4.2',    2, 59, 'Precio.', 2, NULL),
                        (62, 1, NULL, '4.3',    2, 59, 'Plaza y canales.', 3, NULL),
                        (63, 1, NULL, '4.4',    2, 59, 'Promoción.', 4, NULL),
                        (64, 1, NULL, '4.5',    2, 59, 'Personas.', 5, NULL),
                        (65, 1, NULL, '4.6',    2, 59, 'Procesos.', 6, NULL),
                        (66, 1, NULL, '4.7',    2, 59, 'Evidencia física, cuando corresponda.', 7, NULL),
                        (67, 1, NULL, '4.8',    2, 59, 'Experiencia del cliente.', 8, NULL),
                        (68, 1, NULL, '4.9',    2, 59, 'Marketing mix integrado.', 9, NULL),
                        (69, 1, NULL, '4.10',   2, 59, 'Programa de acciones tácticas.', 10, NULL),
                        (70, 1, NULL, '4.11',   2, 59, 'Presupuesto de marketing.', 11, NULL),
                        (71, 1, NULL, '4.12',   2, 59, 'Programa y pronóstico de ventas.', 12, NULL),

                        -- CAPÍTULO V
                        (72, 1, NULL, '5',      1, NULL, 'Capítulo V. Plan de Operaciones', 5, NULL),

                        (73, 1, NULL, '5.1',    2, 72, 'Ubicación del negocio.', 1, NULL),
                        (74, 1, NULL, '5.2',    2, 72, 'Producción de bienes o prestación de servicios.', 2, NULL),
                        (75, 1, NULL, '5.3',    2, 72, 'Procesos operativos.', 3, NULL),
                        (76, 1, NULL, '5.4',    2, 72, 'Capacidad instalada y utilizada.', 4, NULL),
                        (77, 1, NULL, '5.5',    2, 72, 'Plan de producción o prestación.', 5, NULL),
                        (78, 1, NULL, '5.6',    2, 72, 'Equipos, infraestructura y tecnología.', 6, NULL),
                        (79, 1, NULL, '5.7',    2, 72, 'Gestión de calidad.', 7, NULL),

                        (80, 1, NULL, '5.8',    2, 72, 'Logística de entrada.', 8, NULL),
                        (81, 1, NULL, '5.8.1',  3, 80, 'Compras.', 1, NULL),
                        (82, 1, NULL, '5.8.2',  3, 80, 'Proveedores.', 2, NULL),
                        (83, 1, NULL, '5.8.3',  3, 80, 'Inventarios.', 3, NULL),
                        (84, 1, NULL, '5.8.4',  3, 80, 'Almacenamiento.', 4, NULL),

                        (85, 1, NULL, '5.9',    2, 72, 'Logística de salida.', 9, NULL),
                        (86, 1, NULL, '5.9.1',  3, 85, 'Producto o servicio terminado.', 1, NULL),
                        (87, 1, NULL, '5.9.2',  3, 85, 'Despacho.', 2, NULL),
                        (88, 1, NULL, '5.9.3',  3, 85, 'Distribución.', 3, NULL),
                        (89, 1, NULL, '5.9.4',  3, 85, 'Entrega al cliente.', 4, NULL),

                        (90, 1, NULL, '5.10',   2, 72, 'Costos operativos y logísticos.', 10, NULL),

                        -- CAPÍTULO VI
                        (91, 1, NULL, '6',      1, NULL, 'Capítulo VI. Organización de la Empresa', 6, NULL),

                        (92, 1, NULL, '6.1',    2, 91, 'Estructura organizacional.', 1, NULL),
                        (93, 1, NULL, '6.2',    2, 91, 'Organigrama.', 2, NULL),
                        (94, 1, NULL, '6.3',    2, 91, 'Puestos requeridos.', 3, NULL),
                        (95, 1, NULL, '6.4',    2, 91, 'Perfiles, funciones y responsabilidades.', 4, NULL),
                        (96, 1, NULL, '6.5',    2, 91, 'Reclutamiento y selección.', 5, NULL),
                        (97, 1, NULL, '6.6',    2, 91, 'Contratación e inducción.', 6, NULL),
                        (98, 1, NULL, '6.7',    2, 91, 'Capacitación y evaluación.', 7, NULL),
                        (99, 1, NULL, '6.8',    2, 91, 'Motivación y retención.', 8, NULL),
                        (100,1, NULL, '6.9',    2, 91, 'Planilla y presupuesto de remuneraciones.', 9, NULL),
                        (101,1, NULL, '6.10',   2, 91, 'Constitución de la empresa.', 10, NULL),
                        (102,1, NULL, '6.11',   2, 91, 'Aspectos legales.', 11, NULL),
                        (103,1, NULL, '6.12',   2, 91, 'Aspectos laborales y tributarios.', 12, NULL),
                        (104,1, NULL, '6.13',   2, 91, 'Permisos y licencias.', 13, NULL),

                        -- CAPÍTULO VII
                       (105,1, NULL, '7',      1, NULL, 'Capítulo VII. Plan Financiero', 7, NULL),

                        (106,1, NULL, '7.1',    2, 105, 'Supuestos financieros.', 1, NULL),
                        (107,1, NULL, '7.2',    2, 105, 'Plan de inversiones.', 2, NULL),
                        (108,1, NULL, '7.3',    2, 105, 'Capital de trabajo.', 3, NULL),
                        (109,1, NULL, '7.4',    2, 105, 'Fuentes de financiamiento.', 4, NULL),
                        (110,1, NULL, '7.5',    2, 105, 'Estructura de costos.', 5, NULL),
                        (111,1, NULL, '7.6',    2, 105, 'Costos fijos y variables.', 6, NULL),
                        (112,1, NULL, '7.7',    2, 105, 'Costos unitarios.', 7, NULL),
                        (113,1, NULL, '7.8',    2, 105, 'Presupuesto operativo.', 8, NULL),
                        (114,1, NULL, '7.9',    2, 105, 'Proyección de ventas.', 9, NULL),
                        (115,1, NULL, '7.10',   2, 105, 'Punto de equilibrio.', 10, NULL),
                        (116,1, NULL, '7.11',   2, 105, 'Estado de resultados proyectado.', 11, NULL),
                        (117,1, NULL, '7.12',   2, 105, 'Balance general proyectado.', 12, NULL),
                        (118,1, NULL, '7.13',   2, 105, 'Flujo de caja.', 13, NULL),
                        (119,1, NULL, '7.14',   2, 105, 'VAN, TIR y periodo de recuperación.', 14, NULL),
                        (120,1, NULL, '7.15',   2, 105, 'Ratios financieros.', 15, NULL),
                        (121,1, NULL, '7.16',   2, 105, 'Análisis de sensibilidad.', 16, NULL),
                        (122,1, NULL, '7.17',   2, 105, 'Escenarios optimista, probable y pesimista.', 17, NULL),
                        (123,1, NULL, '7.18',   2, 105, 'Evaluación de viabilidad.', 18, NULL),

                        (124,1, NULL, '8',      1, NULL, 'Resumen ejecutivo', 8, NULL),
                        (125,1, NULL, '9',      1, NULL, 'Introducción', 8, NULL),
                        (126,1, NULL, '10',      1, NULL, 'Conclusiones', 8, NULL),
                        (127,1, NULL, '11',      1, NULL, 'Recomendaciones', 8, NULL),

                        (128,1, NULL, '0',      1, NULL, 'Diagnóstico inicial', 0, NULL);




                        -- PLAN DE MEJORA===============================================================
                        -- CAPÍTULO I

                        (124, 2, NULL, '1',     1, NULL, 'Capítulo I : Presentación del Proyecto de Mejora', 1, NULL),

                        (125, 2, NULL, '1.1',   2, 124, 'Situación inicial', 1, NULL),

                        (126, 2, NULL, '1.1.1', 3, 125, 'Descripción breve de la situación observada', 1, NULL),
                        (127, 2, NULL, '1.1.2', 3, 125, 'Empresa, área o proceso involucrado', 2, NULL),
                        (128, 2, NULL, '1.1.3', 3, 125, 'Personas o grupos afectados', 3, NULL),
                        (129, 2, NULL, '1.1.4', 3, 125, 'Consecuencias preliminares', 4, NULL),

                        (130, 2, NULL, '1.2',   2, 124, 'Identificación preliminar del problema', 2, NULL),

                        (131, 2, NULL, '1.2.1', 3, 130, 'Problema percibido', 1, NULL),
                        (132, 2, NULL, '1.2.2', 3, 130, 'Evidencias iniciales', 2, NULL),
                        (133, 2, NULL, '1.2.3', 3, 130, 'Frecuencia', 3, NULL),
                        (134, 2, NULL, '1.2.4', 3, 130, 'Alcance', 4, NULL),
                        (135, 2, NULL, '1.2.5', 3, 130, 'Impacto preliminar', 5, NULL),

                        (136, 2, NULL, '1.3',   2, 124, 'Justificación del proyecto', 3, NULL),

                        (137, 2, NULL, '1.3.1', 3, 136, 'Importancia empresarial', 1, NULL),
                        (138, 2, NULL, '1.3.2', 3, 136, 'Beneficiarios', 2, NULL),
                        (139, 2, NULL, '1.3.3', 3, 136, 'Urgencia', 3, NULL),
                        (140, 2, NULL, '1.3.4', 3, 136, 'Consecuencias de no intervenir', 4, NULL),
                        (141, 2, NULL, '1.3.5', 3, 136, 'Relación con los objetivos empresariales', 5, NULL),

                        (142, 2, NULL, '1.4',   2, 124, 'Alcance y delimitación', 4, NULL),

                        (143, 2, NULL, '1.4.1', 3, 142, 'Área', 1, NULL),
                        (144, 2, NULL, '1.4.2', 3, 142, 'Proceso', 2, NULL),
                        (145, 2, NULL, '1.4.3', 3, 142, 'Sede o ubicación', 3, NULL),
                        (146, 2, NULL, '1.4.4', 3, 142, 'Periodo', 4, NULL),
                        (147, 2, NULL, '1.4.5', 3, 142, 'Actividades comprendidas', 5, NULL),
                        (148, 2, NULL, '1.4.6', 3, 142, 'Actividades excluidas', 6, NULL),
                        (149, 2, NULL, '1.4.7', 3, 142, 'Restricciones', 7, NULL),

                        (150, 2, NULL, '1.5',   2, 124, 'Objetivos preliminares del estudio', 5, NULL),

                        (151, 2, NULL, '1.5.1', 3, 150, 'Objetivo del diagnóstico', 1, NULL),
                        (152, 2, NULL, '1.5.2', 3, 150, 'Resultados esperados', 2, NULL),
                        (153, 2, NULL, '1.5.3', 3, 150, 'Indicadores preliminares de impacto', 3, NULL),

                        -- CAPÍTULO II

                        (154, 2, NULL, '2',     1, NULL, 'Capítulo II. Reseña y Contexto de la Empresa', 2, NULL),

                        (155, 2, NULL, '2.1',   2, 154, 'Identificación de la empresa', 1, NULL),
                        (156, 2, NULL, '2.1.1', 3, 155, 'Razón social.', 1, NULL),
                        (157, 2, NULL, '2.1.2', 3, 155, 'Nombre comercial.', 2, NULL),
                        (158, 2, NULL, '2.1.3', 3, 155, 'RUC o registro equivalente.', 3, NULL),
                        (159, 2, NULL, '2.1.4', 3, 155, 'Actividad económica.', 4, NULL),
                        (160, 2, NULL, '2.1.5', 3, 155, 'Ubicación.', 5, NULL),
                        (161, 2, NULL, '2.1.6', 3, 155, 'Forma jurídica.', 6, NULL),
                        (162, 2, NULL, '2.1.7', 3, 155, 'Tamaño.', 7, NULL),
                        (163, 2, NULL, '2.1.8', 3, 155, 'Antigüedad.', 8, NULL),

                        (164, 2, NULL, '2.2',   2, 154, 'Descripción del negocio', 2, NULL),
                        (165, 2, NULL, '2.2.1', 3, 164, 'Productos y servicios.', 1, NULL),
                        (166, 2, NULL, '2.2.2', 3, 164, 'Clientes.', 2, NULL),
                        (167, 2, NULL, '2.2.3', 3, 164, 'Canales.', 3, NULL),
                        (168, 2, NULL, '2.2.4', 3, 164, 'Proveedores.', 4, NULL),
                        (169, 2, NULL, '2.2.5', 3, 164, 'Modelo de negocio resumido.', 5, NULL),
                        (170, 2, NULL, '2.2.6', 3, 164, 'Principales fuentes de ingresos.', 6, NULL),

                        (171, 2, NULL, '2.3',   2, 154, 'Direccionamiento estratégico', 3, NULL),
                        (172, 2, NULL, '2.3.1', 3, 171, 'Misión.', 1, NULL),
                        (173, 2, NULL, '2.3.2', 3, 171, 'Visión.', 2, NULL),
                        (174, 2, NULL, '2.3.3', 3, 171, 'Valores.', 3, NULL),
                        (175, 2, NULL, '2.3.4', 3, 171, 'Objetivos estratégicos.', 4, NULL),
                        (176, 2, NULL, '2.3.5', 3, 171, 'Indicadores empresariales.', 5, NULL),

                        (177, 2, NULL, '2.4',   2, 154, 'Organización', 4, NULL),
                        (178, 2, NULL, '2.4.1', 3, 177, 'Organigrama.', 1, NULL),
                        (179, 2, NULL, '2.4.2', 3, 177, 'Áreas.', 2, NULL),
                        (180, 2, NULL, '2.4.3', 3, 177, 'Puestos relacionados con el problema.', 3, NULL),
                        (181, 2, NULL, '2.4.4', 3, 177, 'Funciones y responsabilidades.', 4, NULL),
                        (182, 2, NULL, '2.4.5', 3, 177, 'Relaciones de coordinación.', 5, NULL),

                        (183, 2, NULL, '2.5',   2, 154, 'Procesos de la empresa', 5, NULL),
                        (184, 2, NULL, '2.5.1', 3, 183, 'Mapa general de procesos.', 1, NULL),
                        (185, 2, NULL, '2.5.2', 3, 183, 'Procesos estratégicos.', 2, NULL),
                        (186, 2, NULL, '2.5.3', 3, 183, 'Procesos operativos.', 3, NULL),
                        (187, 2, NULL, '2.5.4', 3, 183, 'Procesos de apoyo.', 4, NULL),
                        (188, 2, NULL, '2.5.5', 3, 183, 'Proceso o área objeto de mejora.', 5, NULL),

                        (189, 2, NULL, '2.6',   2, 154, 'Trayectoria empresarial', 6, NULL),
                        (190, 2, NULL, '2.6.1', 3, 189, 'Origen.', 1, NULL),
                        (191, 2, NULL, '2.6.2', 3, 189, 'Principales hitos.', 2, NULL),
                        (192, 2, NULL, '2.6.3', 3, 189, 'Cambios importantes.', 3, NULL),
                        (193, 2, NULL, '2.6.4', 3, 189, 'Situación actual.', 4, NULL),

                        -- CAPÍTULO III

                        (194, 2, NULL, '3',      1, NULL, 'Capítulo III. Diagnóstico Empresarial', 3, NULL),

                        (195, 2, NULL, '3.1',    2, 194, 'Metodología del diagnóstico', 1, NULL),
                        (196, 2, NULL, '3.1.1',  3, 195, 'Objetivo del diagnóstico.', 1, NULL),
                        (197, 2, NULL, '3.1.2',  3, 195, 'Enfoque.', 2, NULL),
                        (198, 2, NULL, '3.1.3',  3, 195, 'Técnicas utilizadas.', 3, NULL),
                        (199, 2, NULL, '3.1.4',  3, 195, 'Fuentes internas y externas.', 4, NULL),
                        (200, 2, NULL, '3.1.5',  3, 195, 'Personas consultadas.', 5, NULL),
                        (201, 2, NULL, '3.1.6',  3, 195, 'Instrumentos.', 6, NULL),
                        (202, 2, NULL, '3.1.7',  3, 195, 'Periodo de recolección.', 7, NULL),
                        (203, 2, NULL, '3.1.8',  3, 195, 'Limitaciones.', 8, NULL),
                        (204, 2, NULL, '3.1.9',  3, 195, 'Criterios de confidencialidad.', 9, NULL),

                        (205, 2, NULL, '3.2',    2, 194, 'Diagnóstico del entorno', 2, 'Aplicable solamente cuando el entorno influya materialmente'),
                        (206, 2, NULL, '3.2.1',  3, 205, 'Factores políticos y legales.', 1, NULL),
                        (207, 2, NULL, '3.2.2',  3, 205, 'Factores económicos.', 2, NULL),
                        (208, 2, NULL, '3.2.3',  3, 205, 'Factores sociales.', 3, NULL),
                        (209, 2, NULL, '3.2.4',  3, 205, 'Factores tecnológicos.', 4, NULL),
                        (210, 2, NULL, '3.2.5',  3, 205, 'Factores ambientales.', 5, NULL),
                        (211, 2, NULL, '3.2.6',  3, 205, 'Factores sectoriales.', 6, NULL),
                        (212, 2, NULL, '3.2.7',  3, 205, 'PESTEL resumido.', 7, NULL),

                        (213, 2, NULL, '3.3',    2, 194, 'Diagnóstico competitivo', 3, 'Aplicable a problemas comerciales, de posicionamiento o servicio'),
                        (214, 2, NULL, '3.3.1',  3, 213, 'Competidores.', 1, NULL),
                        (215, 2, NULL, '3.3.2',  3, 213, 'Alternativas o sustitutos.', 2, NULL),
                        (216, 2, NULL, '3.3.3',  3, 213, 'Perfil competitivo.', 3, NULL),
                        (217, 2, NULL, '3.3.4',  3, 213, 'Cinco Fuerzas de Porter.', 4, NULL),
                        (218, 2, NULL, '3.3.5',  3, 213, 'Benchmarking.', 5, NULL),
                        (219, 2, NULL, '3.3.6',  3, 213, 'Buenas prácticas del sector.', 6, NULL),

                        (220, 2, NULL, '3.4',    2, 194, 'Diagnóstico interno', 4, NULL),

                        (221, 2, NULL, '3.4.1',  3, 220, 'Perspectiva financiera', 1, NULL),
                        (222, 2, NULL, '3.4.1.1',4, 221, 'Ventas.', 1, NULL),
                        (223, 2, NULL, '3.4.1.2',4, 221, 'Costos.', 2, NULL),
                        (224, 2, NULL, '3.4.1.3',4, 221, 'Rentabilidad.', 3, NULL),
                        (225, 2, NULL, '3.4.1.4',4, 221, 'Liquidez.', 4, NULL),
                        (226, 2, NULL, '3.4.1.5',4, 221, 'Pérdidas o sobrecostos vinculados al problema.', 5, NULL),

                        (227, 2, NULL, '3.4.2',  3, 220, 'Perspectiva de clientes', 2, NULL),
                        (228, 2, NULL, '3.4.2.1',4, 227, 'Satisfacción.', 1, NULL),
                        (229, 2, NULL, '3.4.2.2',4, 227, 'Reclamos.', 2, NULL),
                        (230, 2, NULL, '3.4.2.3',4, 227, 'Retención.', 3, NULL),
                        (231, 2, NULL, '3.4.2.4',4, 227, 'Posicionamiento.', 4, NULL),
                        (232, 2, NULL, '3.4.2.5',4, 227, 'Experiencia.', 5, NULL),
                        (233, 2, NULL, '3.4.2.6',4, 227, 'Cumplimiento de servicio.', 6, NULL),

                        (234, 2, NULL, '3.4.3',  3, 220, 'Perspectiva de procesos internos', 3, NULL),
                        (235, 2, NULL, '3.4.3.1',4, 234, 'Proceso actual.', 1, NULL),
                        (236, 2, NULL, '3.4.3.2',4, 234, 'Entradas y salidas.', 2, NULL),
                        (237, 2, NULL, '3.4.3.3',4, 234, 'Tiempos.', 3, NULL),
                        (238, 2, NULL, '3.4.3.4',4, 234, 'Errores.', 4, NULL),
                        (239, 2, NULL, '3.4.3.5',4, 234, 'Mermas.', 5, NULL),
                        (240, 2, NULL, '3.4.3.6',4, 234, 'Retrabajos.', 6, NULL),
                        (241, 2, NULL, '3.4.3.7',4, 234, 'Cuellos de botella.', 7, NULL),
                        (242, 2, NULL, '3.4.3.8',4, 234, 'Riesgos.', 8, NULL),
                        (243, 2, NULL, '3.4.3.9',4, 234, 'Controles.', 9, NULL),

                        (244, 2, NULL, '3.4.4',  3, 220, 'Perspectiva de capacidades', 4, NULL),
                        (245, 2, NULL, '3.4.4.1',4, 244, 'Personal.', 1, NULL),
                        (246, 2, NULL, '3.4.4.2',4, 244, 'Competencias.', 2, NULL),
                        (247, 2, NULL, '3.4.4.3',4, 244, 'Tecnología.', 3, NULL),
                        (248, 2, NULL, '3.4.4.4',4, 244, 'Infraestructura.', 4, NULL),
                        (249, 2, NULL, '3.4.4.5',4, 244, 'Información.', 5, NULL),
                        (250, 2, NULL, '3.4.4.6',4, 244, 'Cultura.', 6, NULL),
                        (251, 2, NULL, '3.4.4.7',4, 244, 'Organización.', 7, NULL),

                        (252, 2, NULL, '3.5',    2, 194, 'Herramientas de levantamiento y análisis', 5, 'Seleccionadas según la naturaleza del problema'),
                        (253, 2, NULL, '3.5.1',  3, 252, 'Entrevistas.', 1, NULL),
                        (254, 2, NULL, '3.5.2',  3, 252, 'Encuestas.', 2, NULL),
                        (255, 2, NULL, '3.5.3',  3, 252, 'Observación.', 3, NULL),
                        (256, 2, NULL, '3.5.4',  3, 252, 'Revisión documental.', 4, NULL),
                        (257, 2, NULL, '3.5.5',  3, 252, 'Ficha de inspección.', 5, NULL),
                        (258, 2, NULL, '3.5.6',  3, 252, 'Medición de tiempos.', 6, NULL),
                        (259, 2, NULL, '3.5.7',  3, 252, 'SIPOC.', 7, NULL),
                        (260, 2, NULL, '3.5.8',  3, 252, 'Flujograma.', 8, NULL),
                        (261, 2, NULL, '3.5.9',  3, 252, 'Diagrama de funciones cruzadas.', 9, NULL),
                        (262, 2, NULL, '3.5.10', 3, 252, 'Mapa de experiencia.', 10, NULL),
                        (263, 2, NULL, '3.5.11', 3, 252, 'Análisis de datos históricos.', 11, NULL),
                        (264, 2, NULL, '3.5.12', 3, 252, 'Benchmarking.', 12, NULL),
                        (265, 2, NULL, '3.5.13', 3, 252, 'Matriz FODA.', 13, NULL),
                        (266, 2, NULL, '3.5.14', 3, 252, 'MEFI y MEFE, cuando sean verdaderamente necesarias.', 14, NULL),

                        (267, 2, NULL, '3.6',    2, 194, 'Línea base', 6, NULL),
                        (268, 2, NULL, '3.6.1',  3, 267, 'Indicador.', 1, NULL),
                        (269, 2, NULL, '3.6.2',  3, 267, 'Fórmula.', 2, NULL),
                        (270, 2, NULL, '3.6.3',  3, 267, 'Unidad.', 3, NULL),
                        (271, 2, NULL, '3.6.4',  3, 267, 'Fuente.', 4, NULL),
                        (272, 2, NULL, '3.6.5',  3, 267, 'Periodo.', 5, NULL),
                        (273, 2, NULL, '3.6.6',  3, 267, 'Valor actual.', 6, NULL),
                        (274, 2, NULL, '3.6.7',  3, 267, 'Frecuencia.', 7, NULL),
                        (275, 2, NULL, '3.6.8',  3, 267, 'Responsable.', 8, NULL),
                        (276, 2, NULL, '3.6.9',  3, 267, 'Calidad del dato.', 9, NULL),

                        -- CAPÍTULO IV

                        (277, 2, NULL, '4',      1, NULL, 'Capítulo IV. Análisis del Problema y Oportunidad de Mejora', 4, NULL),

                        (278, 2, NULL, '4.1',    2, 277, 'Identificación del área o proceso crítico', 1, NULL),
                        (279, 2, NULL, '4.1.1',  3, 278, 'Área evaluada.', 1, NULL),
                        (280, 2, NULL, '4.1.2',  3, 278, 'Proceso afectado.', 2, NULL),
                        (281, 2, NULL, '4.1.3',  3, 278, 'Relación con otros procesos.', 3, NULL),
                        (282, 2, NULL, '4.1.4',  3, 278, 'Personas involucradas.', 4, NULL),

                        (283, 2, NULL, '4.2',    2, 277, 'Formulación del problema', 2, NULL),
                        (284, 2, NULL, '4.2.1',  3, 283, 'Situación actual.', 1, NULL),
                        (285, 2, NULL, '4.2.2',  3, 283, 'Situación esperada.', 2, NULL),
                        (286, 2, NULL, '4.2.3',  3, 283, 'Brecha.', 3, NULL),
                        (287, 2, NULL, '4.2.4',  3, 283, 'Magnitud.', 4, NULL),
                        (288, 2, NULL, '4.2.5',  3, 283, 'Frecuencia.', 5, NULL),
                        (289, 2, NULL, '4.2.6',  3, 283, 'Alcance.', 6, NULL),
                        (290, 2, NULL, '4.2.7',  3, 283, 'Evidencia.', 7, NULL),

                        (291, 2, NULL, '4.3',    2, 277, 'Efectos y consecuencias', 3, NULL),
                        (292, 2, NULL, '4.3.1',  3, 291, 'Costos.', 1, NULL),
                        (293, 2, NULL, '4.3.2',  3, 291, 'Pérdidas.', 2, NULL),
                        (294, 2, NULL, '4.3.3',  3, 291, 'Tiempos.', 3, NULL),
                        (295, 2, NULL, '4.3.4',  3, 291, 'Calidad.', 4, NULL),
                        (296, 2, NULL, '4.3.5',  3, 291, 'Clientes.', 5, NULL),
                        (297, 2, NULL, '4.3.6',  3, 291, 'Personal.', 6, NULL),
                        (298, 2, NULL, '4.3.7',  3, 291, 'Riesgos.', 7, NULL),
                        (299, 2, NULL, '4.3.8',  3, 291, 'Cumplimiento legal.', 8, NULL),
                        (300, 2, NULL, '4.3.9',  3, 291, 'Imagen institucional.', 9, NULL),

                        (301, 2, NULL, '4.4',    2, 277, 'Análisis de causas', 4, 'ValiPlan seleccionará las herramientas más pertinentes'),
                        (302, 2, NULL, '4.4.1',  3, 301, 'Ishikawa.', 1, NULL),
                        (303, 2, NULL, '4.4.2',  3, 301, 'Cinco porqués.', 2, NULL),
                        (304, 2, NULL, '4.4.3',  3, 301, 'Árbol del problema.', 3, NULL),
                        (305, 2, NULL, '4.4.4',  3, 301, 'Pareto.', 4, NULL),
                        (306, 2, NULL, '4.4.5',  3, 301, 'Análisis de fallas.', 5, NULL),
                        (307, 2, NULL, '4.4.6',  3, 301, 'Diagrama de dispersión.', 6, NULL),
                        (308, 2, NULL, '4.4.7',  3, 301, 'Mapa causal.', 7, NULL),
                        (309, 2, NULL, '4.4.8',  3, 301, 'Análisis de restricciones.', 8, NULL),
                        (310, 2, NULL, '4.4.9',  3, 301, 'Análisis de riesgos.', 9, NULL),

                        (311, 2, NULL, '4.5',    2, 277, 'Identificación de puntos críticos', 5, NULL),
                        (312, 2, NULL, '4.5.1',  3, 311, 'Causas controlables.', 1, NULL),
                        (313, 2, NULL, '4.5.2',  3, 311, 'Causas no controlables.', 2, NULL),
                        (314, 2, NULL, '4.5.3',  3, 311, 'Causas directas.', 3, NULL),
                        (315, 2, NULL, '4.5.4',  3, 311, 'Causas secundarias.', 4, NULL),
                        (316, 2, NULL, '4.5.5',  3, 311, 'Puntos críticos de intervención.', 5, NULL),

                        (317, 2, NULL, '4.6',    2, 277, 'Priorización', 6, 'Matriz con criterios como'),
                        (318, 2, NULL, '4.6.1',  3, 317, 'Magnitud del impacto.', 1, NULL),
                        (319, 2, NULL, '4.6.2',  3, 317, 'Urgencia.', 2, NULL),
                        (320, 2, NULL, '4.6.3',  3, 317, 'Frecuencia.', 3, NULL),
                        (321, 2, NULL, '4.6.4',  3, 317, 'Factibilidad.', 4, NULL),
                        (322, 2, NULL, '4.6.5',  3, 317, 'Costo.', 5, NULL),
                        (323, 2, NULL, '4.6.6',  3, 317, 'Tiempo.', 6, NULL),
                        (324, 2, NULL, '4.6.7',  3, 317, 'Capacidad de medición.', 7, NULL),
                        (325, 2, NULL, '4.6.8',  3, 317, 'Riesgo.', 8, NULL),
                        (326, 2, NULL, '4.6.9',  3, 317, 'Alineamiento estratégico.', 9, NULL),

                        (327, 2, NULL, '4.7',    2, 277, 'Formulación de la oportunidad de mejora', 7, NULL),
                        (328, 2, NULL, '4.7.1',  3, 327, 'Brecha priorizada.', 1, NULL),
                        (329, 2, NULL, '4.7.2',  3, 327, 'Resultado potencial.', 2, NULL),
                        (330, 2, NULL, '4.7.3',  3, 327, 'Beneficiarios.', 3, NULL),
                        (331, 2, NULL, '4.7.4',  3, 327, 'Límites.', 4, NULL),
                        (332, 2, NULL, '4.7.5',  3, 327, 'Condiciones necesarias.', 5, NULL),
                        (333, 2, NULL, '4.7.6',  3, 327, 'Hipótesis de mejora.', 6, NULL),































-- RELACION PLAN SECTION
-- INSERT INTO plan_sections ( plan_id, section_id)
-- VALUES                      ( 1 , 24 ),( 1 , 25 ),( 1 , 26 ),( 1 , 27 ),( 1 , 28 ),( 1 , 29 ),( 1 , 30 ),( 1 , 31 ),( 1 , 32 ),( 1 , 33 ),
--                             ( 2 , 6 ),( 2 , 7 ),( 2 , 8 ),( 2 , 9 ),( 2 , 10 ),( 2 ,11 ),( 2 , 12 ),( 2 , 13 ),
--                             ( 3 , 14 ),( 3 , 15 ),( 3 , 16 ),( 3 , 17 ),( 3 , 18 ),( 3 , 19 ),( 3 , 20 ),( 3 , 21 ),( 3 , 22 ),( 3 , 23 ),
--                             ( 4, 34 ), ( 4, 35 ),( 4, 36 ),( 4, 37 ),( 4, 38 ),( 4, 39 ),( 4, 40 ),( 4, 41 ),( 4, 42 ),( 4, 43 ),
--                             ( 5 , 44 ), ( 5 , 45 ), ( 5 , 46 ), ( 5 , 47 ), ( 5 , 48 ), ( 5 , 49 ), ( 5 , 50 ), ( 5 , 51 ), ( 5 , 52 ),( 5 , 53 ), ( 5 , 54 ); 



           
            






            


