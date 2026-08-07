@extends('layouts.public')

@section('content')
<!-- Importamos fuentes editoriales y limpias desde Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,700;1,400;1,600&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    .tramites-page-container {
        width: 100%;
        padding: 40px 20px;
        background-color: #f7f9f6;
        font-family: 'Poppins', sans-serif !important;
        color: #111111; /* Color negro general para textos */
    }
    .tramites-main-wrapper {
        max-width: 1300px;
        margin: 0 auto;
    }
    .tramites-main-row {
        display: flex;
        flex-wrap: wrap;
        margin-right: -12px;
        margin-left: -12px;
    }
    .tramites-col-form {
        flex: 0 0 68%;
        max-width: 68%;
        padding-right: 12px;
        padding-left: 12px;
    }
    .tramites-col-sidebar {
        flex: 0 0 32%;
        max-width: 32%;
        padding-right: 12px;
        padding-left: 12px;
    }
    @media (max-width: 991.98px) {
        .tramites-col-form, .tramites-col-sidebar {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }
    .card-box-custom {
        background: #ffffff;
        border: none;
        border-radius: 14px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        padding: 35px;
        margin-bottom: 20px;
    }

    /* Títulos principales con estilo editorial similar al flyer */
    .tramites-main-title {
        font-family: 'Playfair Display', serif;
        font-size: 1.8rem;
        font-weight: 700;
        color: #111111;
        margin-bottom: 4px;
    }
    .tramites-main-subtitle {
        font-family: 'Poppins', sans-serif;
        font-size: 0.88rem;
        font-weight: 400;
        color: #555555;
        margin-bottom: 28px;
    }
    .section-category-title {
        font-family: 'Playfair Display', serif;
        font-size: 1.15rem;
        font-weight: 700;
        color: #111111;
        margin-bottom: 14px;
    }
    
    /* Filas y columnas personalizadas */
    .custom-row-2 {
        display: flex;
        flex-wrap: wrap;
        gap: 4%;
        margin-bottom: 1.1rem;
    }
    .custom-col-2 {
        flex: 0 0 48%;
        max-width: 48%;
    }

    .custom-row-3 {
        display: flex;
        flex-wrap: wrap;
        gap: 2%;
        margin-bottom: 1.1rem;
    }
    .custom-col-3 {
        flex: 0 0 32%;
        max-width: 32%;
    }

    @media (max-width: 768px) {
        .custom-col-2, .custom-col-3 {
            flex: 0 0 100% !important;
            max-width: 100% !important;
        }
    }

    .tramites-form-group {
        margin-bottom: 0;
    }
    .tramites-form-group label {
        display: block;
        width: 100%;
        font-size: 0.82rem;
        font-weight: 600;
        color: #111111; /* Etiquetas en negro sólido */
        margin-bottom: 6px;
    }
    .tramites-form-group .form-control, 
    .tramites-form-group .form-select {
        display: block;
        width: 100% !important;
        padding: 10px 14px;
        font-size: 0.88rem;
        font-family: 'Poppins', sans-serif;
        border: 1px solid #ced4da;
        border-radius: 8px;
        background-color: #fff;
        color: #111111; /* Texto ingresado en negro */
    }
    .tramites-form-group .form-control::placeholder {
        color: #6c757d;
        font-weight: 300;
    }
    .tramites-form-group .form-control:focus, 
    .tramites-form-group .form-select:focus {
        border-color: #1b4d3e;
        box-shadow: 0 0 0 0.2rem rgba(27, 77, 62, 0.12);
        color: #111111;
    }
    .contact-prefs-container {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }
    .contact-pref-pill {
        border: 1px solid #ced4da;
        border-radius: 10px;
        padding: 10px 18px;
        background: #fff;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        font-weight: 500;
        color: #111111;
        cursor: pointer;
    }
    
    .privacy-policy-link {
        color: #1b4d3e !important;
        text-decoration: underline;
        font-weight: 600;
    }
    .btn-limpiar-custom {
        background-color: #f1f3f5;
        border: 1px solid #ced4da;
        color: #1b4d3e;
        border-radius: 8px;
        font-size: 0.84rem;
        font-weight: 600;
        padding: 8px 16px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-limpiar-custom:hover {
        background-color: #e9ecef;
        color: #1b4d3e;
    }

    .sidebar-card-title {
        font-family: 'Playfair Display', serif;
        font-size: 1.05rem;
        font-weight: 700;
        color: #111111;
        margin-bottom: 12px;
    }
    .sidebar-item-row {
        display: flex;
        align-items: flex-start;
        margin-bottom: 16px;
    }
    .sidebar-item-row:last-child {
        margin-bottom: 0;
    }
    .sidebar-icon-wrapper {
        flex: 0 0 24px;
        text-align: left;
        margin-right: 12px;
        margin-top: 2px;
        font-size: 1rem;
        color: #1b4d3e;
    }
    .sidebar-content-wrapper {
        flex: 1;
    }
    .sidebar-item-title {
        font-family: 'Poppins', sans-serif;
        font-size: 0.85rem;
        font-weight: 700;
        color: #111111;
        display: block;
        margin-bottom: 2px;
    }
    .sidebar-item-desc {
        font-size: 0.78rem;
        font-weight: 400;
        color: #444444;
        line-height: 1.35;
        display: block;
    }
</style>

<div class="tramites-page-container">
    <div class="tramites-main-wrapper">
        
        <!-- Alerta de éxito -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="tramites-main-row">
            
            <!-- COLUMNA IZQUIERDA: Formulario -->
            <div class="tramites-col-form">
                <div class="card-box-custom">
                    <h1 class="tramites-main-title">Trámites</h1>
                    <p class="tramites-main-subtitle">Completa el formulario y nos pondremos en contacto contigo para ayudarte.</p>

                    <form action="{{ route('tramites.public.store') }}" method="POST">
                        @csrf

                        <!-- Información personal -->
                        <div class="section-category-title">Información personal</div>
                        
                        <!-- Fila 1: Nombres y Apellidos en 2 columnas -->
                        <div class="custom-row-2">
                            <div class="custom-col-2 tramites-form-group">
                                <label>Nombres completos <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" value="{{ old('first_name') }}" class="form-control @error('first_name') is-invalid @enderror" placeholder="Ingresa tus nombres completos" required>
                                @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="custom-col-2 tramites-form-group">
                                <label>Apellidos completos <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" value="{{ old('last_name') }}" class="form-control @error('last_name') is-invalid @enderror" placeholder="Ingresa tus apellidos completos" required>
                                @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- Fila 2: Cédula, Correo y Teléfono en 3 columnas -->
                        <div class="custom-row-3">
                            <div class="custom-col-3 tramites-form-group">
                                <label>Cédula de identidad <span class="text-danger">*</span></label>
                                <input type="text" name="identification_card" value="{{ old('identification_card') }}" class="form-control @error('identification_card') is-invalid @enderror" placeholder="Ej: 1712345678" required>
                                @error('identification_card') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="custom-col-3 tramites-form-group">
                                <label>Correo electrónico <span class="text-danger">*</span></label>
                                <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="Ej: correo@ejemplo.com" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="custom-col-3 tramites-form-group">
                                <label>Teléfono / WhatsApp <span class="text-danger">*</span></label>
                                <input type="text" name="phone" value="{{ old('phone') }}" class="form-control @error('phone') is-invalid @enderror" placeholder="Ej: 098 123 4567" required>
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- Información del trámite -->
                        <div class="section-category-title mt-4">Información del trámite</div>
                        
                        <div class="tramites-form-group mb-3">
                            <label>Tipo de trámite que deseas realizar <span class="text-danger">*</span></label>
                            <select name="tramite_type" class="form-select @error('tramite_type') is-invalid @enderror" required>
                                <option value="" selected disabled>Selecciona un trámite</option>
                                <option value="Compra de propiedad" {{ old('tramite_type') == 'Compra de propiedad' ? 'selected' : '' }}>Compra de propiedad</option>
                                <option value="Arriendo de propiedad" {{ old('tramite_type') == 'Arriendo de propiedad' ? 'selected' : '' }}>Arriendo de propiedad</option>
                                <option value="Asesoría legal" {{ old('tramite_type') == 'Asesoría legal' ? 'selected' : '' }}>Asesoría legal</option>
                                <option value="Avalúos y certificados" {{ old('tramite_type') == 'Avalúos y certificados' ? 'selected' : '' }}>Avalúos y certificados</option>
                                <option value="Otros trámites" {{ old('tramite_type') == 'Otros trámites' ? 'selected' : '' }}>Otros trámites</option>
                            </select>
                            @error('tramite_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="tramites-form-group mb-3">
                            <label>Asunto / Referencia <span class="text-danger">*</span></label>
                            <input type="text" name="subject" value="{{ old('subject') }}" class="form-control @error('subject') is-invalid @enderror" placeholder="Ej: Compra de casa, Arriendo de departamento, Asesoría legal, etc." required>
                            @error('subject') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="tramites-form-group mb-4">
                            <label>Mensaje / Detalles <span class="text-danger">*</span></label>
                            <textarea name="message" rows="4" class="form-control @error('message') is-invalid @enderror" placeholder="Cuéntanos brevemente sobre tu trámite o consulta..." maxlength="500" required>{{ old('message') }}</textarea>
                            <div class="text-end text-muted mt-1" style="font-size: 0.72rem;">0/500</div>
                            @error('message') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Preferencia de contacto -->
                        <div class="section-category-title">Preferencia de contacto</div>
                        <p class="text-muted mb-3" style="font-size: 0.83rem;">¿Cómo prefieres que nos comuniquemos contigo?</p>
                        
                        <div class="contact-prefs-container">
                            <label class="contact-pref-pill">
                                <input class="form-check-input mt-0" type="radio" name="contact_preference" id="pref_whatsapp" value="WhatsApp" {{ old('contact_preference', 'WhatsApp') == 'WhatsApp' ? 'checked' : '' }}>
                                <i class="fab fa-whatsapp text-success"></i> WhatsApp
                            </label>
                            <label class="contact-pref-pill">
                                <input class="form-check-input mt-0" type="radio" name="contact_preference" id="pref_llamada" value="Llamada telefónica" {{ old('contact_preference') == 'Llamada telefónica' ? 'checked' : '' }}>
                                <i class="fas fa-phone text-secondary"></i> Llamada telefónica
                            </label>
                            <label class="contact-pref-pill">
                                <input class="form-check-input mt-0" type="radio" name="contact_preference" id="pref_correo" value="Correo electrónico" {{ old('contact_preference') == 'Correo electrónico' ? 'checked' : '' }}>
                                <i class="fas fa-envelope text-primary"></i> Correo electrónico
                            </label>
                        </div>

                        <!-- Términos y condiciones -->
                        <div class="mb-4 form-check">
                            <input type="checkbox" name="accepted_privacy_policy" class="form-check-input @error('accepted_privacy_policy') is-invalid @enderror" id="terminos" value="1" required>
                            <label class="form-check-label text-dark" style="font-size: 0.83rem; font-weight: 500;" for="terminos">
                                Acepto la <a href="#" class="privacy-policy-link">política de privacidad</a> y el tratamiento de mis datos personales. <span class="text-danger">*</span>
                            </label>
                            @error('accepted_privacy_policy') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Botones de acción -->
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <button type="reset" class="btn btn-limpiar-custom">
                                <i class="fas fa-redo-alt"></i> Limpiar formulario
                            </button>
                            <button type="submit" class="btn text-white px-4 py-2 fw-semibold shadow-sm" style="background-color: #1b4d3e; border-color: #1b4d3e; border-radius: 8px; font-size: 0.85rem;">
                                <i class="fas fa-paper-plane me-2"></i> Enviar solicitud
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- COLUMNA DERECHA: Tarjetas laterales -->
            <div class="tramites-col-sidebar">
                
                <!-- Tarjeta 1: ¿Necesitas ayuda? -->
                <div class="card-box-custom">
                    <h2 class="sidebar-card-title">¿Necesitas ayuda?</h2>
                    <p class="text-dark mb-3" style="font-size: 0.83rem; font-weight: 400;">Nuestro equipo está listo para asesorarte en tu trámite.</p>
                    <ul class="list-unstyled text-dark mb-0" style="font-size: 0.83rem; font-weight: 500;">
                        <li class="mb-2"><i class="fas fa-phone-alt me-2 text-success"></i> 098 805 9187</li>
                        <li class="mb-2"><i class="fas fa-envelope me-2 text-success"></i> info@losandesinmobiliaria.com</li>
                        <li><i class="fas fa-map-marker-alt me-2 text-success"></i> Quito, Ecuador</li>
                    </ul>
                </div>

                <!-- Tarjeta 2: ¿Qué trámites puedes realizar? -->
                <div class="card-box-custom">
                    <h2 class="sidebar-card-title mb-3">¿Qué trámites puedes realizar?</h2>
                    
                    <div class="sidebar-item-row">
                        <div class="sidebar-icon-wrapper"><i class="fas fa-home"></i></div>
                        <div class="sidebar-content-wrapper">
                            <span class="sidebar-item-title">Compra de propiedad</span>
                            <span class="sidebar-item-desc">Asesoría y gestión en la compra de inmuebles.</span>
                        </div>
                    </div>

                    <div class="sidebar-item-row">
                        <div class="sidebar-icon-wrapper"><i class="fas fa-key"></i></div>
                        <div class="sidebar-content-wrapper">
                            <span class="sidebar-item-title">Arriendo de propiedad</span>
                            <span class="sidebar-item-desc">Gestión y asesoría para arriendo de inmuebles.</span>
                        </div>
                    </div>

                    <div class="sidebar-item-row">
                        <div class="sidebar-icon-wrapper"><i class="fas fa-balance-scale"></i></div>
                        <div class="sidebar-content-wrapper">
                            <span class="sidebar-item-title">Asesoría legal</span>
                            <span class="sidebar-item-desc">Asesoría en documentación y trámites legales.</span>
                        </div>
                    </div>

                    <div class="sidebar-item-row">
                        <div class="sidebar-icon-wrapper"><i class="fas fa-file-alt"></i></div>
                        <div class="sidebar-content-wrapper">
                            <span class="sidebar-item-title">Avalúos y certificados</span>
                            <span class="sidebar-item-desc">Solicita avalúos, certificados y documentos.</span>
                        </div>
                    </div>

                    <div class="sidebar-item-row">
                        <div class="sidebar-icon-wrapper"><i class="fas fa-folder-open"></i></div>
                        <div class="sidebar-content-wrapper">
                            <span class="sidebar-item-title">Otros trámites</span>
                            <span class="sidebar-item-desc">Consultas generales y otros trámites relacionados.</span>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta 3: Tiempo de respuesta -->
                <div class="card-box-custom">
                    <div class="d-flex align-items-center">
                        <div class="me-3 text-success" style="font-size: 1.7rem;"><i class="fas fa-clock"></i></div>
                        <div>
                            <span class="sidebar-item-title">Tiempo de respuesta</span>
                            <span class="sidebar-item-desc">Respondemos tu solicitud en menos de 24 horas hábiles.</span>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
@endsection