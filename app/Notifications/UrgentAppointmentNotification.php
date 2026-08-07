<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\AppointmentTracking;

class UrgentAppointmentNotification extends Notification
{
    use Queueable;

    protected $appointment;

    public function __construct(AppointmentTracking $appointment)
    {
        $this->appointment = $appointment;
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; // Enviar por correo y guardar alerta en BD para la campanita del sistema
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('🚨 ¡Alerta URGENTE: Nueva Cita/Visita Agendada!')
                    ->greeting('Hola ' . $notifiable->name)
                    ->line('Se ha registrado una actividad marcada como URGENTE.')
                    ->line('Fecha: ' . $this->appointment->appointment_date)
                    ->line('Referencia: ' . $this->appointment->location_reference)
                    ->action('Ver Agenda', url('/admin/agenda'))
                    ->line('Por favor, revise los detalles de inmediato.');
    }

    public function toArray($notifiable)
    {
        return [
            'appointment_id' => $this->appointment->id,
            'message' => 'Cita urgente programada para el ' . $this->appointment->appointment_date,
        ];
    }
}