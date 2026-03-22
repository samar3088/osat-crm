<?php

namespace App\Imports;

use App\Models\User;
use App\Models\UserTarget;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class TargetImport implements ToModel, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    public function model(array $row): ?UserTarget
    {
        // Find user by employee code
        $user = User::where('employee_code', $row['employee_code'])
            ->role('team_member')
            ->first();

        if (!$user) return null;

        return new UserTarget([
            'user_id'          => $user->id,
            'year'             => now()->year,
            'month'            => (int) $row['month_112'],
            'plan'             => $row['frequency_monthlyquarterlyhalf_yearlyannual'] ?? 'Monthly',
            'category'         => $row['category_type_equitydebthydridblended'] ?? null,
            'type'             => $row['target_type_siplumpsum'] ?? 'SIP',
            'target_amount'    => (float) ($row['target_amount'] ?? 0),
            'target_investors' => (int) ($row['target_clients'] ?? 0),
            'target_amount_achieved'    => 0,
            'target_investors_achieved' => 0,
        ]);
    }
}