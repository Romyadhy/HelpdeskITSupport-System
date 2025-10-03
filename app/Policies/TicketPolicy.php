<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    /**
     * Admin full access sebelum cek lainnya
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return null;
    }

    /**
     * Semua user bisa lihat daftar tiket
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Bisa lihat tiket kalau owner atau support
     */
    public function view(User $user, Ticket $ticket): bool
    {
        return $user->id === $ticket->user_id || $user->isSupport();
    }

    /**
     * User biasa bisa create tiket
     */
    public function create(User $user): bool
    {
        return $user->isUser();
    }

    /**
     * Update tiket boleh oleh owner tiket atau support
     */
    public function update(User $user, Ticket $ticket): bool
    {
        return $user->id === $ticket->user_id || $user->isSupport();
    }

    /**
     * Delete tiket: owner boleh hapus tiket miliknya,
     * support juga boleh hapus (misalnya setelah closed).
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        return $user->id === $ticket->user_id || $user->isSupport();
    }

    public function restore(User $user, Ticket $ticket): bool
    {
        return false;
    }

    public function forceDelete(User $user, Ticket $ticket): bool
    {
        return false;
    }

    // ===============================
    //  Extra untuk IT Support
    // ===============================

    /**
     * Support ambil tiket (assign ke dirinya sendiri)
     */
    public function take(User $user, Ticket $ticket): bool
    {
        return $user->isSupport() && is_null($ticket->assigned_to);
    }

    /**
     * Support mulai kerjakan tiket
     */
    public function start(User $user, Ticket $ticket): bool
    {
        return $user->isSupport() && $ticket->status === 'In Progress';
    }

    /**
     * Support menutup tiket dengan solusi
     */
    public function close(User $user, Ticket $ticket): bool
    {
        return $user->isSupport() && $ticket->status !== 'Closed';
    }

    /**
     * Komentar di tiket → support atau owner tiket
     */
    public function comment(User $user, Ticket $ticket): bool
    {
        return $user->isSupport() || $user->id === $ticket->user_id;
    }
}
