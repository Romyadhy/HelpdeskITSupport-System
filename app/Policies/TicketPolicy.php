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
     * User biasa atau admin bisa create tiket
     */
    public function create(User $user): bool
    {
        return $user->isUser() || $user->isAdmin();
    }

    /**
     * Update tiket hanya boleh oleh owner tiket
     */
    public function update(User $user, Ticket $ticket): bool
    {
        return $user->id === $ticket->user_id;
    }

    /**
     * Delete tiket hanya boleh oleh owner tiket
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        return $user->id === $ticket->user_id;
    }

    /**
     * Restore (opsional, bisa false saja)
     */
    public function restore(User $user, Ticket $ticket): bool
    {
        return false;
    }

    /**
     * Force delete (opsional, bisa false saja)
     */
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
        return $user->isSupport() 
            && $ticket->assigned_to === $user->id 
            && $ticket->status !== 'Closed';
    }

    /**
     * Support menutup tiket dengan solusi
     */
    public function close(User $user, Ticket $ticket): bool
    {
        return $user->isSupport() 
            && $ticket->assigned_to === $user->id 
            && $ticket->status !== 'Closed';
    }

    /**
     * Reopen tiket → khusus admin (sudah dihandle di before)
     */
    public function reopen(User $user, Ticket $ticket): bool
    {
        return false;
    }

    /**
     * Komentar di tiket → support atau owner tiket
     */
    public function comment(User $user, Ticket $ticket): bool
    {
        return $user->isSupport() || $user->id === $ticket->user_id;
    }
}
