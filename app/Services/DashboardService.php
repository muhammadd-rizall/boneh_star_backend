<?php

namespace App\Services;

use App\Repositories\BookingRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\BusesRepository;
use App\Repositories\TransactionRepository;
use App\Repositories\ReviewRepository;
use App\Models\Payment;
use App\Models\Booking;
use App\Models\Quotation;

class DashboardService
{
    public function __construct(
        protected BookingRepository $bookingRepository,
        protected CustomerRepository $customerRepository,
        protected BusesRepository $busesRepository,
        protected TransactionRepository $transactionRepository,
        protected ReviewRepository $reviewRepository
    ) {}

    public function getDashboardData(): array
    {
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();

        // 1. Stats
        $totalBookingsThisMonth = Booking::where('created_at', '>=', $startOfMonth)->count();
        $prevMonthBookings = Booking::whereBetween('created_at', [
            $now->copy()->subMonth()->startOfMonth(),
            $now->copy()->subMonth()->endOfMonth()
        ])->count();
        
        $bookingTrend = $prevMonthBookings > 0 
            ? round((($totalBookingsThisMonth - $prevMonthBookings) / $prevMonthBookings) * 100)
            : 0;

        $totalCustomers = $this->customerRepository->getStatistics()['cards']['total'];
        $newCustomersThisMonth = $this->customerRepository->getStatistics()['cards']['new_members'];

        $busStats = $this->busesRepository->getStatistics()['cards'];
        
        $revenueThisMonth = Payment::where('status', 'verified')
            ->where('created_at', '>=', $startOfMonth)
            ->sum('amount');
        
        $prevMonthRevenue = Payment::where('status', 'verified')
            ->whereBetween('created_at', [
                $now->copy()->subMonth()->startOfMonth(),
                $now->copy()->subMonth()->endOfMonth()
            ])->sum('amount');
            
        $revenueTrend = $prevMonthRevenue > 0
            ? round((($revenueThisMonth - $prevMonthRevenue) / $prevMonthRevenue) * 100)
            : 0;

        $totalTransactions = $this->transactionRepository->getStatistics()['total_transactions'];
        $avgRating = $this->reviewRepository->getStatistics()['average_rating'];

        // 2. Recent Activities (filtered by permissions)
        $activities = collect();
        $user = auth()->user();

        if ($user->can('bookings.view')) {
            $recentBookings = Booking::with('customer')
                ->latest()
                ->take(5)
                ->get()
                ->map(fn($b) => [
                    'icon' => 'CheckCircle2',
                    'color' => 'text-emerald-500',
                    'text' => "Pemesanan #{$b->booking_code} oleh {$b->customer->name} telah dibuat",
                    'time' => $b->created_at->diffForHumans()
                ]);
            $activities = $activities->concat($recentBookings);
        }

        if ($user->can('payments.view')) {
            $recentPayments = Payment::with('transaction.booking.customer')
                ->where('status', 'pending')
                ->latest()
                ->take(3)
                ->get()
                ->map(fn($p) => [
                    'icon' => 'CreditCard',
                    'color' => 'text-sky-500',
                    'text' => "Pembayaran baru dari {$p->transaction->booking->customer->name} menunggu verifikasi",
                    'time' => $p->created_at->diffForHumans()
                ]);
            $activities = $activities->concat($recentPayments);
        }

        if ($user->can('quotations.view')) {
            $recentQuotations = Quotation::with('booking.customer')
                ->where('status', 'sent')
                ->latest()
                ->take(2)
                ->get()
                ->map(fn($q) => [
                    'icon' => 'AlertCircle',
                    'color' => 'text-amber-500',
                    'text' => "Penawaran #QT-{$q->id} telah dikirim ke {$q->booking->customer->name}",
                    'time' => $q->created_at->diffForHumans()
                ]);
            $activities = $activities->concat($recentQuotations);
        }

        $activities = $activities
            ->sortByDesc('time')
            ->take(5)
            ->values()
            ->toArray();

        // 3. Monthly Chart Data (Last 6 months)
        $startDate = $now->copy()->subMonths(5)->startOfMonth()->format('Y-m-d');
        $endDate = $now->format('Y-m-d');
        $reportStats = $this->bookingRepository->getReportStatistics($startDate, $endDate);

        return [
            'stats' => [
                [
                    'label' => "Total Pemesanan",
                    'value' => (string)$totalBookingsThisMonth,
                    'sub' => "bulan ini",
                    'trendLabel' => ($bookingTrend >= 0 ? '+' : '') . $bookingTrend . "%",
                    'trend' => $bookingTrend >= 0 ? "up" : "down",
                    'color' => "emerald",
                    'href' => "/admin/bookings",
                ],
                [
                    'label' => "Total Pengguna",
                    'value' => number_format($totalCustomers),
                    'sub' => "terdaftar",
                    'trendLabel' => "+$newCustomersThisMonth baru",
                    'trend' => "up",
                    'color' => "violet",
                    'href' => "/admin/customers",
                ],
                [
                    'label' => "Bus Aktif",
                    'value' => (string)$busStats['available'],
                    'sub' => "dari {$busStats['total']} unit",
                    'color' => "sky",
                    'href' => "/admin/buses",
                ],
                [
                    'label' => "Pendapatan",
                    'value' => "Rp " . number_format($revenueThisMonth / 1000000, 1) . "jt",
                    'sub' => "bulan ini",
                    'trendLabel' => ($revenueTrend >= 0 ? '+' : '') . $revenueTrend . "%",
                    'trend' => $revenueTrend >= 0 ? "up" : "down",
                    'color' => "amber",
                    'href' => "/admin/reports/income",
                ],
                [
                    'label' => "Transaksi",
                    'value' => (string)$totalTransactions,
                    'sub' => "total transaksi",
                    'color' => "slate",
                    'href' => "/admin/transactions",
                ],
                [
                    'label' => "Rating Rata-rata",
                    'value' => (string)$avgRating,
                    'sub' => "dari semua ulasan",
                    'color' => "rose",
                    'href' => "/admin/reviews",
                ],
            ],
            'recent_activity' => $activities,
            'monthly_chart' => $reportStats['monthly_chart'],
        ];
    }
}
