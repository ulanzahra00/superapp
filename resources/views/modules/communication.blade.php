@extends('layouts.app', ['title' => 'Komunikasi', 'eyebrow' => 'Pesan internal sekolah'])

@section('content')
<section class="surface rounded-xl p-5">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h2 class="text-lg font-bold">Pesan Saya</h2>
            <p class="mt-1 text-sm text-slate-500">
                @if(auth()->user()->hasRole('admin'))
                    Admin dapat mengirim pesan ke guru, siswa, dan orang tua.
                @elseif(auth()->user()->hasRole('guru'))
                    Guru hanya dapat mengirim pesan ke siswa kelas perwaliannya.
                @else
                    Balas pesan dari guru atau admin langsung dari percakapan ini.
                @endif
            </p>
        </div>
        <span class="rounded-full bg-teal-50 px-3 py-1 text-sm font-extrabold text-teal-800">{{ $messages->count() }} pesan</span>
    </div>

    @if(auth()->user()->hasRole(['admin', 'guru']))
        <form method="post" action="{{ route('communication.send') }}" class="mt-5 grid gap-4 rounded-xl border border-teal-100 bg-white/75 p-4 sm:grid-cols-[.8fr_1.2fr_auto] sm:items-end">
            @csrf
            <label class="text-sm font-semibold text-slate-700">Kirim ke
                <select name="receiver_id" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3" required>
                    <option value="">Pilih penerima</option>
                    @if(auth()->user()->hasRole('admin'))
                        <option value="announcement">Pengumuman untuk semua guru, siswa, dan orang tua</option>
                    @endif
                    @foreach($recipients as $recipient)
                        <option value="{{ $recipient->id }}">
                            {{ $recipient->name }} - {{ ucfirst(str_replace('_', ' ', $recipient->role)) }}{{ $recipient->class_name ? ' / '.$recipient->class_name : '' }}
                        </option>
                    @endforeach
                </select>
                @if(auth()->user()->hasRole('guru') && $recipients->isEmpty())
                    <span class="mt-1 block text-xs font-semibold text-amber-700">Belum ada siswa pada kelas perwalian akun guru ini.</span>
                @endif
            </label>
            <label class="text-sm font-semibold text-slate-700">Pesan
                <textarea name="body" rows="2" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3" placeholder="Tulis pesan..." required></textarea>
            </label>
            <button class="btn-primary px-4 py-3 text-sm">Kirim</button>
        </form>
    @endif

    @if($errors->any())
        <div class="mt-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-bold text-rose-700">{{ $errors->first() }}</div>
    @endif

    <div class="mt-5 space-y-4">
        @forelse($messages as $message)
            @php
                $viewer = auth()->user();
                $isMine = $message->sender_id === auth()->id();
                $partner = $isMine ? $message->receiver : $message->sender;
                $hideTechnicalIdentity = $viewer->hasRole(['siswa', 'orang_tua']);
                $partnerRole = optional($partner)->role ?? 'user';
                $partnerLabel = match ($partnerRole) {
                    'admin' => 'Admin sekolah',
                    'guru' => 'Wali kelas / Guru',
                    'siswa' => 'Siswa',
                    'orang_tua' => 'Orang tua',
                    default => 'Pengguna sekolah',
                };
                $isAnnouncement = ($message->category ?? 'personal') === 'announcement';
                $announcementRecipientCount = $message->announcement_recipient_count ?? null;
            @endphp
            <article class="rounded-xl border border-slate-200/80 bg-white/75 p-4 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-extrabold uppercase tracking-wide {{ $isMine ? 'text-emerald-700' : 'text-brand' }}">
                            {{ $isAnnouncement ? 'Pengumuman sekolah' : ($isMine ? 'Terkirim ke' : 'Pesan dari') }}
                        </p>
                        <h3 class="mt-1 font-extrabold text-slate-900">
                            @if($isAnnouncement && $isMine)
                                Terkirim sebagai pengumuman
                            @else
                                {{ optional($partner)->name ?? 'User tidak ditemukan' }}
                            @endif
                        </h3>
                        <p class="text-xs font-semibold text-slate-500">
                            @if($isAnnouncement && $isMine)
                                Semua guru, siswa, dan orang tua{{ $announcementRecipientCount ? ' / '.$announcementRecipientCount.' penerima' : '' }}
                            @elseif($hideTechnicalIdentity)
                                {{ $partnerLabel }}
                            @else
                                {{ optional($partner)->email ?? '-' }} / {{ $partnerLabel }}
                            @endif
                        </p>
                    </div>
                    <div class="text-right text-xs font-bold text-slate-500">
                        <p>{{ $message->created_at->format('d M Y H:i') }}</p>
                        <p class="mt-1">{{ $isAnnouncement && $isMine ? 'Pengumuman' : ($message->read_at ? 'Sudah dibaca' : ($isMine ? 'Belum dibaca' : 'Belum dibalas')) }}</p>
                    </div>
                </div>

                <p class="mt-4 whitespace-pre-line text-justify text-sm leading-6 text-slate-700">{{ $message->body }}</p>

                @unless($isAnnouncement && $isMine)
                    <form method="post" action="{{ route('communication.reply', $message) }}" class="mt-4 grid gap-3 sm:grid-cols-[1fr_auto] sm:items-end">
                        @csrf
                        <label class="text-sm font-semibold text-slate-700">Balas pesan
                            <textarea name="body" rows="2" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3" placeholder="Tulis balasan..." required></textarea>
                        </label>
                        <button class="btn-primary px-4 py-3 text-sm">Kirim Balasan</button>
                    </form>
                @endunless
            </article>
        @empty
            <div class="rounded-lg bg-slate-50 p-4 text-sm text-slate-500">Belum ada pesan untuk akun ini.</div>
        @endforelse
    </div>
</section>
@endsection
