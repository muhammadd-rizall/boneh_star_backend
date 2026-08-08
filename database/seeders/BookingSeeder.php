<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\ActivityType;
use App\Models\RouteType;
use App\Models\Buses;
use App\Models\Driver;
use App\Models\User;
use App\Models\PaymentMethod;
use App\Models\Booking;
use App\Models\BookingBus;
use App\Models\Quotation;
use App\Models\Transaction;
use App\Models\Payment;
use App\Models\Review;
use App\Models\Notification;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Fetch dependent data
        $customers = Customer::all();
        $activities = ActivityType::all();
        $routes = RouteType::all();
        $buses = Buses::all();
        $drivers = Driver::all();
        $admins = User::all();
        $paymentMethods = PaymentMethod::all();

        if ($customers->isEmpty() || $activities->isEmpty() || $routes->isEmpty() || $buses->isEmpty() || $drivers->isEmpty() || $admins->isEmpty() || $paymentMethods->isEmpty()) {
            $this->command->error('Make sure all core tables (Customers, Buses, Users, RouteTypes, etc.) are seeded first.');
            return;
        }

        $origins = ['Surabaya', 'Malang', 'Sidoarjo', 'Gresik', 'Mojokerto'];
        $destinations = [
            'Banyuwangi' => 'Surabaya - Probolinggo - Jember - Banyuwangi',
            'Bali' => 'Surabaya - Ketapang - Gilimanuk - Denpasar',
            'Yogyakarta' => 'Malang - Solo - Yogyakarta - Candi Borobudur',
            'Bandung' => 'Surabaya - Semarang - Cirebon - Bandung',
            'Jakarta' => 'Surabaya - Tol Trans Jawa - Jakarta'
        ];

        $bookingIndex = 1;

        // -------------------------------------------------------------
        // 1. SEED PENDING BOOKINGS (3 Records)
        // -------------------------------------------------------------
        for ($i = 0; $i < 3; $i++) {
            $customer = $customers->random();
            $activity = $activities->random();
            $routeType = $routes->random();
            $origin = $faker->randomElement($origins);
            $destination = $faker->randomKey($destinations);
            $fullRoute = $destinations[$destination];
            $departureDate = now()->addDays(rand(10, 30));
            $returnDate = (clone $departureDate)->addDays(rand(2, 5));

            $booking = Booking::create([
                'customer_id' => $customer->id,
                'activity_id' => $activity->id,
                'route_type_id' => $routeType->id,
                'booking_code' => 'BK-' . now()->format('Ymd') . '-' . str_pad($bookingIndex++, 4, '0', STR_PAD_LEFT),
                'departure_date' => $departureDate,
                'return_date' => $returnDate,
                'origin' => $origin,
                'full_route' => $fullRoute,
                'passenger_count' => rand(20, 100),
                'bus_count_requested' => rand(1, 3),
                'special_notes' => $faker->optional(0.7)->sentence(),
                'status' => 'pending',
            ]);

            // Add notification to admin about new booking
            Notification::create([
                'target_type' => 'admin',
                'target_id' => $admins->random()->id,
                'title' => 'Pemesanan Baru Masuk',
                'message' => "Customer {$customer->name} mengajukan sewa bus untuk rute {$origin} ke {$destination}.",
                'type' => 'booking',
                'reference_type' => Booking::class,
                'reference_id' => $booking->id,
                'is_read' => false
            ]);
        }

        // -------------------------------------------------------------
        // 2. SEED QUOTED BOOKINGS (3 Records)
        // -------------------------------------------------------------
        $quoteStatuses = ['sent', 'revision', 'rejected'];
        foreach ($quoteStatuses as $qStatus) {
            $customer = $customers->random();
            $activity = $activities->random();
            $routeType = $routes->random();
            $origin = $faker->randomElement($origins);
            $destination = $faker->randomKey($destinations);
            $fullRoute = $destinations[$destination];
            $departureDate = now()->addDays(rand(10, 30));
            $returnDate = (clone $departureDate)->addDays(rand(2, 5));
            $busCount = rand(1, 2);

            $booking = Booking::create([
                'customer_id' => $customer->id,
                'activity_id' => $activity->id,
                'route_type_id' => $routeType->id,
                'booking_code' => 'BK-' . now()->format('Ymd') . '-' . str_pad($bookingIndex++, 4, '0', STR_PAD_LEFT),
                'departure_date' => $departureDate,
                'return_date' => $returnDate,
                'origin' => $origin,
                'full_route' => $fullRoute,
                'passenger_count' => $busCount * 45,
                'bus_count_requested' => $busCount,
                'special_notes' => $faker->optional(0.7)->sentence(),
                'status' => 'quoted',
            ]);

            $pricePerBus = rand(2500000, 4500000);
            $initialPrice = $busCount * $pricePerBus;
            $proposedPrice = $qStatus === 'revision' ? $initialPrice - 300000 : $initialPrice;
            $dpAmount = $proposedPrice * 0.3; // 30% DP

            $quotation = Quotation::create([
                'booking_id' => $booking->id,
                'user_id' => $admins->random()->id,
                'initial_price' => $initialPrice,
                'proposed_price' => $proposedPrice,
                'dp_amount' => $dpAmount,
                'note_to_customer' => 'Harga penawaran premium untuk perjalanan Anda.',
                'status' => $qStatus,
                'valid_until' => now()->addDays(7),
                'revision_note' => $qStatus === 'revision' ? 'Customer meminta potongan harga sebesar Rp 300.000' : null,
            ]);

            // Notifications
            if ($qStatus === 'sent') {
                Notification::create([
                    'target_type' => 'customer',
                    'target_id' => $customer->id,
                    'title' => 'Penawaran Harga Baru',
                    'message' => 'Admin telah mengirimkan penawaran harga untuk pemesanan Anda.',
                    'type' => 'booking',
                    'reference_type' => Booking::class,
                    'reference_id' => $booking->id,
                    'is_read' => false
                ]);
            } elseif ($qStatus === 'revision') {
                Notification::create([
                    'target_type' => 'admin',
                    'target_id' => $admins->random()->id,
                    'title' => 'Permintaan Negosiasi Harga',
                    'message' => "Customer {$customer->name} mengajukan negosiasi harga untuk booking {$booking->booking_code}.",
                    'type' => 'booking',
                    'reference_type' => Booking::class,
                    'reference_id' => $booking->id,
                    'is_read' => false
                ]);
            }
        }

        // -------------------------------------------------------------
        // 3. SEED CONFIRMED BOOKINGS (3 Records)
        // -------------------------------------------------------------
        // Confirmed bookings have transactions, bus assignments, and DP payments.
        // We will seed:
        // - 1 waiting DP payment
        // - 1 DP paid pending verification
        // - 1 DP paid and verified (fully confirmed)
        $confirmedSubTypes = ['waiting_payment', 'pending_verification', 'verified'];
        foreach ($confirmedSubTypes as $subType) {
            $customer = $customers->random();
            $activity = $activities->random();
            $routeType = $routes->random();
            $origin = $faker->randomElement($origins);
            $destination = $faker->randomKey($destinations);
            $fullRoute = $destinations[$destination];
            $departureDate = now()->addDays(rand(10, 30));
            $returnDate = (clone $departureDate)->addDays(rand(2, 5));
            $busCount = rand(1, 2);

            $booking = Booking::create([
                'customer_id' => $customer->id,
                'activity_id' => $activity->id,
                'route_type_id' => $routeType->id,
                'booking_code' => 'BK-' . now()->format('Ymd') . '-' . str_pad($bookingIndex++, 4, '0', STR_PAD_LEFT),
                'departure_date' => $departureDate,
                'return_date' => $returnDate,
                'origin' => $origin,
                'full_route' => $fullRoute,
                'passenger_count' => $busCount * 45,
                'bus_count_requested' => $busCount,
                'status' => 'confirmed',
            ]);

            $admin = $admins->random();
            $pricePerBus = rand(3000000, 4500000);
            $totalAmount = $busCount * $pricePerBus;
            $dpAmount = $totalAmount * 0.3;

            $quotation = Quotation::create([
                'booking_id' => $booking->id,
                'user_id' => $admin->id,
                'initial_price' => $totalAmount,
                'proposed_price' => $totalAmount,
                'dp_amount' => $dpAmount,
                'note_to_customer' => 'Penawaran harga disetujui.',
                'status' => 'accepted',
                'valid_until' => now()->addDays(3),
            ]);

            // Assign Bus & Driver
            $selectedBuses = $buses->random($busCount);
            $selectedDrivers = $drivers->random($busCount);

            foreach ($selectedBuses as $index => $bus) {
                BookingBus::create([
                    'booking_id' => $booking->id,
                    'bus_id' => $bus->id,
                    'driver_id' => $selectedDrivers[$index]->id,
                    'seat_count' => 45,
                    'assignment_notes' => 'Armada Boneh Star siap berangkat.',
                ]);
            }

            // Create Transaction
            $transactionStatus = 'waiting';
            $dpPaid = 0;
            if ($subType === 'verified') {
                $transactionStatus = 'dp_paid';
                $dpPaid = $dpAmount;
            }

            $transaction = Transaction::create([
                'booking_id' => $booking->id,
                'quotation_id' => $quotation->id,
                'invoice_number' => 'INV-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6)),
                'total_amount' => $totalAmount,
                'dp_paid' => $dpPaid,
                'remaining_amount' => $totalAmount - $dpPaid,
                'status' => $transactionStatus,
            ]);

            // Seed Payments
            if ($subType === 'pending_verification') {
                Payment::create([
                    'transaction_id' => $transaction->id,
                    'payment_method_id' => $paymentMethods->random()->id,
                    'amount' => $dpAmount,
                    'payment_type' => 'dp',
                    'payment_date' => now()->subDay()->format('Y-m-d'),
                    'transfer_proof' => 'https://images.unsplash.com/photo-1554415707-6e8cfc93fe23?q=80&w=400',
                    'status' => 'pending',
                ]);

                Notification::create([
                    'target_type' => 'admin',
                    'target_id' => $admins->random()->id,
                    'title' => 'Pembayaran Masuk',
                    'message' => "Customer {$customer->name} mengunggah bukti bayar DP untuk invoice {$transaction->invoice_number}.",
                    'type' => 'payment',
                    'reference_type' => Booking::class,
                    'reference_id' => $booking->id,
                    'is_read' => false
                ]);
            } elseif ($subType === 'verified') {
                $payment = Payment::create([
                    'transaction_id' => $transaction->id,
                    'payment_method_id' => $paymentMethods->random()->id,
                    'verified_by' => $admin->id,
                    'amount' => $dpAmount,
                    'payment_type' => 'dp',
                    'payment_date' => now()->subDays(2)->format('Y-m-d'),
                    'transfer_proof' => 'https://images.unsplash.com/photo-1554415707-6e8cfc93fe23?q=80&w=400',
                    'status' => 'verified',
                    'verified_at' => now()->subDays(2),
                ]);

                Notification::create([
                    'target_type' => 'customer',
                    'target_id' => $customer->id,
                    'title' => 'Pembayaran Terverifikasi',
                    'message' => "Pembayaran DP Anda untuk invoice {$transaction->invoice_number} telah disetujui admin.",
                    'type' => 'payment',
                    'reference_type' => Booking::class,
                    'reference_id' => $booking->id,
                    'is_read' => false
                ]);
            }
        }

        // -------------------------------------------------------------
        // 4. SEED COMPLETED BOOKINGS (3 Records)
        // -------------------------------------------------------------
        // Fully completed, paid in full, and reviewed.
        for ($i = 0; $i < 3; $i++) {
            $customer = $customers->random();
            $activity = $activities->random();
            $routeType = $routes->random();
            $origin = $faker->randomElement($origins);
            $destination = $faker->randomKey($destinations);
            $fullRoute = $destinations[$destination];
            $departureDate = now()->subDays(rand(10, 30));
            $returnDate = (clone $departureDate)->addDays(rand(2, 5));
            $busCount = rand(1, 2);

            $booking = Booking::create([
                'customer_id' => $customer->id,
                'activity_id' => $activity->id,
                'route_type_id' => $routeType->id,
                'booking_code' => 'BK-' . now()->format('Ymd') . '-' . str_pad($bookingIndex++, 4, '0', STR_PAD_LEFT),
                'departure_date' => $departureDate,
                'return_date' => $returnDate,
                'origin' => $origin,
                'full_route' => $fullRoute,
                'passenger_count' => $busCount * 45,
                'bus_count_requested' => $busCount,
                'status' => 'completed',
            ]);

            $admin = $admins->random();
            $pricePerBus = rand(3000000, 4500000);
            $totalAmount = $busCount * $pricePerBus;
            $dpAmount = $totalAmount * 0.3;
            $settlementAmount = $totalAmount - $dpAmount;

            $quotation = Quotation::create([
                'booking_id' => $booking->id,
                'user_id' => $admin->id,
                'initial_price' => $totalAmount,
                'proposed_price' => $totalAmount,
                'dp_amount' => $dpAmount,
                'note_to_customer' => 'Layanan VIP Boneh Star.',
                'status' => 'accepted',
                'valid_until' => $departureDate->subDays(5),
            ]);

            // Assign Bus & Driver
            $selectedBuses = $buses->random($busCount);
            $selectedDrivers = $drivers->random($busCount);

            foreach ($selectedBuses as $index => $bus) {
                BookingBus::create([
                    'booking_id' => $booking->id,
                    'bus_id' => $bus->id,
                    'driver_id' => $selectedDrivers[$index]->id,
                    'seat_count' => 45,
                    'assignment_notes' => 'Tugas study tour diselesaikan dengan baik.',
                ]);
            }

            // Create Transaction
            $transaction = Transaction::create([
                'booking_id' => $booking->id,
                'quotation_id' => $quotation->id,
                'invoice_number' => 'INV-' . $departureDate->format('Ymd') . '-' . Str::upper(Str::random(6)),
                'total_amount' => $totalAmount,
                'dp_paid' => $dpAmount,
                'remaining_amount' => 0,
                'status' => 'fully_paid',
            ]);

            // DP Payment
            Payment::create([
                'transaction_id' => $transaction->id,
                'payment_method_id' => $paymentMethods->random()->id,
                'verified_by' => $admin->id,
                'amount' => $dpAmount,
                'payment_type' => 'dp',
                'payment_date' => $departureDate->subDays(7)->format('Y-m-d'),
                'transfer_proof' => 'https://images.unsplash.com/photo-1554415707-6e8cfc93fe23?q=80&w=400',
                'status' => 'verified',
                'verified_at' => $departureDate->subDays(7),
            ]);

            // Settlement Payment
            Payment::create([
                'transaction_id' => $transaction->id,
                'payment_method_id' => $paymentMethods->random()->id,
                'verified_by' => $admin->id,
                'amount' => $settlementAmount,
                'payment_type' => 'settlement',
                'payment_date' => $departureDate->subDays(2)->format('Y-m-d'),
                'transfer_proof' => 'https://images.unsplash.com/photo-1554415707-6e8cfc93fe23?q=80&w=400',
                'status' => 'verified',
                'verified_at' => $departureDate->subDays(2),
            ]);

            // Review
            Review::create([
                'booking_id' => $booking->id,
                'customer_id' => $customer->id,
                'rating' => rand(4, 5),
                'comment' => $faker->randomElement([
                    'Bus sangat bersih, pelayanan driver sangat profesional dan ramah sepanjang perjalanan.',
                    'Perjalanan menyenangkan, AC dingin, suspensi bus empuk sekali.',
                    'Sangat direkomendasikan untuk sewa bus pariwisata keluarga maupun instansi!',
                    'Pelayanan memuaskan, armada terawat dengan baik.'
                ]),
                'is_publish' => true,
            ]);
        }

        // -------------------------------------------------------------
        // 5. SEED CANCELLED BOOKINGS (2 Records)
        // -------------------------------------------------------------
        for ($i = 0; $i < 2; $i++) {
            $customer = $customers->random();
            $activity = $activities->random();
            $routeType = $routes->random();
            $origin = $faker->randomElement($origins);
            $destination = $faker->randomKey($destinations);
            $fullRoute = $destinations[$destination];
            $departureDate = now()->addDays(rand(5, 15));
            $returnDate = (clone $departureDate)->addDays(rand(2, 5));

            Booking::create([
                'customer_id' => $customer->id,
                'activity_id' => $activity->id,
                'route_type_id' => $routeType->id,
                'booking_code' => 'BK-' . now()->format('Ymd') . '-' . str_pad($bookingIndex++, 4, '0', STR_PAD_LEFT),
                'departure_date' => $departureDate,
                'return_date' => $returnDate,
                'origin' => $origin,
                'full_route' => $fullRoute,
                'passenger_count' => rand(30, 50),
                'bus_count_requested' => 1,
                'status' => 'cancelled',
                'cancelled_by' => $i === 0 ? 'customer' : 'admin',
                'cancelled_reason' => $i === 0 ? 'Rencana perjalanan keluarga dibatalkan mendadak.' : 'Jadwal armada bertabrakan dengan perawatan rutin.',
            ]);
        }

        $this->command->info('Bookings, Quotations, Transactions, Payments, Reviews, and Notifications workflow seeded successfully!');
    }
}
