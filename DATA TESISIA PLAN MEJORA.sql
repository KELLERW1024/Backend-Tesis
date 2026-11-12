


                    INSERT INTO plan_node
                        (id, plan_id, user_plan_id, codigo, nivel, parent_id, titulo, orden, execution_phase,  objective)
                    VALUES

                        -- PLAN DE MEJORA===============================================================
                        -- CAPÍTULO I

                        (129, 2, NULL, '0',      1, NULL, 'Diagnóstico inicial', 0,           0, NULL),
                        (130, 2, NULL, '0',      1, NULL, 'Resumen ejecutivo', 1,             2, NULL),
                        (131, 2, NULL, '0',      1, NULL, 'Introducción', 1,                  2,  NULL),
                         
                        (132, 2, NULL, '1',      1, NULL, 'Capítulo I. Presentación del proyecto de mejora', 1,  1, NULL),
                       
                        (133, 2, NULL, '1.1',   2, 132, 'Situación inicial', 1,1, 'describir la situación observada, el área, los afectados y el periodo'),
                        (134, 2, NULL, '1.2',   2, 132, 'Identificación preliminar del problema', 2,1, 'formular una brecha inicial sin confundir problema, causa y solución'),
                        (135, 2, NULL, '1.3',   2, 132, 'Evidencias iniciales', 3,1, 'inventariar registros, documentos, testimonios y datos disponibles'),
                        (136, 2, NULL, '1.4',   2, 132, 'Justificación del proyecto', 4,1, 'demostrar importancia, urgencia, beneficiarios y costo de no actuar'),
                        (137, 2, NULL, '1.5',   2, 132, 'Alcance y delimitación', 5,1, 'definir sede, área, proceso, periodo, incluidos, excluidos y restricciones'),
                        (138, 2, NULL, '1.6',   2, 132, 'Objetivos preliminares del estudio', 6,1, 'orientar el diagnóstico y los resultados esperados'),

                        -- CAPÍTULO II

                        (139, 2, NULL, '2',     1, NULL, 'Capítulo II. Reseña y Contexto de la Empresa', 2,1, NULL),

                        (140, 2, NULL, '2.1',   2, 139, 'Identificación de la empresa', 1,1, 'registrar datos generales, actividad, ubicación, tamaño y antigüedad'),
                        (141, 2, NULL, '2.2',   2, 139, 'Descripción del negocio', 2,1, 'explicar oferta, clientes, canales, proveedores e ingresos'),
                        (142, 2, NULL, '2.3',   2, 139, 'Direccionamiento estratégico', 3,1, 'vincular misión, visión, valores y objetivos con la mejora'),
                        (143, 2, NULL, '2.4',   2, 139, 'Organización', 4,1, 'analizar áreas, puestos, funciones, autoridad y coordinación'),
                        (144, 2, NULL, '2.5',   2, 139, 'Mapa general de procesos', 5,1, 'ubicar procesos estratégicos, operativos, de apoyo y el proceso afectado'),
                        (145, 2, NULL, '2.6',   2, 139, 'Trayectoria empresarial', 6,1, 'explicar hitos y cambios relacionados con la situación actual'),
                        (146, 2, NULL, '2.7',   2, 139, 'Partes interesadas', 7,1, 'identificar actores, expectativas, influencia y participación'),

                        -- CAPÍTULO III

                        (147, 2, NULL, '3',     1, NULL, 'Capítulo III. Diagnóstico Empresarial', 3,1, NULL),

                        (148, 2, NULL, '3.1',   2, 147, 'Metodología del diagnostico', 1,1, 'definir enfoque, técnicas, fuentes, periodo, muestra, limitaciones y confidencialidad'),
                        (149, 2, NULL, '3.2',   2, 147, 'Instrumentos y validación', 2,1, 'diseñar y validar instrumentos alineados con las variables'),
                        (150, 2, NULL, '3.3',   2, 147, 'Factores políticos y legales', 3,1, 'evaluar normas, permisos y decisiones públicas materiales'),
                        (151, 2, NULL, '3.4',   2, 147, 'Factores económicos', 4,1, 'evaluar variables que afectan costos, demanda y financiamiento'),
                        (152, 2, NULL, '3.5',   2, 147, 'Factores sociales', 5,1, 'evaluar hábitos, cultura, población y expectativas'),
                        (153, 2, NULL, '3.6',   2, 147, 'Factores tecnológicos', 6,1, 'evaluar tecnologías, adopción, integración, seguridad y obsolescencia'),
                        (154, 2, NULL, '3.7',   2, 147, 'Factores ambientales', 7,1, 'evaluar clima, recursos, residuos e impactos'),
                        (155, 2, NULL, '3.8',   2, 147, 'Diagnostico competitivo', 8,1, 'comparar competidores, referentes y buenas prácticas pertinentes'),
                        (156, 2, NULL, '3.9',   2, 147, 'Perspectiva financiera', 9,1, 'medir ventas, costos, rentabilidad, liquidez y perdida asociada'),
                        (157, 2, NULL, '3.10',  2, 147, 'Perspectiva de clientes', 10,1, 'medir satisfacción, reclamos, retención y cumplimiento'),
                        (158, 2, NULL, '3.11',  2, 147, 'Perspectiva de procesos', 11,1, 'medir flujo, tiempos, errores, mermas, controles y cuellos de botella'),
                        (159, 2, NULL, '3.12',  2, 147, 'Perspectiva de capacidades', 12,1, 'evaluar personas, competencias, tecnología, infraestructura y cultura'),
                        (160, 2, NULL, '3.13',  2, 147, 'Diagnostico FODA', 13,1, 'integrar factores internos y externos realmente demostrados'),
                        (161, 2, NULL, '3.14',  2, 147, 'Línea base', 14,1, 'establecer indicadores, formulas, fuentes, responsables y valores actuales'),
                        (162, 2, NULL, '3.15',  2, 147, 'Síntesis del diagnostico', 15,1, 'consolidar hallazgos, impacto, evidencia, prioridad e incertidumbre'),
                        
                        -- CAPÍTULO IV

                        (163, 2, NULL, '4',     1, NULL, 'Capítulo IV. Análisis del Problema y Oportunidad de Mejora', 4,1, NULL),
                        
                        (164, 2, NULL, '4.1',   2, 163, 'Área o proceso critico', 1,1, 'delimitar el proceso de intervención y sus interfaces'),
                        (165, 2, NULL, '4.2',   2, 163, 'Formulación del problema', 2,1, 'definir brecha medible, afectados, periodo y evidencia'),
                        (166, 2, NULL, '4.3',   2, 163, 'Efectos y consecuencias', 3,1, 'cuantificar impactos sobre costo, tiempo, calidad, clientes, personas y riesgo'),
                        (167, 2, NULL, '4.4',   2, 163, 'Diagrama de Ishikawa', 4,1, 'organizar causas posibles por categorías y evidencia'),
                        (168, 2, NULL, '4.5',   2, 163, 'Cinco porques', 5,1, 'profundizar hasta una causa raíz controlable'),
                        (169, 2, NULL, '4.6',   2, 163, 'Análisis de Pareto', 6,1, 'priorizar categorías por frecuencia, costo o impacto'),
                        (170, 2, NULL, '4.7',   2, 163, 'Árbol del problema', 7,1, 'relacionar problema central, causas y efectos'),
                        (171, 2, NULL, '4.8',   2, 163, 'Puntos críticos', 8,1, 'seleccionar causas directas, relevantes y controlables'),
                        (172, 2, NULL, '4.9',   2, 163, 'Matriz de priorización', 9,1, 'ordenar puntos mediante criterios, pesos, escalas y sensibilidad'),
                        (173, 2, NULL, '4.10',  2, 163, 'Oportunidad de mejora', 10,1, 'convertir la brecha en un resultado potencial y condicionado'),
                        (174, 2, NULL, '4.11',  2, 163, 'Criterios de éxito', 11,1, 'definir metas, tolerancias y condiciones de aprobación'),
     
                        -- CAPÍTULO V

                        (175, 2, NULL, '5',     1, NULL, 'Capítulo V. Diseño de la Propuesta de Mejora', 5,1, NULL),
                        
                        (176, 2, NULL, '5.1',   2, 175, 'Justificación de la propuesta', 1,1, 'demostrar relacion entre causas, solución, beneficios y estrategia'),
                        (177, 2, NULL, '5.2',   2, 175, 'Objetivo general de mejora', 2,1, 'definir resultado SMART con línea base, meta, plazo y ámbito'),
                        (178, 2, NULL, '5.3',   2, 175, 'Objetivos específicos', 3,1, 'desglosar resultados vinculados con causas e indicadores'),
                        (179, 2, NULL, '5.4',   2, 175, 'Generación de alternativas', 4,1, 'crear alternativas distintas, incluida la no intervención'),
                        (180, 2, NULL, '5.5',   2, 175, 'Evaluación de alternativas', 5,1, 'comparar efectividad, costo, tiempo, riesgo, sostenibilidad y aceptación'),
                        (181, 2, NULL, '5.6',   2, 175, 'Descripción técnica de la solución', 6,1, 'explicar componentes, usuarios, datos, procesos, reglas y controles'),
                        (182, 2, NULL, '5.7',   2, 175, 'Proceso AS-IS y TO-BE', 7,1, 'comparar proceso actual y futuro con cambios medibles'),
                        (183, 2, NULL, '5.8',   2, 175, 'Estrategia operativa', 8,1, 'definir procedimientos, estándares, roles y controles'),
                        (184, 2, NULL, '5.9',   2, 175, 'Estrategia tecnológica', 9,1, 'definir herramienta, requisitos, integración, seguridad y soporte'),
                        (185, 2, NULL, '5.10',   2, 175, 'Estrategia comercial o de clientes', 10,1, 'mejorar oferta, canal, experiencia o posicionamiento cuando corresponda'),
                        (186, 2, NULL, '5.11',   2, 175, 'Estrategia de personas y capacitación', 11,1, 'cerrar brechas de competencia y conducta'),
                        (187, 2, NULL, '5.12',   2, 175, 'Gestión del cambio', 12,1, 'gestionar patrocinio, resistencia, participación y adopción'),
                        (188, 2, NULL, '5.13',   2, 175, 'Registros y controles', 13,1, 'crear datos, formatos, responsables, frecuencia y conservación'),
                        (189, 2, NULL, '5.14',   2, 175, 'Gestión de riesgos', 14,1, 'valorar probabilidad, impacto, prevención, contingencia y responsable'),
                        (190, 2, NULL, '5.15',   2, 175, 'Factores favorables y desfavorables', 15,1, 'reconocer condiciones que impulsan o dificultan la propuesta'),
                        (191, 2, NULL, '5.16',   2, 175, 'Sostenibilidad de la solución', 16,1, 'asegurar mantenimiento, presupuesto, auditoria y escalamiento'),
       
                        -- CAPÍTULO VI

                        (192, 2, NULL, '6',     1, NULL, 'Capítulo VI. Evaluación Económica y Viabilidad', 6,1, NULL),
       
                        (193, 2, NULL, '6.1',   2, 192, 'Presupuesto de implementacion', 1,1, 'cuantificar inversión, costos recurrentes y contingencia'),
                        (194, 2, NULL, '6.2',   2, 192, 'Costos actuales del problema', 2,1, 'monetizar errores, retrabajos, mermas, penalidades y riesgos'),
                        (195, 2, NULL, '6.3',   2, 192, 'Beneficios esperados', 3,1, 'estimar ahorro, ingreso incremental y beneficios no monetarios'),
                        (196, 2, NULL, '6.4',   2, 192, 'Flujo incremental', 4,1, 'comparar situación sin mejora y con mejora por periodo'),
                        (197, 2, NULL, '6.5',   2, 192, 'Relación beneficio-costo', 5,1, 'comparar beneficios y costos incrementales con criterio correcto'),
                        (198, 2, NULL, '6.6',   2, 192, 'Retorno sobre la inversión', 6,1, 'medir beneficio neto frente a inversión'),
                        (199, 2, NULL, '6.7',   2, 192, 'Periodo de recuperación', 7,1, 'determinar cuándo se recupera la inversión.'),
                        (200, 2, NULL, '6.8',   2, 192, 'VAN y TIR', 8,1, 'evaluar inversiones de varios periodos cuando corresponda'),
                        (201, 2, NULL, '6.9',   2, 192, 'Escenario probable', 9,1, 'establecer valores más plausibles y su evidencia'),
                        (202, 2, NULL, '6.10',   2, 192, 'Escenario optimista', 10,1, 'evaluar mejor caso plausible sin convertirlo en promesa'),
                        (203, 2, NULL, '6.11',   2, 192, 'Escenario pesimista', 11,1, 'evaluar menor adopción, retrasos y sobrecostos.'),
                        (204, 2, NULL, '6.12',   2, 192, 'Análisis de sensibilidad', 12,1, 'identificar variables críticas y umbrales'),
                        (205, 2, NULL, '6.13',   2, 192, 'Viabilidad técnica', 13,1, 'comprobar infraestructura, compatibilidad, capacidad y soporte'),
                        (206, 2, NULL, '6.14',   2, 192, 'Viabilidad operativa y organizacional', 14,1, 'comprobar roles, competencias, carga, cultura y respaldo'),
                        (207, 2, NULL, '6.15',   2, 192, 'Viabilidad integral', 15,1, 'integrar viabilidad económica, legal, ambiental, temporal y organizacional'),
                        
                        -- CAPÍTULO VII

                        (208, 2, NULL, '7',     1, NULL, 'Capítulo VII. Implementación y Control', 7,1, NULL),
       
                        (209, 2, NULL, '7.1',   2, 208, 'Estrategia de implementación', 1,1, 'definir despliegue total, por fases o piloto, dependencias e hitos.'),
                        (210, 2, NULL, '7.2',   2, 208, 'Plan de acción', 2,1, 'traducir estrategias en actividades, entregables, responsables, recursos y costos.'),
                        (211, 2, NULL, '7.3',   2, 208, 'Cronograma Gantt', 3,1, 'secuenciar fechas, duraciones, dependencias, hitos y ruta critica.'),
                        (212, 2, NULL, '7.4',   2, 208, 'Matriz RACI', 4,1, 'aclarar quien ejecuta, aprueba, consulta e informa.'),
                        (213, 2, NULL, '7.5',   2, 208, 'Plan de capacitación', 5,1, 'programar publico, brechas, temas, método, evaluación y costo.'),
                        (214, 2, NULL, '7.6',   2, 208, 'Plan de comunicación', 6,1, 'definir grupos, mensajes, canales, fechas y retroalimentación.'),
                        (215, 2, NULL, '7.7',   2, 208, 'Gestión de resistencia', 7,1, 'identificar resistencias, causas y respuestas.'),
                        (216, 2, NULL, '7.8',   2, 208, 'Diseño del piloto', 8,1, 'definir alcance, duración, participantes, datos, criterios y reversa.'),
                        (217, 2, NULL, '7.9',   2, 208, 'Ejecución y resultados del piloto', 9,1, 'documentar incidencias, resultados, aprendizaje y ajustes.'),
                        (218, 2, NULL, '7.10',   2, 208, 'Sistema de monitoreo', 10,1, 'definir datos, instrumentos, frecuencia, responsable y almacenamiento.'),
                        (219, 2, NULL, '7.11',   2, 208, 'Definición de KPI', 11,1, 'crear ficha completa de indicador y semáforo.'),
                        (220, 2, NULL, '7.12',   2, 208, 'Acciones correctivas', 12,1, 'establecer alertas, análisis, acción, plazo y cierre.'),
                        (221, 2, NULL, '7.13',   2, 208, 'Evaluación antes-después', 13,1, 'comparar periodos equivalentes y atribución razonable.'),
                        (222, 2, NULL, '7.14',   2, 208, 'Beneficios reales y lecciones', 14,1, 'registrar costos, beneficios, desviaciones y aprendizaje reales.'),
                        (223, 2, NULL, '7.15',   2, 208, 'Estandarización', 15,1, 'incorporar procedimientos, formatos, versionado y auditoria.'),
                        (224, 2, NULL, '7.16',   2, 208, 'Sostenibilidad y PHVA', 16,1, 'mantener la mejora mediante planear, hacer, verificar y actuar.'),

                        (225, 2, NULL, '8',      1, NULL, 'Conclusiones', 8,2, NULL),
                        (226, 2, NULL, '9',      1, NULL, 'Recomendaciones', 9,2, NULL);


-- RELACION PLAN SECTION
-- INSERT INTO plan_sections ( plan_id, section_id)
-- VALUES                      ( 1 , 24 ),( 1 , 25 ),( 1 , 26 ),( 1 , 27 ),( 1 , 28 ),( 1 , 29 ),( 1 , 30 ),( 1 , 31 ),( 1 , 32 ),( 1 , 33 ),
--                             ( 2 , 6 ),( 2 , 7 ),( 2 , 8 ),( 2 , 9 ),( 2 , 10 ),( 2 ,11 ),( 2 , 12 ),( 2 , 13 ),
--                             ( 3 , 14 ),( 3 , 15 ),( 3 , 16 ),( 3 , 17 ),( 3 , 18 ),( 3 , 19 ),( 3 , 20 ),( 3 , 21 ),( 3 , 22 ),( 3 , 23 ),
--                             ( 4, 34 ), ( 4, 35 ),( 4, 36 ),( 4, 37 ),( 4, 38 ),( 4, 39 ),( 4, 40 ),( 4, 41 ),( 4, 42 ),( 4, 43 ),
--                             ( 5 , 44 ), ( 5 , 45 ), ( 5 , 46 ), ( 5 , 47 ), ( 5 , 48 ), ( 5 , 49 ), ( 5 , 50 ), ( 5 , 51 ), ( 5 , 52 ),( 5 , 53 ), ( 5 , 54 ); 

