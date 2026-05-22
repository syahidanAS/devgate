<x-mail::message>
# Halo, {{ $notifiable->name }}!

**{{ $replierName }}** baru saja membalas komentar Anda di forum **DevGate**.

---

### Komentar Anda yang dibalas:

<x-mail::panel>
> {{ Str::limit(strip_tags($parentReply->body), 150) }}
</x-mail::panel>

### Balasan dari **{{ $replierName }}**:

<x-mail::panel>
{!! Str::limit(strip_tags($reply->body), 200) !!}
</x-mail::panel>

Topik Diskusi: **"{{ $thread->title }}"**

---

Klik tombol di bawah ini untuk melihat percakapan lengkap dan membalas balik.

<x-mail::button :url="$url" color="primary">
Lihat Balasan
</x-mail::button>

Terima kasih telah aktif berdiskusi di komunitas **DevGate**!

Salam hangat,<br>
**Tim DevGate**
</x-mail::message>
