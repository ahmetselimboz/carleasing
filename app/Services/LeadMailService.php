<?php

namespace App\Services;

use App\Mail\LeadAdminAlertMail;
use App\Mail\LeadCustomerReceiptMail;
use App\Models\Message;
use App\Models\RentalRequest;
use App\Models\User;
use App\Models\WeCallYou;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class LeadMailService
{
    public function handleCreatedLead(string $type, int $id): void
    {
        if (! config('lead-mail.enabled', true)) {
            return;
        }

        $model = $this->resolveLeadModel($type, $id);

        if ($model === null) {
            return;
        }

        $payload = $this->buildPayload($type, $model);
        $this->sendCustomerReceipt($payload);
        $this->sendAdminAlert($payload);
    }

    private function resolveLeadModel(string $type, int $id): Message|RentalRequest|WeCallYou|null
    {
        return match ($type) {
            'rental_request' => RentalRequest::query()->find($id),
            'message' => Message::query()->find($id),
            'we_call_you' => WeCallYou::query()->find($id),
            default => null,
        };
    }

    /**
     * @param  Message|RentalRequest|WeCallYou  $model
     * @return array<string, mixed>
     */
    private function buildPayload(string $type, Model $model): array
    {
        $fullName = trim((string) ($model->name ?? '').' '.(string) ($model->surname ?? ''));
        $typeLabel = match ($type) {
            'rental_request' => 'Kiralama Talebi',
            'message' => 'Iletisim Mesaji',
            'we_call_you' => 'Geri Arama Talebi',
            default => 'Talep',
        };

        $adminUrl = match ($type) {
            'rental_request' => route('rental-requests.show', $model),
            'message' => route('messages.show', $model),
            'we_call_you' => route('we-call-you.show', $model),
            default => route('dashboard'),
        };

        return [
            'type' => $type,
            'type_label' => $typeLabel,
            'lead_id' => (int) $model->getKey(),
            'name' => $fullName !== '' ? $fullName : 'Bilinmeyen',
            'email' => $model->email ?? null,
            'phone' => $model->phone_number ?? null,
            'created_at' => optional($model->created_at)->format('d.m.Y H:i'),
            'admin_url' => $adminUrl,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function sendCustomerReceipt(array $payload): void
    {
        $email = $this->normalizeEmail($payload['email'] ?? null);
        if ($email === null) {
            return;
        }

        $recipient = $this->forceTo() ?? $email;

        try {
            Mail::to($recipient)->send(new LeadCustomerReceiptMail($payload));
        } catch (\Throwable $e) {
            Log::error('Lead customer receipt email failed.', [
                'type' => $payload['type'] ?? null,
                'lead_id' => $payload['lead_id'] ?? null,
                'recipient' => $recipient,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function sendAdminAlert(array $payload): void
    {
        $recipients = $this->adminRecipients();

        if ($recipients->isEmpty()) {
            return;
        }

        $forceTo = $this->forceTo();
        if ($forceTo !== null) {
            $recipients = collect([$forceTo]);
        }

        foreach ($recipients as $recipient) {
            try {
                Mail::to($recipient)->send(new LeadAdminAlertMail($payload));
            } catch (\Throwable $e) {
                Log::error('Lead admin alert email failed.', [
                    'type' => $payload['type'] ?? null,
                    'lead_id' => $payload['lead_id'] ?? null,
                    'recipient' => $recipient,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * @return Collection<int, string>
     */
    private function adminRecipients(): Collection
    {
        $roles = config('lead-mail.notify_roles', []);
        $withSuperAdmin = in_array('super_admin', $roles, true);
        $roleValues = array_values(array_filter($roles, fn ($role) => $role !== 'super_admin'));

        return User::query()
            ->where('active', true)
            ->where(function ($query) use ($withSuperAdmin, $roleValues): void {
                if ($withSuperAdmin) {
                    $query->orWhere('is_super_admin', true);
                }

                if ($roleValues !== []) {
                    $query->orWhereIn('role', $roleValues);
                }
            })
            ->pluck('email')
            ->map(fn ($email) => $this->normalizeEmail($email))
            ->filter()
            ->unique()
            ->values();
    }

    private function forceTo(): ?string
    {
        return $this->normalizeEmail(config('lead-mail.force_to'));
    }

    private function normalizeEmail(mixed $email): ?string
    {
        if (! is_string($email)) {
            return null;
        }

        $email = trim($email);

        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
    }
}
