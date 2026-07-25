<?php

// DATA-01 / FR-AUTH-01 / NFR-03

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'is_active', 'created_by'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements HasLocalePreference
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /** Which admin created this account. DATA-01 */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Classes owned by this guru. DATA-02 */
    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class, 'guru_id');
    }

    /** Class memberships held by this siswa. DATA-03 */
    public function classMemberships(): HasMany
    {
        return $this->hasMany(ClassMember::class, 'student_id');
    }

    /** Quiz attempts made by this siswa. DATA-11 / M5 */
    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class, 'student_id');
    }

    // DATA-15 / M6
    public function componentScores(): HasMany
    {
        return $this->hasMany(ComponentScore::class, 'student_id');
    }

    // DATA-14 / M6
    public function finalGrades(): HasMany
    {
        return $this->hasMany(FinalGrade::class, 'student_id');
    }

    /** Indonesian locale for all notifications. FR-AUTH-04 */
    public function preferredLocale(): string
    {
        return 'id';
    }

    /** Send Indonesian password reset notification. FR-AUTH-04 / BR-02 */
    public function sendPasswordResetNotification($token): void
    {
        $notification = new ResetPasswordNotification($token);
        $notification->toMailUsing(
            fn ($notifiable, $token) => (new MailMessage)
                ->subject('Atur Ulang Kata Sandi')
                ->line('Anda menerima email ini karena ada permintaan pengaturan ulang kata sandi untuk akun Anda.')
                ->action('Atur Ulang Kata Sandi', url(route('auth.reset.show', [
                    'token' => $token,
                    'email' => $notifiable->getEmailForPasswordReset(),
                ], false)))
                ->line('Tautan pengaturan ulang kata sandi ini akan kedaluwarsa dalam '.config('auth.passwords.'.config('auth.defaults.passwords').'.expire').' menit.')
                ->line('Jika Anda tidak meminta pengaturan ulang kata sandi, abaikan email ini.')
        );

        $this->notify($notification);
    }
}
