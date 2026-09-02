
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
































-- RELACION PLAN SECTION
-- INSERT INTO plan_sections ( plan_id, section_id)
-- VALUES                      ( 1 , 24 ),( 1 , 25 ),( 1 , 26 ),( 1 , 27 ),( 1 , 28 ),( 1 , 29 ),( 1 , 30 ),( 1 , 31 ),( 1 , 32 ),( 1 , 33 ),
--                             ( 2 , 6 ),( 2 , 7 ),( 2 , 8 ),( 2 , 9 ),( 2 , 10 ),( 2 ,11 ),( 2 , 12 ),( 2 , 13 ),
--                             ( 3 , 14 ),( 3 , 15 ),( 3 , 16 ),( 3 , 17 ),( 3 , 18 ),( 3 , 19 ),( 3 , 20 ),( 3 , 21 ),( 3 , 22 ),( 3 , 23 ),
--                             ( 4, 34 ), ( 4, 35 ),( 4, 36 ),( 4, 37 ),( 4, 38 ),( 4, 39 ),( 4, 40 ),( 4, 41 ),( 4, 42 ),( 4, 43 ),
--                             ( 5 , 44 ), ( 5 , 45 ), ( 5 , 46 ), ( 5 , 47 ), ( 5 , 48 ), ( 5 , 49 ), ( 5 , 50 ), ( 5 , 51 ), ( 5 , 52 ),( 5 , 53 ), ( 5 , 54 ); 



           
            






            


