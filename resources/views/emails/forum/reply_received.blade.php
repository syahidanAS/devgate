<x-mail::message>
# Halo, {{ $notifiable->name }}!

Ada balasan baru pada topik diskusi yang Anda buat di **DevGate Forum**.

---

### **{{ $replierName }}** baru saja mengirimkan tanggapan:

<x-mail::panel>
{!! Str::limit(strip_tags($reply->body), 200) !!}
</x-mail::panel>

Topik Anda: **"{{ $thread->title }}"**

---

Silakan klik tombol di bawah ini untuk melihat balasan lengkap dan melanjutkan diskusi Anda.

<x-mail::button :url="$url" color="primary">
Lihat Balasan Diskusi
</x-mail::button>

Terima kasih telah aktif berdiskusi dan berbagi ilmu di komunitas **DevGate**!

Salam hangat,<br>
**Tim DevGate**
</x-mail::message>
