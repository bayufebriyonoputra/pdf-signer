<?php

namespace App\Enum;

enum StatusEnum: string
{
    case NEW = 'new';
    case REVISE = 'revise';
    case CHECKED = 'check';
    case SIGNED = 'sign';
    case SENDED = 'send';
    case CONFIRMED = 'confirm';
    case PENDING = 'pending';
    case CANCEL = 'cancel';
    case DONE = 'done';

    public function label(): string
    {
        return match ($this) {
            self::NEW => 'Siap Cek',
            self::REVISE => 'Revisi',
            self::CHECKED => 'Siap Approve',
            self::SIGNED => 'Finish Approve',
            self::SENDED => 'Sended',
            self::CONFIRMED => 'Confirm',
            self::PENDING => 'Pending',
            self::CANCEL => 'Canceled',
            self::DONE => 'Done'

        };
    }

    public static function toArray(): array
    {
        return array_map(fn($status) => [
            'value' => $status->value,
            'label' => $status->label(),
        ], self::cases());
    }

    public function badge(): string
    {
        return match ($this) {
            self::NEW => '<span class="px-2 py-1 text-sm font-bold text-white rounded-md bg-cyan-500"><i class="bi bi-stars"></i>' . self::NEW->label() . '</span>',
            self::CHECKED => '<span class="px-2 py-1 text-sm font-bold text-white bg-blue-700 rounded-md"><i class="bi bi-check2-circle"></i>' . self::CHECKED->label() . '</span>',
            self::SIGNED => '<span class="px-2 py-1 text-sm font-bold text-white bg-green-600 rounded-md"><i class="bi bi-clipboard-check-fill"></i>' . self::SIGNED->label() . '</span>',
            self::SENDED => '<span class="px-2 py-1 text-sm font-bold text-white bg-teal-600 rounded-md"><i class="bi bi-envelope-check-fill"></i>' . self::SENDED->label() . '</span>',
            self::CONFIRMED => '<span class="px-2 py-1 text-sm font-bold text-white bg-indigo-700 rounded-md"><i class="bi bi-check-circle-fill"></i>' . self::CONFIRMED->label() . '</span>',
            self::PENDING => '<span class="px-2 py-1 text-sm font-bold text-white bg-red-500 rounded-md"><i class="bi bi-clock-history"></i>' . self::PENDING->label() . '</span>',
            self::CANCEL => '<span class="px-2 py-1 text-sm font-bold text-white bg-red-500 rounded-md"><i class="bi bi-x-octagon-fill"></i>' . self::CANCEL->label() .  '</span>',
            self::REVISE => '<span class="px-2 py-1 text-sm font-bold text-white rounded-md bg-amber-600"><i class="bi bi-arrow-counterclockwise"></i>' . self::REVISE->label() . '</span>',
            self::DONE => '<span class="px-2 py-1 text-sm font-bold text-white rounded-md bg-lime-500"><i class="bi bi-list-check"></i>' . self::DONE->label() . '</span>',
            default => 'tidak diketahui'
        };
    }
}
