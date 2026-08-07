@extends('layouts.public')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-xl-11">
            
            <!-- Ruta de navegación (Breadcrumb) -->
            <nav aria-label="breadcrumb" class="mb-3">
                
            </nav>

            <div class="row g-4">
                <!-- Columna Izquierda: Información, Beneficios y Aviso PDF -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm p-4 h-100 bg-white">
                        <div class="text-center mb-4">
                            <div class="rounded-circle d-inline-flex p-3 shadow-sm mb-2" style="background-color: #e8f5e9;">
                                <i class="fas fa-briefcase fa-2x text-success"></i>
                            </div>
                            <h3 class="h5 fw-bold text-dark">Trabaja con nosotros</h3>
                            <p class="text-muted small">Únete al equipo y construyamos juntos el futuro.</p>
                        </div>

                        <hr class="text-muted opacity-25">

                        <div class="mb-4">
                            <div class="d-flex align-items-start mb-3">
                                <div class="rounded-circle p-2 me-3 mt-1 d-inline-flex" style="background-color: #e8f5e9;">
                                    <i class="fas fa-chart-line text-success"></i>
                                </div>
                                <div>
                                    <span class="fw-bold small d-block">Crecimiento profesional</span>
                                    <span class="text-muted small">Te ofrecemos oportunidades de desarrollo y aprendizaje continuo.</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-start mb-3">
                                <div class="rounded-circle p-2 me-3 mt-1 d-inline-flex" style="background-color: #e8f5e9;">
                                    <i class="fas fa-shield-alt text-success"></i>
                                </div>
                                <div>
                                    <span class="fw-bold small d-block">Buen ambiente laboral</span>
                                    <span class="text-muted small">Promovemos el respeto, la colaboración y el trabajo en equipo.</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-start mb-3">
                                <div class="rounded-circle p-2 me-3 mt-1 d-inline-flex" style="background-color: #e8f5e9;">
                                    <i class="fas fa-bullseye text-success"></i>
                                </div>
                                <div>
                                    <span class="fw-bold small d-block">Impacto real</span>
                                    <span class="text-muted small">Tu talento contribuye a proyectos que generan valor y marcan la diferencia.</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-auto p-3 rounded shadow-sm border border-success border-opacity-25" style="background-color: #fcfdfd;">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-file-pdf fa-2x text-success me-3"></i>
                                <div>
                                    <span class="fw-bold small d-block text-dark">Solo archivos PDF</span>
                                    <span class="text-muted small d-block" style="font-size: 0.75rem;">Asegúrate de subir tu hoja de vida en formato PDF.</span>
                                    <span class="badge bg-success bg-opacity-10 text-success mt-1 px-2 py-1 fw-bold" style="font-size: 0.7rem;">Máx. 5 MB</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha: Formulario de Postulación -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm p-4 bg-white">
                        <h2 class="h4 fw-bold text-dark mb-1">Postula con nosotros</h2>
                        <p class="text-muted small mb-4">Completa el formulario y adjunta tu hoja de vida para aplicar a nuestras vacantes.</p>

                        <!-- Alerta de éxito -->
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <!-- Alerta de errores de validación -->
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0 small">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('postulaciones.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Nombre completo <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-muted"></i></span>
                                        <input type="text" name="nombres" value="{{ old('nombres') }}" class="form-control border-start-0 ps-0" placeholder="Ingresa tu nombre completo" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Apellido completo <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-muted"></i></span>
                                        <input type="text" name="apellidos" value="{{ old('apellidos') }}" class="form-control border-start-0 ps-0" placeholder="Ingresa tu apellido completo" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Celular <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-phone text-muted"></i></span>
                                        <input type="text" name="celular" value="{{ old('celular') }}" class="form-control border-start-0 ps-0" placeholder="Ej: +593 99 123 4567" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Correo electrónico <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                                        <input type="email" name="correo" value="{{ old('correo') }}" class="form-control border-start-0 ps-0" placeholder="ejemplo@correo.com" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Fila 1 Juntos: Profesión (Área de interés) y Experiencia Laboral -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Profesión / Área de interés <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-graduation-cap text-muted"></i></span>
                                        <select name="profesion" class="form-control border-start-0 ps-0" required>
                                            <option value="" disabled selected>Selecciona una opción...</option>
                                            <option value="asesor comercial" {{ old('profesion') == 'asesor comercial' ? 'selected' : '' }}>Asesor comercial</option>
                                            <option value="publicista y marketing" {{ old('profesion') == 'publicista y marketing' ? 'selected' : '' }}>Publicista y marketing</option>
                                            <option value="contabilidad" {{ old('profesion') == 'contabilidad' ? 'selected' : '' }}>Contabilidad</option>
                                            <option value="administrativo" {{ old('profesion') == 'administrativo' ? 'selected' : '' }}>Administrativo</option>
                                            <option value="area legal" {{ old('profesion') == 'area legal' ? 'selected' : '' }}>Área legal</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Experiencia laboral (años) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-briefcase text-muted"></i></span>
                                        <select name="experiencia" class="form-control border-start-0 ps-0" required>
                                            <option value="" disabled selected>Selecciona tu experiencia</option>
                                            <option value="Menos de 1 año" {{ old('experiencia') == 'Menos de 1 año' ? 'selected' : '' }}>Menos de 1 año</option>
                                            <option value="1 a 3 años" {{ old('experiencia') == '1 a 3 años' ? 'selected' : '' }}>1 a 3 años</option>
                                            <option value="3 a 5 años" {{ old('experiencia') == '3 a 5 años' ? 'selected' : '' }}>3 a 5 años</option>
                                            <option value="Más de 5 años" {{ old('experiencia') == 'Más de 5 años' ? 'selected' : '' }}>Más de 5 años</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Fila Abajo: Ciudad de residencia -->
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Ciudad de residencia <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-map-marker-alt text-muted"></i></span>
                                    <input type="text" name="ciudad" value="{{ old('ciudad') }}" class="form-control border-start-0 ps-0" placeholder="Ej: Cuenca, Ecuador" required>
                                </div>
                            </div>

                            <!-- Zona Interactiva para Adjuntar PDF -->
                            <div class="mb-4">
                                <label class="form-label small fw-semibold">Adjunta tu hoja de vida (PDF) <span class="text-danger">*</span></label>
                                <div id="dropZone" class="border border-2 border-dashed rounded p-4 text-center bg-light" style="cursor: pointer; border-color: #cbd5e1 !important;">
                                    <div class="rounded-circle d-inline-flex p-2 mb-2" style="background-color: #e8f5e9;">
                                        <i class="fas fa-cloud-upload-alt text-success"></i>
                                    </div>
                                    <p id="fileLabelText" class="mb-1 small fw-semibold text-dark">Arrastra tu archivo PDF aquí<br><span class="text-muted fw-normal" style="font-size: 0.8rem;">o haz clic en el botón para seleccionar</span></p>
                                    <input type="file" name="cv" class="d-none" id="cvFile" accept=".pdf" required>
                                    <button type="button" class="btn btn-outline-success btn-sm mt-2 px-3 fw-semibold" style="border-color: #1b4d3e; color: #1b4d3e;" onmouseover="this.style.backgroundColor='#1b4d3e'; this.style.color='#fff';" onmouseout="this.style.backgroundColor='transparent'; this.style.color='#1b4d3e';">Seleccionar archivo PDF</button>
                                    <div class="form-text small mt-2 text-muted" style="font-size: 0.75rem;">Formato permitido: PDF • Máx. 5 MB</div>
                                </div>
                            </div>

                            <div class="mb-4 d-flex align-items-center">
                                <i class="fas fa-lock text-muted me-2 small"></i>
                                <span class="text-muted" style="font-size: 0.8rem;">Tu información está protegida y será tratada con confidencialidad.</span>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn px-5 fw-semibold text-white shadow-sm" style="background-color: #1b4d3e; border-color: #1b4d3e;">
                                    <i class="fas fa-paper-plane me-2"></i>Enviar postulación
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Script interactivo para el archivo PDF -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('cvFile');
        const fileLabelText = document.getElementById('fileLabelText');

        dropZone.addEventListener('click', function () {
            fileInput.click();
        });

        fileInput.addEventListener('change', function (e) {
            if (e.target.files.length > 0) {
                let fileName = e.target.files[0].name;
                fileLabelText.innerHTML = `<i class="fas fa-file-pdf text-danger me-1"></i> <strong class="text-dark">Seleccionado:</strong> <span class="text-success">${fileName}</span>`;
            }
        });
    });
</script>
@endsection