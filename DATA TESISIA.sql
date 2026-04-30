
INSERT INTO questions (section_id , question_text, question_detail,  evidencia_detail , validation_detail , apa_detail ) -- SP
VALUES      ( 7,  '¿Cuál fue tu cargo o rol más relevante?', 'Identifica el puesto que mejor demuestra competencias de tu carrera.', 'Contrato o constancia opcional', 'Mínimo 5 caracteres.', 'Dato primario: no cita.' ), 
            ( 7,  '¿En qué empresa o institución trabajaste y durante qué periodo?', 'Debe incluir nombre de entidad y rango temporal claro.' , 'Certificado de trabajo', 'Debe contener nombre y periodo.', 'Dato primario: no cita.'), 
            ( 7,  'Describe tus funciones principales (máximo 5).', 'Resume responsabilidades medulares y repetidas del puesto.'  , 'MOF o descripción de puesto', 'Mínimo 3 funciones.', 'Dato primario: no cita.'), 
            ( 7,  '¿Qué logros concretos obtuviste?', 'Prioriza resultados con cifras o mejoras observables.'  , 'Reportes, indicadores', 'Al menos 1 logro verificable.', 'Dato primario: no cita.'), 
            ( 7,  '¿Qué competencias desarrollaste o fortaleciste?', 'Incluye técnicas y blandas.'  , 'Certificados opcionales', 'Mínimo 3 competencias.', 'Dato primario: no cita.'), 
            ( 7,  '¿Cómo conecta esta experiencia con tu TSP?', 'Explica por qué esta vivencia permite abordar el problema elegido', 'No aplica', 'Mínimo 20 caracteres.', 'Dato primario: no cita.'  ), 

            ( 8,  'Nombre de la empresa o institución.', 'Identificación exacta de la entidad '  , 'RUC/ficha empresa', 'Mínimo 5 caracteres.', 'Dato primario; si se completa con Sunat, citar fuente.'), 
            ( 8,  '¿A qué rubro pertenece?', 'Sector económico o actividad principal.'  , 'No aplica', 'Mínimo 3 caracteres.', 'Dato primario.'), 
            ( 8,  'Describe la empresa: ubicación, tamaño, servicios y posicionamiento.', 'Construye el contexto operativo de la experiencia.', 'Web, Google Maps, brochure', 'Mínimo 40 caracteres.', 'Si se toman datos públicos, citar fuente.'  ), 
            ( 8,  '¿Cuál era tu rol específico dentro de la entidad?', 'Vincula puesto con área y alcance.'  , 'Carta de funciones', 'Mínimo 5 caracteres.', 'Dato primario.'), 
            ( 8,  '¿A quién reportabas y qué decisiones podías tomar?', 'Aclara jerarquía y nivel de autonomía.'  , 'Organigrama opcional', 'Debe mencionar superior o área.', 'Dato primario.'), 
            ( 8,  'Describe la situación problemática que enfrentaste.', 'Debe identificar qué ocurrió, cuándo y a quién afectó.' , 'Fotos, correos, reportes', 'Mínimo 60 caracteres.', 'Dato primario; si usa evidencia externa, citar.' ), 
            ( 8,  'Formula el objetivo general de tu intervención.', 'Debe iniciar con verbo de acción y apuntar al problema principal.' , 'No aplica', 'Mínimo 15 caracteres.', 'Dato primario.' ), 
            ( 8,  'Enumera tres objetivos específicos.', 'Descompón el objetivo general en acciones medibles.' , 'No aplica', 'Mínimo 3 objetivos.', 'Dato primario.' ), 
            ( 8,  '¿Qué metas cuantitativas te propusiste?', 'Define indicadores esperados.' , 'Línea base o reportes', 'Al menos una meta cuantificable.', 'Dato primario.' ), 
            ( 8,  '¿Qué limitaciones aparecieron?', 'Registra barreras internas o externas que condicionaron el proceso.' , 'No aplica', 'Mínimo 20 caracteres.', 'Dato primario.' ), 

            ( 9,  'Menciona al menos dos antecedentes relacionados con tu problema.', 'Pueden ser tesis, artículos, informes o casos comparables.' , 'PDF, enlace o ficha bibliográfica', 'Mínimo 2 antecedentes.', 'Fuente externa: requiere cita y referencia.' ), 
            ( 9,  '¿Qué teorías, modelos o autores respaldan tu enfoque?', 'Selecciona marcos realmente útiles para interpretar tu caso.' , 'Libros, artículos', 'Mínimo 2 autores/modelos.', 'Fuente externa: requiere cita y referencia.' ), 
            ( 9,  '¿Qué elementos del entorno son relevantes?', 'Incluye factores económicos, sociales, tecnológicos o legales.' , 'Informes sectoriales, leyes', 'Mínimo 2 fuentes externas.', 'Fuente externa: requiere cita y referencia.' ), 

            ( 10,  '¿Cómo se organizó el equipo de trabajo?', 'Describe roles, coordinación y dependencia funcional.' , 'Diagrama o esquema', 'Mínimo 3 roles si aplica.', 'Si se presenta como organigrama, tratar como figura.' ), 
            ( 10,  'Presenta el cronograma de actividades.', 'Ordena las acciones por semanas o meses.' , 'Cronograma en Excel o imagen', 'Mínimo 3 actividades.', 'Tabla APA con nota si corresponde.' ), 
            ( 10,  'Describe cómo ejecutaste cada actividad.', 'Explica método, procedimiento y alcance.'  , 'Registros, actas', 'Mínimo 100 caracteres por actividad principal.', 'Dato primario.'), 
            ( 10,  'Adjunta evidencias de ejecución.', 'Sube fotos, capturas, actas o soportes.' , 'Imágenes, PDF', 'Al menos 1 evidencia si el TSP lo requiere.', 'Anexos no van a referencias salvo cita expresa.' ), 
            ( 10,  '¿Qué resultados obtuviste en cada actividad?', 'Relaciona actividad con resultado o indicador.' , 'Reportes de resultados', 'Mínimo 1 resultado por actividad clave.', 'Si se tabula, aplicar formato APA.' ), 
            ( 10,  '¿Hubo ajustes o reprocesos?', 'Documenta correcciones sobre la marcha.' , 'No aplica', 'Responder "No" si no hubo.', 'Dato primario.' ), 


            ( 11,  '¿En qué medida se alcanzaron los objetivos y metas?', 'Compara lo logrado con lo planificado.' , 'No aplica', 'Mínimo 30 caracteres.', 'Dato primario; puede apoyarse en tablas propias.' ), 
            ( 11,  '¿Por qué se superaron o no los resultados esperados?', 'Analiza factores internos y externos.' , 'No aplica', 'Mínimo 50 caracteres.', 'Dato primario.' ), 
            ( 11,  '¿Qué aspectos de la cultura u organización influyeron?', 'Profundiza en causas institucionales.' , 'No aplica', 'Mínimo 30 caracteres.', 'Dato primario.' ), 
            ( 11,  '¿Qué lecciones aprendiste?', 'Extrae aprendizajes transferibles.' , 'No aplica', 'Mínimo 3 lecciones.', 'Dato primario.'  ), 

            -- proyecto de innovacion
            ( 15,  '¿Cuál es el problema o la oportunidad que da origen al proyecto?', 'Definir el punto de partida innovador.', 'Problema observado, oportunidad emergente o vacío en el mercado.', 'Debe ser específico y verificable.', 'Citar toda afirmación externa.'  ), 
            ( 15,  '¿Qué hipótesis principal quieres validar?', 'Forzar una afirmación comprobable.', 'Hipótesis cliente-problema o solución-valor.', 'Una hipótesis clara y medible.', 'Sin cita salvo soporte teórico.'  ), 
            ( 15,  '¿Qué segmento de cliente presenta con mayor fuerza este problema?', 'Delimitar el foco de validación.', 'Segmento y contexto de uso.', 'Debe ser específico.', 'Sin cita.'  ), 

            ( 16,  '¿Qué evidencias iniciales tienes del problema del usuario?', 'Evitar innovación sin usuario.', 'Entrevistas, observación, encuesta, quejas, comentarios, shadowing.', 'Debe incluir evidencia directa.', 'Dato primario del usuario; presentarlo con tabla o figura APA cuando corresponda.'  ), 
            ( 16,  '¿Cómo describirías al usuario objetivo?', 'Construir persona o arquetipo.', 'Datos, metas, frustraciones, motivaciones, comportamientos.', 'Debe incluir dolor y expectativa.', 'Sin cita salvo apoyo externo.'  ), 
            ( 16,  '¿Qué insight clave surge del análisis del usuario?', 'Convertir evidencia en aprendizaje.', 'Insight redactado en lenguaje claro.', '1 a 3 insights potentes.', 'Sin cita.'  ), 

            ( 17,  '¿Cómo formularás el reto creativo?', 'Traducir el problema a desafío de diseño.', '¿Cómo podríamos...?', 'Debe enfocarse en una solución deseable.', 'Sin cita.'  ), 
            ( 17,  '¿Qué ideas de solución generaste y por qué una fue priorizada?', 'Mostrar pensamiento divergente y convergente.', 'Lista de ideas y criterio de selección.', 'Mínimo 3 ideas si aplica.', 'Sin cita.'  ), 
            ( 17,  '¿Cómo es la solución innovadora elegida?', 'Definir atributos innovadores.', 'Producto, servicio, experiencia o proceso innovador.', 'Debe explicar novedad y valor.', 'Si usa autor/modelo de innovación, citarlo.'  ),

            ( 18,  '¿Cómo se expresa la solución en un modelo de negocio?', 'Pasar de idea a modelo sostenible.', 'Segmentos, propuesta de valor, canales, ingresos, socios, costos.', 'Debe estar completo.', 'Sin cita salvo modelo teórico usado.'  ), 
            ( 18,  '¿Qué prototipo mínimo construirás o simularás?', 'Aterrizar la solución.', 'Mockup, landing, demo, video, piloto, muestra, servicio simulado.', 'Debe ser rápido de probar.', 'Sin cita.'  ), 
            ( 18,  '¿En qué contexto real será probado el prototipo?', 'Asegurar realismo de validación.', 'Lugar, usuario, momento, canal.', 'Debe ser ejecutable.', 'Sin cita.'  ),

            ( 19,  '¿Qué experimento de validación ejecutarás?', 'Aplicar lógica Lean Startup.', 'Prueba, métrica, duración, tamaño de muestra.', 'Debe incluir criterio de éxito.', 'Sin cita salvo marco teórico Lean Startup.'  ), 
            ( 19,  '¿Qué métricas indicarían validación o rechazo?', 'Evitar validación subjetiva.', 'Conversión, intención de compra, repetición, tiempo, satisfacción, adopción, etc.', '2 a 5 métricas clave.', 'Sin cita.'  ), 
            ( 19,  '¿Qué harás si la hipótesis no se confirma?', 'Abrir espacio a pivoteo.', 'Perseverar, ajustar, cambiar segmento, cambiar canal, pausar.', 'Una decisión contingente clara.', 'Sin cita.'  ), 

            ( 20,  '¿Qué recursos y costos mínimos exige la solución?', 'Cuantificar viabilidad inicial.', 'Equipo, insumos, software, marketing, prueba piloto, talento.', 'Debe cubrir el MVP.', 'Anexos de soporte; sin cita bibliográfica estándar.'  ), 
            ( 20,  '¿Cómo capturarías ingresos si el experimento funciona?', 'Explorar monetización.', 'Precio, suscripción, comisión, paquete, licencia, etc.', 'Modelo de ingreso coherente.', 'Sin cita.'  ), 
            ( 20,  '¿Qué mínimo resultado económico o de tracción justificaría avanzar?', 'Fijar criterio de decisión.', 'Ventas mínimas, usuarios, margen, retención o interés.', 'Medible y temporal.', 'Sin cita.'  ), 

            ( 21,  '¿La solución debe avanzar, ajustarse o detenerse?', 'Cerrar con una decisión explícita.', 'Semáforo y razones.', 'Una decisión y 3 razones.', 'Sin cita.'  ), 
            ( 21,  '¿Cuál es la hoja de ruta de 30, 60 y 90 días?', 'Conectar el proyecto con ejecución.', 'Hitos, responsables y entregables.', 'Tres tramos temporales.', 'Sin cita.'  ), 
            ( 21,  '¿Qué anexos respaldan el proyecto?', 'Reunir evidencia de diseño y validación.', 'Entrevistas, encuesta, storyboard, prototipo, métricas, capturas, cotizaciones.', 'Mínimo 4 anexos.', 'Cada anexo con título y nota de origen; citar solo cuando corresponda.'  ), 

            -- PLAN NEGOCIO  =======>>>>>>>> section_index , question_text, question_detail,  evidencia_detail , validation_detail , apa_detail
            (25, '¿Cuál es la idea de negocio y qué necesidad concreta atiende?' ,'Definir el negocio en términos claros y defendibles.', 'Descripción de la idea y problema/oportunidad.' , 'Máx. 150 palabras; sin vaguedades.', 'Sin cita salvo que el problema se apoye en fuente externa.'), 
            (25, '¿Cómo funcionará el negocio en términos simples?' ,'Esbozar el modelo de negocio preliminar.', 'Cliente, oferta, ingreso, canal, recurso clave.' , 'Debe cubrir cliente, propuesta, ingreso y canal.', 'Sin cita; construcción estratégica.'), 
            (25, '¿Qué evidencia muestra que existe oportunidad de mercado?' ,'Evitar un negocio basado solo en intuición.', 'Demanda insatisfecha, tendencia, brecha local, observación, encuesta.' , 'Debe apoyarse en evidencia mínima.', 'Toda afirmación externa requiere cita; lo propio va como dato del usuario.'), 
            (25, '¿Qué problema del cliente o del mercado resuelve el negocio?' ,'Conectar el negocio con dolor real del cliente.', 'Problema principal, consecuencias, urgencia.' , 'Debe ser específico.', 'Si se apoya en fuente, citarla.'), 

            (26, '¿Cómo se describirá la empresa y su alcance?' ,'Definir rubro, escala, ubicación y foco.', 'Nombre tentativo, ubicación, rubro, tamaño, modalidad.' , 'Debe ser consistente con la idea.', 'Sin cita.'), 
            (26, '¿Cuál será la misión de la empresa?' ,'Expresar propósito y servicio.', 'Propuesta breve.' , 'Máx. 40 palabras.', 'Sin cita.'), 
            (26, '¿Cuál será la visión?' ,'Expresar aspiración futura.', 'Propuesta breve.' , 'Debe ser aspiracional y realista.', 'Sin cita.'), 
            (26, '¿Qué objetivos generales y específicos tendrá el negocio?' ,'Traducir la estrategia en metas.', 'Objetivos medibles y cronológicos.' , 'Al menos 1 general y 3 específicos.', 'Sin cita.'), 
            (26, '¿Cuáles son tus fortalezas, oportunidades, debilidades y amenazas?' ,'Construir la base estratégica.', 'Matriz FODA y lectura cruzada.' , 'Debe incluir 4 componentes completos.', 'Si las oportunidades/amenazas usan fuente externa, citarla en el análisis.'), 

            (27, '¿Qué factores PESTA influyen en tu negocio?' ,'Analizar el entorno macro.', 'Político, económico, social, tecnológico y ambiental.' , 'Al menos 1 hallazgo útil por factor.', 'Todo dato externo con cita APA.'), 
            (27, '¿Quién es tu cliente objetivo y cómo se comporta?' ,'Perfilar al mercado meta.', 'Datos demográficos, psicográficos y de compra.' , 'Debe incluir segmentación accionable.', 'Citar fuente externa cuando se cuantifique mercado.'), 
            (27, '¿Quiénes son tus competidores y cómo se comparan contigo?' ,'Ubicar competencia directa e indirecta.', 'Nombre, precio, propuesta, ubicación, ventaja/desventaja.' , 'Mínimo 3 competidores si existen.', 'Si se usa Google Maps o páginas web, consignar nota de fuente/captura.'), 
            (27, '¿Quiénes serán tus proveedores y qué riesgo implica depender de ellos?' ,'Entender abastecimiento.', 'Proveedor, condiciones, tiempos, sustitutos.' , 'Mínimo 2 proveedores si aplica.', 'Sin referencia bibliográfica salvo fuente pública especial.'), 
            (27, '¿Cómo estimarás el tamaño de mercado?' ,'Cuantificar mercado potencial, objetivo y meta de captación.', 'Población base, segmento, supuestos y cálculo.' , 'Debe separar dato oficial de supuestos.', 'Cita obligatoria para bases poblacionales y económicas; supuestos del usuario declarados por separado.'), 

            (28, '¿Cómo será el producto o servicio y qué atributos lo diferencian?' ,'Definir la oferta concreta.', 'Características, presentación, beneficios, empaque, garantía.' , 'Debe vincularse a cliente y competencia.', 'Sin cita salvo norma técnica o regulación.'), 
            (28, '¿Cómo fijarás el precio?' ,'Sustentar la lógica de monetización.', 'Precio, margen, referencia competitiva y percepción del cliente.' , 'Debe explicar criterio usado.', 'Si usa precios observados externos, nota metodológica; si usa boletines públicos, cita.'), 
            (28, '¿Cómo llegará el producto al cliente?' ,'Definir canales y cobertura.', 'Canal, frecuencia, reparto, delivery, presencia física.' , 'Debe ser coherente con operaciones.', 'Sin cita.'), 
            (28, '¿Qué acciones promocionales ejecutarás?' ,'Traducir estrategia a tácticas.', 'Redes, pauta, activación, volanteo, convenios, degustación.' , 'Debe incluir acción, costo y objetivo.', 'Sin cita.'), 
            (28, '¿Cómo proyectarás tus ventas?' ,'Construir el ingreso esperado.', 'Supuesto de demanda, frecuencia, ticket, días y estacionalidad.' , 'Supuestos explícitos.', 'Sin cita; proyección del usuario.'), 

            (29, '¿Cuál es el proceso operativo de principio a fin?' ,'Diseñar la operación real.', 'Secuencia de actividades.' , 'Mínimo 5 etapas si aplica.', 'Sin cita; puede presentarse como figura elaborada por la plataforma.'), 
            (29, '¿Cuál es la capacidad instalada o de atención?' ,'Medir el techo operativo.', 'Unidades/hora, día o mes.' , 'Consistente con recursos.', 'Sin cita.'), 
            (29, '¿Cómo comprarás, recibirás y almacenarás?' ,'Definir logística de entrada.', 'Frecuencia, proveedor, almacenamiento, reposición.' , 'Debe incluir control básico.', 'Sin cita.'), 
            (29, '¿Cómo despacharás o entregarás?' ,'Definir logística de salida.', 'Despacho, entrega, tiempos, devoluciones, mermas.' , 'Debe ser coherente con canal.', 'Sin cita.'), 
            (29, '¿Qué equipos, mobiliario y tecnología necesitas?' ,'Cuantificar requerimientos operativos.', 'Lista de activos y herramientas.' , 'Debe vincularse con inversión.', 'Anexar cotizaciones; no requieren cita bibliográfica salvo fuente externa especial.'), 

            (30, '¿Qué estructura organizacional tendrá la empresa?' ,'Definir forma de trabajo.', 'Roles, jerarquía y funciones.' , 'Mínimo roles clave.', 'Sin cita.'), 
            (30, '¿Qué perfiles de puesto necesitas?' ,'Alinear talento con operación.', 'Puesto, funciones, horario, remuneración.' , 'Debe cubrir personal clave.', 'Sin cita.'), 
            (30, '¿Qué forma legal y tributaria usarás y por qué?' ,'Asegurar viabilidad formal.', 'Persona natural, EIRL, SRL, MYPE, etc.' , 'Debe ser congruente con escala.', 'Citar fuente oficial si se explica requisito o régimen.'), 
            (30, '¿Qué permisos y registros necesitas?' ,'Evitar bloqueo normativo.', 'Municipal, sanitario, sectorial, marca, laboral, etc.' , 'Estado actual y pendiente.', 'Cita oficial cuando se mencione la exigencia.'), 

            (31, '¿Cuál es la inversión total del proyecto?' ,'Construir el punto de partida financiero.', 'Activos, intangibles y capital de trabajo.' , 'Debe sumar total y fuente de financiamiento.', 'Anexos de cotización; sin cita bibliográfica salvo fuente pública.'), 
            (31, '¿Cómo quedaría el estado de resultados proyectado?' ,'Medir rentabilidad operativa.', 'Ingresos, costos, gastos, utilidad.' , 'Escenario mínimo anual.', 'Sin cita; es modelo del proyecto.'), 
            (31, '¿Cómo se comportará el flujo de caja?' ,'Medir liquidez y recuperación.', 'Entradas, salidas, saldo.' , 'Horizonte definido (mensual o anual).', 'Sin cita.'), 
            (31, '¿Qué muestran VAN, TIR, B/C o indicadores elegidos?' ,'Cerrar la evaluación financiera.', 'Indicadores y lectura ejecutiva.' , 'Aplicar cuando el nivel del trabajo lo exija.', 'Sin cita; cálculo propio del proyecto.'), 
            (31, '¿Qué pasa si venden menos o suben los costos?' ,'Evaluar robustez del negocio.', 'Escenarios pobre/base/mejor.' , 'Mínimo 2 escenarios si aplica.', 'Sin cita.'), 

            -- ============>>>>>>>>>>>>>>> PLAN NEGOCIO URBANO
            (35, '¿Qué producto o servicio ofrecerás en una frase clara?', 'Definir el núcleo del negocio.', 'Descripción breve del negocio.' ,'1 oración, sin ambigüedad.' , 'Sin cita; rotular como dato del usuario.'), 
            (35, '¿Qué problema resuelve o qué necesidad atiende?', 'Justificar la existencia del negocio.', 'Problema u oportunidad concreta.' ,'Debe ser específico y observable.' , 'Si se apoya en fuente externa, citarla; si no, tratar como dato primario.'), 
            (35, '¿Quién sería tu cliente principal?', 'Delimitar el segmento inicial.', 'Edad, estilo de vida, ubicación, capacidad de pago.' ,'Debe incluir segmento, zona y criterio de compra.' , 'Si se usan datos poblacionales, citar INEI/APEIM.'), 
            (35, '¿Cómo surgió la idea de negocio?', 'Capturar el origen y el insight inicial.', 'Historia breve, observación o experiencia.' ,'Máx. 120 palabras.' , 'Sin cita salvo que mencione estudio externo.'), 
            (35, '¿Cuál es tu propuesta de valor en una frase?', 'Fijar la promesa central del negocio.', 'Beneficio diferencial resumido.' ,'Una frase concreta y orientada al cliente.' , 'Sin cita; dato de construcción estratégica.'), 

            (36, '¿Qué características demográficas y de comportamiento tiene tu cliente objetivo?', 'Perfilar al consumidor con mayor detalle.', 'Edad, frecuencia de compra, sensibilidad al precio, canal preferido.' ,'Debe incluir al menos 4 rasgos accionables.' , 'Si se cuantifica tamaño de segmento, citar fuente externa y separar supuestos del usuario.'), 
            (36, '¿Quiénes son tus 3 competidores directos y dónde están ubicados?', 'Aterrizar la competencia real de proximidad.', 'Nombre comercial, dirección o referencia.' ,'Tres competidores como mínimo.' , 'Competidores observados por el usuario: sin cita; si se usa Google Maps, consignar nota de fuente/cartografía.'), 
            (36, '¿Qué hacen mejor que tú tus competidores?', 'Reconocer ventajas ajenas y evitar sesgo.', 'Precio, rapidez, ubicación, reputación, experiencia.' ,'Mínimo 2 fortalezas comparativas.' , 'Sin cita; dato primario del usuario.'), 
            (36, '¿Qué vacíos dejan tus competidores que tu negocio puede aprovechar?', 'Encontrar la brecha competitiva.', 'Problemas del competidor observables por el cliente.' ,'Mínimo 2 brechas concretas.' , 'Sin cita; dato primario.'), 
            (36, '¿Qué atributo será el principal motivo de elección del cliente?', 'Definir el eje de posicionamiento.', 'Precio, sabor, rapidez, limpieza, experiencia, cercanía, etc.' ,'Elegir 1 atributo dominante y justificarlo.' , 'Si la justificación usa encuesta, tratar como dato primario y presentar tabla/figura APA.'), 

            (37, '¿Dónde venderás y por qué ese punto es conveniente?', 'Evaluar la localización del negocio.', 'Dirección, tránsito, cercanía de clientes, alquiler.' ,'Debe incluir ubicación y argumento comercial.' , 'Si se inserta mapa o captura, tratar como figura con nota de elaboración propia o fuente.'), 
            (37, '¿Por qué canales venderás?', 'Diseñar el modelo comercial.', 'Local, WhatsApp, delivery, redes, marketplaces.' ,'Mínimo un canal principal y uno complementario.' , 'Sin cita.'), 
            (37, '¿Cuánto tiempo toma atender un pedido?', 'Medir la velocidad operativa.', 'Minutos por pedido o servicio.' ,'Valor numérico razonable.' , 'Sin cita; dato primario del usuario.'), 
            (37, '¿Cuál es tu capacidad máxima de atención o producción por día?', 'Dimensionar la operación.', 'Unidades por día y horas pico.' ,'Debe ser consistente con recursos y tiempo de atención.' , 'Sin cita.'), 
            (37, '¿Qué insumos o suministros necesitas comprar con frecuencia?', 'Definir compras recurrentes y estructura variable.', 'Lista de insumos, frecuencia y proveedor tentativo.' ,'Mínimo 5 insumos si aplica.' , 'Si se anexan cotizaciones, presentarlas como anexos o figuras con nota.'), 

            (38, '¿Cuántas personas trabajarán al inicio?', 'Dimensionar la estructura inicial.', 'Cantidad y modalidad (propietario, familiar, contratado).' ,'Debe incluir roles básicos.' , 'Sin cita.'), 
            (38, '¿Qué hará cada persona?', 'Evitar duplicidad y vacíos operativos.', 'Funciones por rol.' ,'Cada rol con 3 a 5 funciones.' , 'Sin cita.'), 
            (38, '¿Qué nivel de formalización tienes o necesitas?', 'Definir la ruta mínima de cumplimiento.', 'RUC, licencia, permiso sanitario, libros, POS, etc.' ,'Indicar estado: tiene/no tiene/tramitará.' , 'Cuando se cite requisito legal o administrativo, citar norma o entidad oficial.'), 
            (38, '¿Qué requisito no atendido podría frenarte?', 'Prevenir contingencias de apertura.', 'Permiso, local, manipulación de alimentos, marca, etc.' ,'Un riesgo principal.' , 'Si se apoya en requisito externo, citar entidad.'),

            (39, '¿Cuánto necesitas invertir al inicio y en qué conceptos?', 'Cuantificar la inversión total.', 'Equipos, adecuación, garantía, stock inicial, publicidad, capital de trabajo.' ,'Debe sumar el total.' , 'Si se incorporan precios de mercado, usar nota ‘cotización del usuario’ o fuente comercial fechada.'), 
            (39, '¿Cuáles serán tus costos fijos mensuales?', 'Identificar el umbral mínimo de operación.', 'Alquiler, servicios, personal, internet, software, transporte fijo.' ,'Montos mensuales y total.' , 'Sin cita salvo referencia externa de tarifa normativa.'), 
            (39, '¿Cuánto te cuesta producir o atender una unidad?', 'Estimar margen unitario.', 'Ingredientes, materiales, comisión, empaque, delivery variable.' ,'Costo por unidad claro.' , 'Sin cita.'), 
            (39, '¿A qué precio venderás y por qué ese precio es razonable?', 'Definir el precio objetivo.', 'Precio por unidad o ticket promedio y justificación.' ,'Debe ser consistente con cliente y competencia.' , 'Si compara precios observados, usar tabla con nota metodológica.'), 
            (39, '¿Cuántas unidades esperas vender al mes?', 'Construir el escenario base de ingresos.', 'Proyección mensual inicial.' ,'Debe indicar supuesto de ventas por día y días de operación.' , 'Sin cita; explicitar que es proyección del usuario.'), 
            (39, '¿Qué resultado esperarías considerar aceptable en el mes 3 o 4?', 'Ajustar criterio de viabilidad.', 'Meta mínima de ventas o utilidad.' ,'Indicador cuantificable.' , 'Sin cita.'), 

            (40, '¿Qué es lo peor que podría pasar si el negocio no funciona?', 'Identificar el riesgo dominante.', 'Pérdida de inversión, deuda, inventario, reputación, etc.' ,'Un riesgo principal y su impacto.' , 'Sin cita.'), 
            (40, '¿Qué harías si vendes solo la mitad de lo esperado durante 3 meses?', 'Diseñar una reacción temprana.', 'Medidas correctivas concretas.' ,'Mínimo 3 acciones.' , 'Sin cita.'), 
            (40, '¿Qué indicador te obligaría a cambiar o cerrar?', 'Forzar criterio de decisión.', 'Venta mínima, margen, rotación, flujo de caja.' ,'Debe ser medible.' , 'Sin cita.'), 

            (41, '¿Qué evidencias anexarás al perfil?', 'Sostener la decisión con soporte real.', 'Encuesta, cotizaciones, fotos, mapa, lista de precios.' ,'Mínimo 4 anexos.' , 'Cada anexo con título y nota; citar solo si proviene de fuente externa.'), 



            -- ==============================================================================================================================>>>>>>>>>>>>>>> PLAN NEGOCIO RURAL
            (45, '¿Qué producto, recurso o servicio rural ofrecerás?', 'Definir el núcleo productivo o comercial.', 'Nombre del producto y forma de venta.' ,'1 frase clara.' , 'Sin cita.'), 
            (45, '¿Dónde produces, recolectas, acopias o transformas el recurso?', 'Ubicar territorialmente el negocio.', 'Comunidad, chacra, río, carretera, distrito, coordenada referencial.' ,'Debe identificar lugar real.' , 'Si se inserta mapa, tratar como figura con nota de elaboración propia o fuente cartográfica.'), 
            (45, '¿Qué problema del territorio o de la cadena comercial quieres resolver?', 'Justificar la idea en función del contexto.', 'Bajo precio en chacra, pérdida por merma, falta de compradores, etc.' ,'Problema concreto y verificable.' , 'Si se respalda con fuente externa o institucional, citarla.'), 
            (45, '¿Qué oportunidad comercial o de mejora viste en este recurso?', 'Explicar la oportunidad de mercado.', 'Brecha de precio, demanda insatisfecha, diferenciación orgánica, etc.' ,'Máx. 120 palabras.' , 'Sin cita salvo apoyo externo.'), 

            (46, '¿El recurso está disponible todo el año o tiene meses críticos?', 'Medir continuidad de oferta.', 'Meses de alta, media y baja disponibilidad.' ,'Debe identificar al menos 12 meses o periodos clave.' , 'Si usa calendario institucional, citar entidad; si es experiencia propia, rotular como dato del productor.'), 
            (46, '¿Cuánto puedes producir o acopiar por mes?', 'Cuantificar la oferta real.', 'Kilos, sacos, racimos, cajas, litros, unidades.' ,'Debe incluir unidad de medida.' , 'Sin cita; dato primario.'), 
            (46, '¿Qué prácticas aplicas para asegurar sostenibilidad del recurso?', 'Evaluar sostenibilidad y trazabilidad.', 'Rotación, recolección selectiva, no tala, manejo de residuos, etc.' ,'Mínimo 2 prácticas si aplica.' , 'Sin cita.'), 
            (46, '¿Necesitas permiso comunal, sectorial o ambiental para operar?', 'Prevenir riesgos de legalidad y acceso al recurso.', 'Acuerdo comunal, SERFOR, pesca, sanidad, transporte, etc.' ,'Indicar estado actual.' , 'Cuando se mencione permiso o requisito, citar la entidad/norma oficial si se usa como sustento.'), 
            (46, '¿Qué porcentaje se pierde por manejo, clima, transporte o almacenamiento?', 'Hacer visible la pérdida económica.', '% de merma y causa principal.' ,'Valor porcentual y explicación.' , 'Sin cita; dato primario.'), 

            (47, '¿A quién le vendes primero?', 'Ubicar el primer eslabón comercial.', 'Intermediario, feria, acopiador, restaurante, consumidor final.' ,'Debe identificar el primer comprador.' , 'Sin cita.'), 
            (47, '¿Cuál es tu precio de venta en origen o chacra?', 'Fijar el ingreso inicial por unidad.', 'Precio y unidad.' ,'Debe incluir unidad y moneda.' , 'Sin cita.'), 
            (47, '¿Cuál es el precio en mercado o destino final?', 'Medir la brecha de valor en la cadena.', 'Precio observado en destino.' ,'Debe indicar dónde se observó el precio.' , 'Si usa boletines o base pública, citar fuente; si es observación propia, dejar nota metodológica.'), 
            (47, '¿Cuánto gana o agrega valor cada eslabón?', 'Mostrar dónde se queda el margen.', 'Intermediario, transporte, mayorista, minorista.' ,'Cadena mínima de 3 eslabones si aplica.' , 'Citar fuente externa cuando no sea dato primario.'), 
            (47, '¿Quién es el cliente final que más valora tu producto?', 'Conectar con el mercado de destino.', 'Perfil de cliente final o institucional.' ,'Debe indicar uso o preferencia.' , 'Sin cita salvo respaldo externo.'),

            (48, '¿Cómo va el producto desde el origen hasta la venta?', 'Diseñar el flujo del negocio.', 'Cosecha, selección, acopio, transporte, venta.' ,'Debe describir 4 etapas mínimas.' , 'Sin cita; puede presentarse como flujograma.'), 
            (48, '¿Cómo conservas el producto para evitar pérdidas?', 'Definir manejo poscosecha o almacenamiento.', 'Sombra, hielo, cajas, sacos, secado, frío, empaque, etc.' ,'Debe reflejar la realidad operativa.' , 'Sin cita.'), 
            (48, '¿Cómo lo transportas y cuánto cuesta?', 'Cuantificar la logística del negocio.', 'Medio, ruta, frecuencia, costo por viaje.' ,'Debe incluir costo y frecuencia.' , 'Si se usa cotización externa, citar o anotar origen del documento en anexos.'), 
            (48, '¿Necesitas transformar, clasificar o empacar para vender mejor?', 'Explorar valor agregado.', 'Lavado, selección, pelado, fileteado, secado, marca, empaque.' ,'Explicar si aplica o no.' , 'Sin cita.'), 

            (49, '¿Eres productor individual, familia, asociación o comunidad?', 'Precisar forma organizativa.', 'Tipo y nombre si existe.' ,'Debe indicar modalidad real.' , 'Sin cita.'), 
            (49, '¿Quién hace qué dentro del proceso?', 'Aclarar responsabilidades.', 'Producción, selección, acopio, transporte, venta, administración.' ,'Cada rol con su responsable.' , 'Sin cita.'), 
            (49, '¿Existen acuerdos escritos o verbales que sostengan el negocio?', 'Evaluar gobernanza mínima.', 'Acta, acuerdo comunal, reparto de utilidades, préstamo, etc.' ,'Debe indicar si existe o no evidencia.' , 'Si se adjunta acta o documento, presentar como anexo sin necesidad de citar como referencia bibliográfica.'), 
            (49, '¿Qué formalización comercial te conviene en esta etapa?', 'Definir paso siguiente de formalidad.', 'RUC, asociación, cooperativa, permiso local, etc.' ,'Debe ser viable para su escala.' , 'Cuando se cite requisito, citar fuente oficial.'), 

            (50, '¿Cuánto debes invertir para arrancar o mejorar el negocio?', 'Cuantificar inversión inicial.', 'Herramientas, cajas, semillas, acopio, transporte, empaque, adecuación.' ,'Debe sumar total.' , 'Cotizaciones como anexo o nota, no necesariamente referencia bibliográfica.'), 
            (50, '¿Qué costos fijos mensuales tendrás?', 'Definir umbral mínimo de operación.', 'Transporte fijo, alquiler, almacén, sueldo fijo, crédito.' ,'Montos mensuales y total.' , 'Sin cita.'), 
            (50, '¿Cuánto cuesta cada kilo, saco o unidad vendida?', 'Calcular margen real por unidad.', 'Mano de obra, empaque, comisión, merma, combustible variable.' ,'Debe incluir unidad comercial.' , 'Sin cita.'), 
            (50, '¿A qué precio venderás y cuál sería el mejor canal?', 'Comparar rentabilidad por canal.', 'Precio en origen vs. precio directo.' ,'Mínimo dos canales si aplica.' , 'Si usa boletines o precios públicos, citar fuente.'), 
            (50, '¿Cuánto venderás al mes en el escenario base?', 'Armar el ingreso esperado.', 'Volumen y periodicidad.' ,'Debe explicar el supuesto.' , 'Sin cita; proyección del usuario.'), 

            (51, '¿Qué riesgo climático afecta tu producción o logística?', 'Hacer visible el riesgo territorial.', 'Lluvias, crecida, sequía, plagas, bloqueo de ruta, etc.' ,'Un riesgo principal y su efecto.' , 'Si se usa fuente meteorológica o institucional, citarla.'), 
            (51, '¿Dependes de un solo comprador, transportista o ruta?', 'Evaluar vulnerabilidad comercial y logística.', 'Sí/no y explicación.' ,'Debe identificar dependencia crítica.' , 'Sin cita.'), 
            (51, '¿Qué pasa si el precio baja 20% o la merma sube?', 'Medir sensibilidad del negocio.', 'Impacto en utilidad o decisión.' ,'Explicar si sigue siendo viable.' , 'Sin cita.'), 
            (51, '¿Qué decisión tomarás con este perfil: avanzar, ajustar o pausar?', 'Cerrar con una decisión operativa.', 'Semáforo de viabilidad y razones.' ,'Una decisión y 3 razones.' , 'Sin cita.'), 

            (52, '¿Qué evidencias adjuntarás para sostener el perfil?', 'Reunir la base documental mínima.', 'Fotos, mapa, cotizaciones, precios observados, actas, entrevistas.' ,'Mínimo 4 anexos.' , 'Cada anexo con título; citar solo si corresponde a fuente externa.');





INSERT INTO plan_sections ( plan_id, section_id)
VALUES                      ( 1 , 24 ),( 1 , 25 ),( 1 , 26 ),( 1 , 27 ),( 1 , 28 ),( 1 , 29 ),( 1 , 30 ),( 1 , 31 ),( 1 , 32 ),( 1 , 33 ),
                            ( 2 , 6 ),( 2 , 7 ),( 2 , 8 ),( 2 , 9 ),( 2 , 10 ),( 2 ,11 ),( 2 , 12 ),( 2 , 13 ),
                            ( 3 , 14 ),( 3 , 15 ),( 3 , 16 ),( 3 , 17 ),( 3 , 18 ),( 3 , 19 ),( 3 , 20 ),( 3 , 21 ),( 3 , 22 ),( 3 , 23 ),
                            ( 4, 34 ), ( 4, 35 ),( 4, 36 ),( 4, 37 ),( 4, 38 ),( 4, 39 ),( 4, 40 ),( 4, 41 ),( 4, 42 ),( 4, 43 ),
                            ( 5 , 44 ), ( 5 , 45 ), ( 5 , 46 ), ( 5 , 47 ), ( 5 , 48 ), ( 5 , 49 ), ( 5 , 50 ), ( 5 , 51 ), ( 5 , 52 ),( 5 , 53 ), ( 5 , 54 ); 



           
            







            




            








