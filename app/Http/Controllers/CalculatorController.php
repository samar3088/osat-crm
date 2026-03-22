<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class CalculatorController extends Controller
{
    /**
     * Calculator registry — add new calculators here only
     * DRY: One place to register all calculators
     */
    public static function getCalculators(): array
    {
        return [
            [
                'id'          => 'sip',
                'name'        => 'SIP Calculator',
                'description' => 'Calculate returns on Systematic Investment Plan',
                'icon'        => 'trending-up',
                'color'       => 'blue',
                'tags'        => ['sip', 'systematic', 'investment', 'monthly'],
            ],
            [
                'id'          => 'lumpsum',
                'name'        => 'Lumpsum Calculator',
                'description' => 'Calculate returns on one-time investment',
                'icon'        => 'dollar-sign',
                'color'       => 'green',
                'tags'        => ['lumpsum', 'one-time', 'investment'],
            ],
            [
                'id'          => 'fd',
                'name'        => 'FD Calculator',
                'description' => 'Calculate Fixed Deposit maturity amount',
                'icon'        => 'landmark',
                'color'       => 'purple',
                'tags'        => ['fd', 'fixed', 'deposit', 'bank'],
            ],
            [
                'id'          => 'simple-interest',
                'name'        => 'Simple Interest',
                'description' => 'Calculate simple interest on principal amount',
                'icon'        => 'percent',
                'color'       => 'orange',
                'tags'        => ['simple', 'interest', 'principal'],
            ],
            [
                'id'          => 'compound-interest',
                'name'        => 'Compound Interest',
                'description' => 'Calculate compound interest with compounding frequency',
                'icon'        => 'bar-chart',
                'color'       => 'teal',
                'tags'        => ['compound', 'interest', 'compounding'],
            ],
            [
                'id'          => 'cagr',
                'name'        => 'CAGR Calculator',
                'description' => 'Calculate Compound Annual Growth Rate',
                'icon'        => 'activity',
                'color'       => 'red',
                'tags'        => ['cagr', 'growth', 'annual', 'rate'],
            ],
            [
                'id'          => 'inflation',
                'name'        => 'Inflation Calculator',
                'description' => 'Calculate future value adjusted for inflation',
                'icon'        => 'arrow-up',
                'color'       => 'yellow',
                'tags'        => ['inflation', 'future', 'value', 'purchasing'],
            ],
        ];
    }

    public function index(): View
    {
        $calculators = self::getCalculators();
        return view('calculators.index', compact('calculators'));
    }
}