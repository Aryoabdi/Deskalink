<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function fetchData()
    {
        $totalUsers = DB::table('users')->whereIn('role', ['client', 'partner'])->count();

        $totalTransactions = DB::table('transactions')->count();

        $totalEarnings = DB::table('transactions')
            ->where('status', 'completed')
            ->sum('platform_fee');

        $totalReports = DB::table('reports')->count();

        $transactions = DB::table('transactions')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->get();

        $chartData = [
            'dates' => $transactions->pluck('date'),
            'counts' => $transactions->pluck('count'),
        ];

        $userRoles = DB::table('users')
            ->select('role', DB::raw('COUNT(*) as count'))
            ->whereIn('role', ['client', 'partner'])
            ->groupBy('role')
            ->get();

        $userData = ['client' => 0, 'partner' => 0];
        foreach ($userRoles as $role) {
            $userData[$role->role] = $role->count;
        }

        return response()->json([
            'total_users' => $totalUsers,
            'total_transactions' => $totalTransactions,
            'total_earnings' => $totalEarnings,
            'total_reports' => $totalReports,
            'transactions' => $chartData,
            'users' => $userData,
        ]);
    }
}
