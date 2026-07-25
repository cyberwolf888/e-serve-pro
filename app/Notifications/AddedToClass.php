<?php

// FR-GR-03 / FR-SW-03 / BR-02

namespace App\Notifications;

use App\Models\SchoolClass;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AddedToClass extends Notification
{
    use Queueable;

    public const REASON_ADDED = 'added';

    public const REASON_JOINED = 'joined';

    public function __construct(
        private SchoolClass $class,
        public readonly string $reason = self::REASON_ADDED,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subject = $this->reason === self::REASON_JOINED
            ? 'Berhasil Bergabung ke Kelas'
            : 'Anda Ditambahkan ke Kelas';

        $line = $this->reason === self::REASON_JOINED
            ? "Anda telah bergabung ke kelas **{$this->class->name}**."
            : "Anda telah ditambahkan ke kelas **{$this->class->name}** oleh guru.";

        return (new MailMessage)
            ->subject($subject)
            ->line($line)
            ->action('Lihat Kelas', url(route('siswa.classes.show', $this->class)))
            ->line('Silakan masuk ke portal untuk melihat materi, pertemuan, dan kuis yang tersedia.');
    }
}
