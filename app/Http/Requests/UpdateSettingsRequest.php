<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'maintenance_mode' => $this->has('maintenance_mode'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'maintenance_mode' => ['boolean'],
            'logo' => ['nullable', 'image', 'max:5120'],
            'favicon' => ['nullable', 'file', 'max:2048', 'mimes:jpeg,jpg,png,gif,webp,ico,bmp,svg'],
            'placeholder_image' => ['nullable', 'image', 'max:5120'],
            'magicbox' => ['nullable', 'array'],
            'magicbox.maintenance' => ['nullable', 'array'],
            'magicbox.maintenance.message' => ['nullable', 'string', 'max:10000'],
            'magicbox.maintenance.estimated_end' => ['nullable', 'string', 'max:64'],
            'magicbox.contact' => ['nullable', 'array'],
            'magicbox.contact.email' => ['nullable', 'email', 'max:255'],
            'magicbox.contact.phone' => ['nullable', 'string', 'max:64'],
            'magicbox.contact.address' => ['nullable', 'string', 'max:500'],
            'magicbox.social' => ['nullable', 'array'],
            'magicbox.social.facebook' => ['nullable', 'string', 'max:500'],
            'magicbox.social.twitter' => ['nullable', 'string', 'max:500'],
            'magicbox.social.instagram' => ['nullable', 'string', 'max:500'],
            'magicbox.social.linkedin' => ['nullable', 'string', 'max:500'],
            'magicbox.social.youtube' => ['nullable', 'string', 'max:500'],
            'magicbox.social.tiktok' => ['nullable', 'string', 'max:500'],
            'magicbox.seo' => ['nullable', 'array'],
            'magicbox.site' => ['nullable', 'array'],
            'magicbox.site.copyright' => ['nullable', 'string', 'max:2000'],
            'magicbox.seo.meta_keywords' => ['nullable', 'string', 'max:500'],
            'magicbox.seo.default_meta_description' => ['nullable', 'string', 'max:1000'],
            'magicbox.seo.allow_indexing' => ['boolean'],
            'magicbox.inject' => ['nullable', 'array'],
            'magicbox.inject.head' => ['nullable', 'string', 'max:100000'],
            'magicbox.inject.body' => ['nullable', 'string', 'max:100000'],
            'magicbox.inject.footer' => ['nullable', 'string', 'max:100000'],
            'magicbox.ads_txt' => ['nullable', 'array'],
            'magicbox.ads_txt.content' => ['nullable', 'string', 'max:100000'],
            'magicbox.google' => ['nullable', 'array'],
            'magicbox.google.oauth_client_id' => ['nullable', 'string', 'max:500'],
            'magicbox.google.oauth_client_secret' => ['nullable', 'string', 'max:500'],
            'magicbox.google.oauth_redirect_url' => ['nullable', 'string', 'max:2000'],
            'magicbox.google.property_id' => ['nullable', 'string', 'max:128'],
            'magicbox.google.analytics_tracking_code' => ['nullable', 'string', 'max:100000'],
            'magicbox.mail' => ['nullable', 'array'],
            'magicbox.mail.smtp_host' => ['nullable', 'string', 'max:255'],
            'magicbox.mail.smtp_port' => ['nullable', 'string', 'max:5'],
            'magicbox.mail.smtp_username' => ['nullable', 'string', 'max:255'],
            'magicbox.mail.smtp_password' => ['nullable', 'string', 'max:255'],
            'magicbox.mail.smtp_encryption' => ['nullable', 'string', 'max:12'],
            'magicbox.mail.from_address' => ['nullable', 'string', 'max:255'],
            'magicbox.mail.from_name' => ['nullable', 'string', 'max:255'],
            'magicbox.integrations' => ['nullable', 'array'],
            'magicbox.integrations.google_analytics_id' => ['nullable', 'string', 'max:64'],
            'magicbox.integrations.google_tag_manager_id' => ['nullable', 'string', 'max:64'],
        ];
    }
}
