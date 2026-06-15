<?php

namespace App\Providers;

use App\Models\Message;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('layouts.app', function ($view) {
            $user = auth()->user();
            $popups = collect();

            if ($user && ! request()->routeIs('communication')) {
                if ($user->hasRole(['guru', 'siswa', 'orang_tua'])) {
                    $announcementQuery = Message::with('sender')
                        ->where('school_id', $user->school_id)
                        ->where('receiver_id', $user->id)
                        ->where('category', 'announcement')
                        ->whereNull('read_at')
                        ->whereHas('sender', function ($query) {
                            $query->where('role', 'admin');
                        });

                    $announcements = (clone $announcementQuery)->latest()->take(3)->get();

                    if ($announcements->isNotEmpty()) {
                        $popups->push([
                            'count' => (clone $announcementQuery)->count(),
                            'eyebrow' => 'Pengumuman sekolah',
                            'title' => 'pengumuman belum dibaca',
                            'note' => 'pengumuman lain yang juga belum dibaca.',
                            'latest' => $announcements->first(),
                            'messages' => $announcements,
                        ]);
                    }
                }

                $unreadQuery = Message::with('sender')
                    ->where('school_id', $user->school_id)
                    ->where('receiver_id', $user->id)
                    ->where('category', 'personal')
                    ->whereNull('read_at');

                $popupEyebrow = 'Pesan baru';
                $popupTitle = 'pesan belum dibaca';
                $popupNote = 'pesan lain yang juga belum dibaca.';

                if ($user->hasRole('guru')) {
                    $unreadQuery->whereHas('sender', function ($query) {
                        $query->where('role', 'siswa');
                    });
                    $popupEyebrow = 'Balasan siswa baru';
                    $popupTitle = 'balasan siswa belum dibaca';
                    $popupNote = 'balasan siswa lain yang juga belum dibaca.';
                } elseif (! $user->hasRole(['siswa', 'orang_tua'])) {
                    $unreadQuery = null;
                }

                $unreadMessages = $unreadQuery
                    ? (clone $unreadQuery)->latest()->take(5)->get()
                    : collect();

                if ($unreadMessages->isNotEmpty()) {
                    $popups->push([
                        'count' => (clone $unreadQuery)->count(),
                        'eyebrow' => $popupEyebrow,
                        'title' => $popupTitle,
                        'note' => $popupNote,
                        'latest' => $unreadMessages->first(),
                        'messages' => $unreadMessages,
                    ]);
                }
            }

            $view->with('incomingMessagePopups', $popups);
        });
    }
}
