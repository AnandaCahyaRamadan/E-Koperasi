<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use App\Models\Role;
use App\Models\User;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Delivery;
use App\Models\StockMovement;
use App\Models\StockOpname;
use App\Models\OpnameDetail;
use App\Models\Setting;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---------------------------------------------------------
        // 0. GENERATE SAMPLE IMAGES (Payment Proofs & QRIS)
        // ---------------------------------------------------------
        $proofDir = storage_path('app/public/payment_proofs');
        $qrisDir = storage_path('app/public/qris');
        File::ensureDirectoryExists($proofDir);
        File::ensureDirectoryExists($qrisDir);

        $this->generateSampleProof($proofDir . '/proof_sample_1.jpg', 'BUKTI TRANSFER QRIS', 'Rp 27.000', 'Budi Santoso');
        $this->generateSampleProof($proofDir . '/proof_sample_2.jpg', 'TRANSFER BANK MANDIRI QRIS', 'Rp 45.000', 'Dewi Lestari');
        $this->generateSampleProof($proofDir . '/proof_sample_3.jpg', 'BUKTI TRANSFER GOPAY QRIS', 'Rp 16.000', 'Ahmad Fauzi');

        // ---------------------------------------------------------
        // 1. ROLES
        // ---------------------------------------------------------
        $adminRole = Role::firstOrCreate(['slug' => 'admin'], [
            'name' => 'Administrator',
            'description' => 'Akses penuh ke semua modul sistem koperasi, stok opname, dan laporan.',
        ]);

        $petugasRole = Role::firstOrCreate(['slug' => 'petugas'], [
            'name' => 'Petugas / Kasir',
            'description' => 'Akses ke POS kasir, verifikasi pembayaran, persiapan dan pengantaran pesanan.',
        ]);

        $pelangganRole = Role::firstOrCreate(['slug' => 'pelanggan'], [
            'name' => 'Pelanggan / Pegawai',
            'description' => 'Akses belanja katalog e-koperasi, pemesanan, dan upload bukti QRIS.',
        ]);

        // ---------------------------------------------------------
        // 2. USERS
        // ---------------------------------------------------------
        $admin = User::firstOrCreate(['email' => 'admin@koperasi.com'], [
            'role_id' => $adminRole->id,
            'name' => 'Administrator Koperasi',
            'phone' => '081234567890',
            'employee_id' => 'ADM-001',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $petugas1 = User::firstOrCreate(['email' => 'petugas@koperasi.com'], [
            'role_id' => $petugasRole->id,
            'name' => 'Kasir Operasional',
            'phone' => '081234567891',
            'employee_id' => 'KAS-002',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $petugas2 = User::firstOrCreate(['email' => 'siti@koperasi.com'], [
            'role_id' => $petugasRole->id,
            'name' => 'Siti Nurhaliza (Kasir)',
            'phone' => '081234567899',
            'employee_id' => 'KAS-003',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $pelanggan1 = User::firstOrCreate(['email' => 'pegawai@koperasi.com'], [
            'role_id' => $pelangganRole->id,
            'name' => 'Budi Santoso',
            'phone' => '081234567892',
            'employee_id' => 'PEG-10492',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $pelanggan2 = User::firstOrCreate(['email' => 'dewi@koperasi.com'], [
            'role_id' => $pelangganRole->id,
            'name' => 'Dewi Lestari (Keuangan)',
            'phone' => '081234567893',
            'employee_id' => 'PEG-10493',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $pelanggan3 = User::firstOrCreate(['email' => 'ahmad@koperasi.com'], [
            'role_id' => $pelangganRole->id,
            'name' => 'Ahmad Fauzi (IT & Kominfo)',
            'phone' => '081234567894',
            'employee_id' => 'PEG-10494',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $pelanggan4 = User::firstOrCreate(['email' => 'rina@koperasi.com'], [
            'role_id' => $pelangganRole->id,
            'name' => 'Rina Marlina (Kepegawaian)',
            'phone' => '081234567895',
            'employee_id' => 'PEG-10495',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $pelanggan5 = User::firstOrCreate(['email' => 'eko@koperasi.com'], [
            'role_id' => $pelangganRole->id,
            'name' => 'Eko Prasetyo (Umum)',
            'phone' => '081234567896',
            'employee_id' => 'PEG-10496',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        // ---------------------------------------------------------
        // 3. SETTINGS
        // ---------------------------------------------------------
        $settings = [
            'koperasi_name' => ['E-Koperasi Karyawan Sejahtera', 'general', 'Nama Resmi Koperasi'],
            'koperasi_address' => ['Gedung Kantor Pusat Lt. 1, Ruang Koperasi Pegawai', 'general', 'Alamat Koperasi'],
            'koperasi_phone' => ['(021) 555-0199 / WA 081234567891', 'general', 'Kontak WhatsApp / Telepon'],
            'koperasi_email' => ['koperasi@kantor.go.id', 'general', 'Email Resmi'],
            'qris_merchant_name' => ['KOPERASI PEGAWAI KANTOR SEJAHTERA', 'payment', 'Nama Merchant QRIS'],
            'qris_nmid' => ['ID1020030040506', 'payment', 'NMID QRIS'],
            'order_auto_cancel_hours' => ['24', 'order', 'Batas Waktu Pembayaran (Jam)'],
        ];

        foreach ($settings as $k => $v) {
            Setting::updateOrCreate(['key' => $k], ['value' => $v[0], 'group' => $v[1], 'description' => $v[2]]);
        }

        // ---------------------------------------------------------
        // 4. UNITS
        // ---------------------------------------------------------
        $unitsData = [
            ['name' => 'Pieces / Buah', 'symbol' => 'Pcs', 'description' => 'Satuan per biji / satuan'],
            ['name' => 'Botol', 'symbol' => 'Btl', 'description' => 'Satuan botol kemasan'],
            ['name' => 'Bungkus / Pack', 'symbol' => 'Bks', 'description' => 'Kemasan bungkus'],
            ['name' => 'Kotak / Box', 'symbol' => 'Box', 'description' => 'Kemasan kardus / kotak'],
            ['name' => 'Rim', 'symbol' => 'Rim', 'description' => '500 lembar kertas'],
            ['name' => 'Kaleng', 'symbol' => 'Klg', 'description' => 'Kemasan kaleng'],
            ['name' => 'Cup', 'symbol' => 'Cup', 'description' => 'Kemasan cup / gelas'],
            ['name' => 'Sachet', 'symbol' => 'Scht', 'description' => 'Kemasan sachet kecil'],
        ];
        $units = [];
        foreach ($unitsData as $u) {
            $units[$u['symbol']] = Unit::firstOrCreate(['symbol' => $u['symbol']], $u);
        }

        // ---------------------------------------------------------
        // 5. CATEGORIES
        // ---------------------------------------------------------
        $categoriesData = [
            ['name' => 'Makanan & Snack', 'slug' => 'makanan-snack', 'icon' => 'bi-basket2', 'description' => 'Aneka camilan, roti, dan snack ringan santai'],
            ['name' => 'Minuman Segar', 'slug' => 'minuman-segar', 'icon' => 'bi-cup-hot', 'description' => 'Air mineral, kopi, teh, susu, dan minuman dingin'],
            ['name' => 'Kebutuhan ATK', 'slug' => 'kebutuhan-atk', 'icon' => 'bi-pencil-square', 'description' => 'Kertas HVS, pulpen, spidol, buku, dan binder'],
            ['name' => 'Sembako & Kebutuhan Harian', 'slug' => 'sembako-harian', 'icon' => 'bi-cart-check', 'description' => 'Minyak goreng, gula pasir, mie instan, teh celup'],
            ['name' => 'Kebersihan & Pribadi', 'slug' => 'kebersihan-pribadi', 'icon' => 'bi-shield-check', 'description' => 'Tisu, sabun cuci tangan, dan hand sanitizer'],
        ];
        $categories = [];
        foreach ($categoriesData as $c) {
            $categories[$c['slug']] = Category::firstOrCreate(['slug' => $c['slug']], $c);
        }

        // ---------------------------------------------------------
        // 6. SUPPLIERS
        // ---------------------------------------------------------
        $suppliersData = [
            [
                'code' => 'SUP-001',
                'name' => 'PT Sumber Pangan Sejahtera',
                'contact_person' => 'Hendra Setiawan',
                'phone' => '081298765432',
                'email' => 'sales@sumberpangan.co.id',
                'address' => 'Jl. Pergudangan Surya Blok B No. 12, Jakarta',
            ],
            [
                'code' => 'SUP-002',
                'name' => 'CV Mitra ATK Jaya',
                'contact_person' => 'Lina Marlina',
                'phone' => '081345678901',
                'email' => 'order@mitraatkjaya.com',
                'address' => 'Komp. Ruko Niaga Blok C No. 4, Jakarta Barat',
            ],
            [
                'code' => 'SUP-003',
                'name' => 'PT Tirta Niaga Nusantara',
                'contact_person' => 'Agus Priyono',
                'phone' => '081122334455',
                'email' => 'distribusi@tirtaniaga.com',
                'address' => 'Kawasan Industri Pulogadung No. 8, Jakarta Timur',
            ],
        ];
        $suppliers = [];
        foreach ($suppliersData as $s) {
            $suppliers[$s['code']] = Supplier::firstOrCreate(['code' => $s['code']], $s);
        }

        // ---------------------------------------------------------
        // 7. PRODUCTS (20+ Varied Realistic Products)
        // ---------------------------------------------------------
        $productsData = [
            // Minuman Segar
            [
                'code' => 'PRD-MIN-001',
                'name' => 'Le Minerale 600ml',
                'slug' => 'le-minerale-600ml',
                'category' => 'minuman-segar',
                'unit' => 'Btl',
                'purchase_price' => 2800,
                'sell_price' => 4000,
                'stock' => 95,
                'min_stock' => 20,
                'description' => 'Air mineral segar dengan mineral alami yang higienis.',
            ],
            [
                'code' => 'PRD-MIN-002',
                'name' => 'Kopi Kapal Api Spesial Mix 10x24g',
                'slug' => 'kopi-kapal-api-mix',
                'category' => 'minuman-segar',
                'unit' => 'Bks',
                'purchase_price' => 12500,
                'sell_price' => 15000,
                'stock' => 40,
                'min_stock' => 10,
                'description' => 'Kopi bubuk instan perpaduan biji kopi pilihan dan gula murni.',
            ],
            [
                'code' => 'PRD-MIN-003',
                'name' => 'Teh Botol Sosro Kotak 250ml',
                'slug' => 'teh-botol-sosro-250ml',
                'category' => 'minuman-segar',
                'unit' => 'Pcs',
                'purchase_price' => 3400,
                'sell_price' => 5000,
                'stock' => 65,
                'min_stock' => 15,
                'description' => 'Teh melati asli dalam kemasan kotak yang praktis.',
            ],
            [
                'code' => 'PRD-MIN-004',
                'name' => 'Ultra Milk Cokelat 250ml',
                'slug' => 'ultra-milk-cokelat-250ml',
                'category' => 'minuman-segar',
                'unit' => 'Pcs',
                'purchase_price' => 5800,
                'sell_price' => 7500,
                'stock' => 45,
                'min_stock' => 12,
                'description' => 'Susu sapi segar UHT rasa cokelat lezat berprotein tinggi.',
            ],
            [
                'code' => 'PRD-MIN-005',
                'name' => 'Pocari Sweat Botol 500ml',
                'slug' => 'pocari-sweat-500ml',
                'category' => 'minuman-segar',
                'unit' => 'Btl',
                'purchase_price' => 6800,
                'sell_price' => 9000,
                'stock' => 30,
                'min_stock' => 10,
                'description' => 'Minuman isotonik pengganti ion tubuh yang hilang.',
            ],
            [
                'code' => 'PRD-MIN-006',
                'name' => 'Good Day Cappuccino 250ml Botol',
                'slug' => 'good-day-cappuccino-botol',
                'category' => 'minuman-segar',
                'unit' => 'Btl',
                'purchase_price' => 6200,
                'sell_price' => 8000,
                'stock' => 35,
                'min_stock' => 8,
                'description' => 'Kopi dingin siap minum dengan rasa cappuccino lembut.',
            ],

            // Makanan & Snack
            [
                'code' => 'PRD-SNK-001',
                'name' => 'Chitato Sapi Panggang 68g',
                'slug' => 'chitato-sapi-panggang-68g',
                'category' => 'makanan-snack',
                'unit' => 'Bks',
                'purchase_price' => 9500,
                'sell_price' => 12000,
                'stock' => 38,
                'min_stock' => 10,
                'description' => 'Keripik kentang bergelombang rasa sapi panggang khas.',
            ],
            [
                'code' => 'PRD-SNK-002',
                'name' => 'Beng-Beng Wafer Cokelat 3x20g',
                'slug' => 'beng-beng-wafer-cokelat',
                'category' => 'makanan-snack',
                'unit' => 'Bks',
                'purchase_price' => 6000,
                'sell_price' => 8000,
                'stock' => 55,
                'min_stock' => 15,
                'description' => 'Wafer lapis karamel, rice crispies, dan cokelat legit.',
            ],
            [
                'code' => 'PRD-SNK-003',
                'name' => 'Sari Roti Sobek Cokelat',
                'slug' => 'sari-roti-sobek-cokelat',
                'category' => 'makanan-snack',
                'unit' => 'Bks',
                'purchase_price' => 13500,
                'sell_price' => 16500,
                'stock' => 12,
                'min_stock' => 5,
                'description' => 'Roti sobek empuk isi selai cokelat pekat.',
            ],
            [
                'code' => 'PRD-SNK-004',
                'name' => 'Oreo Sandwich Vanilla 133g',
                'slug' => 'oreo-sandwich-vanilla-133g',
                'category' => 'makanan-snack',
                'unit' => 'Bks',
                'purchase_price' => 8800,
                'sell_price' => 11000,
                'stock' => 40,
                'min_stock' => 10,
                'description' => 'Biskuit cokelat sandwich isi krim vanila nikmat.',
            ],
            [
                'code' => 'PRD-SNK-005',
                'name' => 'SilverQueen Cashew 58g',
                'slug' => 'silverqueen-cashew-58g',
                'category' => 'makanan-snack',
                'unit' => 'Pcs',
                'purchase_price' => 13000,
                'sell_price' => 16500,
                'stock' => 25,
                'min_stock' => 8,
                'description' => 'Cokelat batang premium bertabur kacang mede renyah.',
            ],

            // Kebutuhan ATK
            [
                'code' => 'PRD-ATK-001',
                'name' => 'Kertas PaperOne A4 75gsm 500 Lembar',
                'slug' => 'kertas-paperone-a4-75gsm',
                'category' => 'kebutuhan-atk',
                'unit' => 'Rim',
                'purchase_price' => 45000,
                'sell_price' => 52000,
                'stock' => 32,
                'min_stock' => 10,
                'description' => 'Kertas HVS putih mulus untuk fotokopi dan cetak dokumen resmi.',
            ],
            [
                'code' => 'PRD-ATK-002',
                'name' => 'Pulpen Standard AE7 0.5mm Hitam',
                'slug' => 'pulpen-standard-ae7-hitam',
                'category' => 'kebutuhan-atk',
                'unit' => 'Pcs',
                'purchase_price' => 2300,
                'sell_price' => 3500,
                'stock' => 115,
                'min_stock' => 25,
                'description' => 'Pulpen tinta lancar dan tahan lama untuk penulisan berkas kantor.',
            ],
            [
                'code' => 'PRD-ATK-003',
                'name' => 'Sticky Notes Post-it 3x3 Inch Warna',
                'slug' => 'sticky-notes-post-it',
                'category' => 'kebutuhan-atk',
                'unit' => 'Pcs',
                'purchase_price' => 8500,
                'sell_price' => 11000,
                'stock' => 34,
                'min_stock' => 8,
                'description' => 'Memo kertas perekat warna-warni untuk catatan pengingat.',
            ],
            [
                'code' => 'PRD-ATK-004',
                'name' => 'Spidol Whiteboard Snowman Hitam',
                'slug' => 'spidol-whiteboard-snowman',
                'category' => 'kebutuhan-atk',
                'unit' => 'Pcs',
                'purchase_price' => 8000,
                'sell_price' => 10000,
                'stock' => 28,
                'min_stock' => 8,
                'description' => 'Spidol papan tulis hitam yang mudah dihapus tanpa noda.',
            ],
            [
                'code' => 'PRD-ATK-005',
                'name' => 'Binder Clip Kenko No. 107 (Kotak 12 Pcs)',
                'slug' => 'binder-clip-kenko-107',
                'category' => 'kebutuhan-atk',
                'unit' => 'Box',
                'purchase_price' => 7000,
                'sell_price' => 9500,
                'stock' => 22,
                'min_stock' => 5,
                'description' => 'Penjepit kertas baja kuat ukuran 19mm.',
            ],

            // Sembako & Harian
            [
                'code' => 'PRD-SMB-001',
                'name' => 'Indomie Goreng Spesial 85g',
                'slug' => 'indomie-goreng-spesial-85g',
                'category' => 'sembako-harian',
                'unit' => 'Bks',
                'purchase_price' => 2900,
                'sell_price' => 3800,
                'stock' => 140,
                'min_stock' => 30,
                'description' => 'Mi instan goreng favorit legendaris dengan bawang goreng gurih.',
            ],
            [
                'code' => 'PRD-SMB-002',
                'name' => 'Minyak Goreng Bimoli 1 Liter Pouch',
                'slug' => 'minyak-goreng-bimoli-1l',
                'category' => 'sembako-harian',
                'unit' => 'Pcs',
                'purchase_price' => 17500,
                'sell_price' => 21000,
                'stock' => 24,
                'min_stock' => 8,
                'description' => 'Minyak kelapa sawit murni higienis untuk memasak renyah.',
            ],
            [
                'code' => 'PRD-SMB-003',
                'name' => 'Gula Pasir Gulaku Premium 1kg',
                'slug' => 'gula-pasir-gulaku-1kg',
                'category' => 'sembako-harian',
                'unit' => 'Pcs',
                'purchase_price' => 16500,
                'sell_price' => 19500,
                'stock' => 20,
                'min_stock' => 6,
                'description' => 'Gula tebu murni kristal putih manis alami.',
            ],
            [
                'code' => 'PRD-SMB-004',
                'name' => 'Teh Celup SariWangi Asli Kotak 25s',
                'slug' => 'teh-celup-sariwangi-25s',
                'category' => 'sembako-harian',
                'unit' => 'Box',
                'purchase_price' => 6500,
                'sell_price' => 8500,
                'stock' => 35,
                'min_stock' => 10,
                'description' => 'Teh celup aroma melati pilihan khas Indonesia.',
            ],

            // Kebersihan & Pribadi
            [
                'code' => 'PRD-KBR-001',
                'name' => 'Tisu Paseo Soft Pack 250 Lembar 2 Ply',
                'slug' => 'tisu-paseo-softpack-250s',
                'category' => 'kebersihan-pribadi',
                'unit' => 'Pcs',
                'purchase_price' => 11500,
                'sell_price' => 14500,
                'stock' => 45,
                'min_stock' => 10,
                'description' => 'Tisu wajah higienis berserat alami yang lembut di kulit.',
            ],
            [
                'code' => 'PRD-KBR-002',
                'name' => 'Hand Sanitizer Dettol 50ml Gel',
                'slug' => 'hand-sanitizer-dettol-50ml',
                'category' => 'kebersihan-pribadi',
                'unit' => 'Btl',
                'purchase_price' => 10500,
                'sell_price' => 13500,
                'stock' => 22,
                'min_stock' => 8,
                'description' => 'Pembersih tangan antiseptik praktis membunuh 99.9% kuman.',
            ],
            [
                'code' => 'PRD-KBR-003',
                'name' => 'Sabun Cuci Tangan Lifebuoy 180ml Refill',
                'slug' => 'sabun-tangan-lifebuoy-180ml',
                'category' => 'kebersihan-pribadi',
                'unit' => 'Pcs',
                'purchase_price' => 9000,
                'sell_price' => 12000,
                'stock' => 18,
                'min_stock' => 6,
                'description' => 'Sabun cuci tangan cair antibakteri menjaga higienitas ruangan kantor.',
            ],
        ];

        $products = [];
        foreach ($productsData as $p) {
            $cat = $categories[$p['category']] ?? null;
            $unit = $units[$p['unit']] ?? null;

            $product = Product::updateOrCreate(
                ['code' => $p['code']],
                [
                    'category_id' => $cat ? $cat->id : null,
                    'unit_id' => $unit ? $unit->id : null,
                    'name' => $p['name'],
                    'slug' => $p['slug'],
                    'purchase_price' => $p['purchase_price'],
                    'sell_price' => $p['sell_price'],
                    'stock' => $p['stock'],
                    'min_stock' => $p['min_stock'],
                    'description' => $p['description'],
                    'is_active' => true,
                ]
            );

            $products[$p['code']] = $product;

            // Catat initial movement jika belum ada
            if (!StockMovement::where('product_id', $product->id)->where('reference_type', 'initial')->exists()) {
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => $admin->id,
                    'type' => 'in',
                    'reference_type' => 'initial',
                    'quantity' => $product->stock,
                    'stock_before' => 0,
                    'stock_after' => $product->stock,
                    'notes' => 'Stok awal sistem e-koperasi',
                ]);
            }
        }

        // ---------------------------------------------------------
        // 8. PURCHASES (PO BARANG MASUK DARI SUPPLIER)
        // ---------------------------------------------------------
        if (Purchase::count() === 0) {
            // PO 1
            $po1 = Purchase::create([
                'purchase_number' => 'PO-' . date('Ymd', strtotime('-5 days')) . '-0001',
                'supplier_id' => $suppliers['SUP-001']->id,
                'user_id' => $admin->id,
                'purchase_date' => now()->subDays(5)->toDateString(),
                'total_amount' => 545000,
                'status' => 'received',
                'invoice_number' => 'INV/SPS/2026/1102',
                'notes' => 'Pasokan rutin mingguan snack dan mi instan',
                'created_at' => now()->subDays(5),
            ]);
            PurchaseItem::create(['purchase_id' => $po1->id, 'product_id' => $products['PRD-SNK-001']->id, 'quantity' => 20, 'unit_price' => 9500, 'subtotal' => 190000]);
            PurchaseItem::create(['purchase_id' => $po1->id, 'product_id' => $products['PRD-SMB-001']->id, 'quantity' => 50, 'unit_price' => 2900, 'subtotal' => 145000]);
            PurchaseItem::create(['purchase_id' => $po1->id, 'product_id' => $products['PRD-SMB-002']->id, 'quantity' => 12, 'unit_price' => 17500, 'subtotal' => 210000]);

            // PO 2
            $po2 = Purchase::create([
                'purchase_number' => 'PO-' . date('Ymd', strtotime('-3 days')) . '-0002',
                'supplier_id' => $suppliers['SUP-002']->id,
                'user_id' => $admin->id,
                'purchase_date' => now()->subDays(3)->toDateString(),
                'total_amount' => 680000,
                'status' => 'received',
                'invoice_number' => 'INV/MAJ/2026/894',
                'notes' => 'Pasokan kebutuhan ATK dan kertas printer',
                'created_at' => now()->subDays(3),
            ]);
            PurchaseItem::create(['purchase_id' => $po2->id, 'product_id' => $products['PRD-ATK-001']->id, 'quantity' => 10, 'unit_price' => 45000, 'subtotal' => 450000]);
            PurchaseItem::create(['purchase_id' => $po2->id, 'product_id' => $products['PRD-ATK-002']->id, 'quantity' => 100, 'unit_price' => 2300, 'subtotal' => 230000]);
        }

        // ---------------------------------------------------------
        // 9. REALISTIC SAMPLE ORDERS IN VARIOUS LIFECYCLE STATES
        // ---------------------------------------------------------
        if (Order::count() === 0) {
            // Case 1: Pending Payment dengan Upload Bukti QRIS (Waiting Verification) -> Siap di-ACC atau Ditolak oleh Petugas
            $ord1 = Order::create([
                'order_number' => 'ORD-' . date('Ymd') . '-0001',
                'user_id' => $pelanggan1->id,
                'order_type' => 'pickup',
                'total_amount' => 27000,
                'discount_amount' => 0,
                'final_amount' => 27000,
                'status' => 'pending_payment',
                'payment_status' => 'waiting_verification',
                'payment_method' => 'qris',
                'notes' => 'Saya ambil siang jam istirahat ya',
                'created_at' => now()->subMinutes(25),
            ]);
            OrderItem::create(['order_id' => $ord1->id, 'product_id' => $products['PRD-MIN-001']->id, 'product_name' => $products['PRD-MIN-001']->name, 'product_code' => $products['PRD-MIN-001']->code, 'price' => 4000, 'quantity' => 3, 'subtotal' => 12000]);
            OrderItem::create(['order_id' => $ord1->id, 'product_id' => $products['PRD-MIN-002']->id, 'product_name' => $products['PRD-MIN-002']->name, 'product_code' => $products['PRD-MIN-002']->code, 'price' => 15000, 'quantity' => 1, 'subtotal' => 15000]);
            Payment::create([
                'order_id' => $ord1->id,
                'user_id' => $pelanggan1->id,
                'payment_method' => 'qris',
                'amount' => 27000,
                'proof_image' => 'payment_proofs/proof_sample_1.jpg',
                'status' => 'waiting_verification',
                'notes' => 'Bukti bayar diunggah oleh pelanggan',
                'created_at' => now()->subMinutes(20),
            ]);
            $ord1->addStatusHistory('waiting_verification', 'Pelanggan telah mengunggah bukti pembayaran QRIS.', $pelanggan1->id);

            // Case 2: Pending Payment QRIS Waiting Verification (Order Type Delivery ke Meja)
            $ord2 = Order::create([
                'order_number' => 'ORD-' . date('Ymd') . '-0002',
                'user_id' => $pelanggan2->id,
                'order_type' => 'delivery',
                'total_amount' => 45000,
                'discount_amount' => 0,
                'final_amount' => 45000,
                'status' => 'pending_payment',
                'payment_status' => 'waiting_verification',
                'payment_method' => 'qris',
                'delivery_address' => 'Gedung A Lantai 3, Ruang Bagian Keuangan, Meja Dewi',
                'created_at' => now()->subMinutes(15),
            ]);
            OrderItem::create(['order_id' => $ord2->id, 'product_id' => $products['PRD-SNK-003']->id, 'product_name' => $products['PRD-SNK-003']->name, 'product_code' => $products['PRD-SNK-003']->code, 'price' => 16500, 'quantity' => 2, 'subtotal' => 33000]);
            OrderItem::create(['order_id' => $ord2->id, 'product_id' => $products['PRD-SNK-001']->id, 'product_name' => $products['PRD-SNK-001']->name, 'product_code' => $products['PRD-SNK-001']->code, 'price' => 12000, 'quantity' => 1, 'subtotal' => 12000]);
            Payment::create([
                'order_id' => $ord2->id,
                'user_id' => $pelanggan2->id,
                'payment_method' => 'qris',
                'amount' => 45000,
                'proof_image' => 'payment_proofs/proof_sample_2.jpg',
                'status' => 'waiting_verification',
                'created_at' => now()->subMinutes(12),
            ]);
            Delivery::create([
                'order_id' => $ord2->id,
                'recipient_name' => 'Dewi Lestari',
                'recipient_phone' => '081234567893',
                'delivery_address' => 'Gedung A Lantai 3, Ruang Bagian Keuangan, Meja Dewi',
                'status' => 'preparing',
            ]);
            $ord2->addStatusHistory('waiting_verification', 'Menunggu verifikasi bukti QRIS oleh kasir.', $pelanggan2->id);

            // Case 3: Pembayaran Lunas, Status CONFIRMED (Siap untuk disiapkan petugas kasir)
            $ord3 = Order::create([
                'order_number' => 'ORD-' . date('Ymd') . '-0003',
                'user_id' => $pelanggan3->id,
                'order_type' => 'pickup',
                'total_amount' => 52000,
                'discount_amount' => 0,
                'final_amount' => 52000,
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'payment_method' => 'qris',
                'paid_at' => now()->subMinutes(40),
                'created_at' => now()->subMinutes(45),
            ]);
            OrderItem::create(['order_id' => $ord3->id, 'product_id' => $products['PRD-ATK-001']->id, 'product_name' => $products['PRD-ATK-001']->name, 'product_code' => $products['PRD-ATK-001']->code, 'price' => 52000, 'quantity' => 1, 'subtotal' => 52000]);
            Payment::create([
                'order_id' => $ord3->id,
                'user_id' => $pelanggan3->id,
                'verified_by' => $petugas1->id,
                'payment_method' => 'qris',
                'amount' => 52000,
                'status' => 'paid',
                'verified_at' => now()->subMinutes(40),
            ]);
            $ord3->addStatusHistory('confirmed', 'Pembayaran QRIS telah diverifikasi lunas oleh kasir.', $petugas1->id);

            // Case 4: Status PROCESSING (Sedang Disiapkan)
            $ord4 = Order::create([
                'order_number' => 'ORD-' . date('Ymd') . '-0004',
                'user_id' => $pelanggan4->id,
                'order_type' => 'delivery',
                'total_amount' => 38000,
                'discount_amount' => 0,
                'final_amount' => 38000,
                'status' => 'processing',
                'payment_status' => 'paid',
                'payment_method' => 'cash',
                'delivery_address' => 'Gedung B Lantai 2, Ruang Kepegawaian, Meja Rina',
                'paid_at' => now()->subMinutes(50),
                'created_at' => now()->subMinutes(60),
            ]);
            OrderItem::create(['order_id' => $ord4->id, 'product_id' => $products['PRD-SMB-001']->id, 'product_name' => $products['PRD-SMB-001']->name, 'product_code' => $products['PRD-SMB-001']->code, 'price' => 3800, 'quantity' => 5, 'subtotal' => 19000]);
            OrderItem::create(['order_id' => $ord4->id, 'product_id' => $products['PRD-SMB-003']->id, 'product_name' => $products['PRD-SMB-003']->name, 'product_code' => $products['PRD-SMB-003']->code, 'price' => 19000, 'quantity' => 1, 'subtotal' => 19000]);
            Payment::create(['order_id' => $ord4->id, 'user_id' => $pelanggan4->id, 'verified_by' => $petugas1->id, 'payment_method' => 'cash', 'amount' => 38000, 'status' => 'paid', 'verified_at' => now()->subMinutes(50)]);
            Delivery::create(['order_id' => $ord4->id, 'recipient_name' => 'Rina Marlina', 'recipient_phone' => '081234567895', 'delivery_address' => 'Gedung B Lantai 2, Ruang Kepegawaian, Meja Rina', 'status' => 'preparing']);
            $ord4->addStatusHistory('processing', 'Pesanan sedang disiapkan barangnya oleh petugas kasir.', $petugas1->id);

            // Case 5: Status READY FOR PICKUP (Siap Diambil di Kasir)
            $ord5 = Order::create([
                'order_number' => 'ORD-' . date('Ymd') . '-0005',
                'user_id' => $pelanggan5->id,
                'order_type' => 'pickup',
                'total_amount' => 28000,
                'discount_amount' => 0,
                'final_amount' => 28000,
                'status' => 'ready_for_pickup',
                'payment_status' => 'paid',
                'payment_method' => 'cash',
                'paid_at' => now()->subHours(2),
                'created_at' => now()->subHours(2)->subMinutes(15),
            ]);
            OrderItem::create(['order_id' => $ord5->id, 'product_id' => $products['PRD-KBR-001']->id, 'product_name' => $products['PRD-KBR-001']->name, 'product_code' => $products['PRD-KBR-001']->code, 'price' => 14500, 'quantity' => 1, 'subtotal' => 14500]);
            OrderItem::create(['order_id' => $ord5->id, 'product_id' => $products['PRD-KBR-002']->id, 'product_name' => $products['PRD-KBR-002']->name, 'product_code' => $products['PRD-KBR-002']->code, 'price' => 13500, 'quantity' => 1, 'subtotal' => 13500]);
            Payment::create(['order_id' => $ord5->id, 'user_id' => $pelanggan5->id, 'verified_by' => $petugas2->id, 'payment_method' => 'cash', 'amount' => 28000, 'status' => 'paid', 'verified_at' => now()->subHours(2)]);
            $ord5->addStatusHistory('ready_for_pickup', 'Barang telah dipaketkan dan siap diambil di meja kasir.', $petugas2->id);

            // Case 6: Status ON DELIVERY (Sedang Diantar)
            $ord6 = Order::create([
                'order_number' => 'ORD-' . date('Ymd') . '-0006',
                'user_id' => $pelanggan1->id,
                'order_type' => 'delivery',
                'total_amount' => 31000,
                'discount_amount' => 0,
                'final_amount' => 31000,
                'status' => 'on_delivery',
                'payment_status' => 'paid',
                'payment_method' => 'qris',
                'delivery_address' => 'Gedung Utama Lantai 1, Ruang Bagian Perencanaan',
                'paid_at' => now()->subHours(1),
                'created_at' => now()->subHours(1)->subMinutes(20),
            ]);
            OrderItem::create(['order_id' => $ord6->id, 'product_id' => $products['PRD-ATK-003']->id, 'product_name' => $products['PRD-ATK-003']->name, 'product_code' => $products['PRD-ATK-003']->code, 'price' => 11000, 'quantity' => 1, 'subtotal' => 11000]);
            OrderItem::create(['order_id' => $ord6->id, 'product_id' => $products['PRD-ATK-004']->id, 'product_name' => $products['PRD-ATK-004']->name, 'product_code' => $products['PRD-ATK-004']->code, 'price' => 10000, 'quantity' => 2, 'subtotal' => 20000]);
            Payment::create(['order_id' => $ord6->id, 'user_id' => $pelanggan1->id, 'verified_by' => $petugas1->id, 'payment_method' => 'qris', 'amount' => 31000, 'status' => 'paid', 'verified_at' => now()->subHours(1)]);
            Delivery::create(['order_id' => $ord6->id, 'officer_id' => $petugas1->id, 'courier_name' => $petugas1->name, 'recipient_name' => 'Budi Santoso', 'recipient_phone' => '081234567892', 'delivery_address' => 'Gedung Utama Lantai 1, Ruang Bagian Perencanaan', 'status' => 'on_the_way']);
            $ord6->addStatusHistory('on_delivery', 'Pesanan sedang dibawa kurir petugas koperasi menuju ruangan.', $petugas1->id);

            // Case 7: POS Transaksi Kasir Langsung Selesai Hari Ini
            $pos1 = Order::create([
                'order_number' => 'POS-' . date('Ymd') . '-0001',
                'user_id' => null,
                'cashier_id' => $petugas1->id,
                'order_type' => 'pos',
                'total_amount' => 39500,
                'discount_amount' => 0,
                'final_amount' => 39500,
                'status' => 'completed',
                'payment_status' => 'paid',
                'payment_method' => 'cash',
                'notes' => 'Transaksi Kasir POS Langsung (Pelanggan Toko)',
                'paid_at' => now()->subHours(3),
                'completed_at' => now()->subHours(3),
                'created_at' => now()->subHours(3),
            ]);
            OrderItem::create(['order_id' => $pos1->id, 'product_id' => $products['PRD-MIN-003']->id, 'product_name' => $products['PRD-MIN-003']->name, 'product_code' => $products['PRD-MIN-003']->code, 'price' => 5000, 'quantity' => 2, 'subtotal' => 10000]);
            OrderItem::create(['order_id' => $pos1->id, 'product_id' => $products['PRD-SNK-001']->id, 'product_name' => $products['PRD-SNK-001']->name, 'product_code' => $products['PRD-SNK-001']->code, 'price' => 12000, 'quantity' => 1, 'subtotal' => 12000]);
            OrderItem::create(['order_id' => $pos1->id, 'product_id' => $products['PRD-SNK-005']->id, 'product_name' => $products['PRD-SNK-005']->name, 'product_code' => $products['PRD-SNK-005']->code, 'price' => 16500, 'quantity' => 1, 'subtotal' => 16500]);
            Payment::create(['order_id' => $pos1->id, 'verified_by' => $petugas1->id, 'payment_method' => 'cash', 'amount' => 39500, 'status' => 'paid', 'verified_at' => now()->subHours(3)]);
            $pos1->addStatusHistory('completed', 'Transaksi tunai POS kasir selesai dan lunas.', $petugas1->id);

            // Case 8-15: Completed Orders across the past 7 days to generate a dynamic Chart.js sales curve and report figures
            $usersPool = [$pelanggan1, $pelanggan2, $pelanggan3, $pelanggan4, $pelanggan5];
            $prodsPool = array_values($products);

            for ($daysAgo = 6; $daysAgo >= 1; $daysAgo--) {
                $numOrdersThatDay = rand(2, 4);
                for ($j = 1; $j <= $numOrdersThatDay; $j++) {
                    $u = $usersPool[array_rand($usersPool)];
                    $p1 = $prodsPool[array_rand($prodsPool)];
                    $p2 = $prodsPool[array_rand($prodsPool)];
                    $q1 = rand(1, 3);
                    $q2 = rand(1, 2);
                    $tot = ($p1->sell_price * $q1) + ($p2->sell_price * $q2);

                    $createdAt = now()->subDays($daysAgo)->setHour(rand(9, 16))->setMinute(rand(0, 59));
                    $pastOrd = Order::create([
                        'order_number' => 'ORD-' . $createdAt->format('Ymd') . '-' . sprintf('%04d', rand(10, 999)),
                        'user_id' => $u->id,
                        'order_type' => rand(0, 1) ? 'pickup' : 'delivery',
                        'total_amount' => $tot,
                        'discount_amount' => 0,
                        'final_amount' => $tot,
                        'status' => 'completed',
                        'payment_status' => 'paid',
                        'payment_method' => rand(0, 1) ? 'qris' : 'cash',
                        'paid_at' => $createdAt->copy()->addMinutes(10),
                        'completed_at' => $createdAt->copy()->addMinutes(30),
                        'created_at' => $createdAt,
                    ]);

                    OrderItem::create(['order_id' => $pastOrd->id, 'product_id' => $p1->id, 'product_name' => $p1->name, 'product_code' => $p1->code, 'price' => $p1->sell_price, 'quantity' => $q1, 'subtotal' => $p1->sell_price * $q1]);
                    OrderItem::create(['order_id' => $pastOrd->id, 'product_id' => $p2->id, 'product_name' => $p2->name, 'product_code' => $p2->code, 'price' => $p2->sell_price, 'quantity' => $q2, 'subtotal' => $p2->sell_price * $q2]);
                    Payment::create(['order_id' => $pastOrd->id, 'user_id' => $u->id, 'verified_by' => $petugas1->id, 'payment_method' => $pastOrd->payment_method, 'amount' => $tot, 'status' => 'paid', 'verified_at' => $createdAt->copy()->addMinutes(10), 'created_at' => $createdAt]);
                }
            }
        }

        // ---------------------------------------------------------
        // 10. STOCK OPNAMES (ADMIN MODULE)
        // ---------------------------------------------------------
        if (StockOpname::count() === 0) {
            // Sesi 1: Approved History (Opname Awal Bulan)
            $opn1 = StockOpname::create([
                'opname_number' => 'OPN-' . date('Ymd', strtotime('-10 days')) . '-001',
                'created_by' => $admin->id,
                'approved_by' => $admin->id,
                'status' => 'approved',
                'notes' => 'Stok opname berkala pembukaan periode operasional November',
                'approved_at' => now()->subDays(10)->addHours(4),
                'created_at' => now()->subDays(10),
            ]);
            foreach (array_slice($products, 0, 8) as $prod) {
                OpnameDetail::create([
                    'stock_opname_id' => $opn1->id,
                    'product_id' => $prod->id,
                    'system_stock' => $prod->stock,
                    'physical_stock' => $prod->stock,
                    'difference' => 0,
                    'notes' => 'Stok fisik sesuai catatan sistem',
                ]);
            }

            // Sesi 2: Reviewed / Menunggu Persetujuan Admin (dengan variasi selisih nyata untuk pengujian approval)
            $opn2 = StockOpname::create([
                'opname_number' => 'OPN-' . date('Ymd') . '-002',
                'created_by' => $admin->id,
                'approved_by' => null,
                'status' => 'reviewed',
                'notes' => 'Sesi stok opname mingguan - menunggu persetujuan akhir Admin',
                'created_at' => now()->subHours(2),
            ]);
            foreach (array_slice($products, 0, 10) as $idx => $prod) {
                $diff = ($idx === 1) ? -2 : (($idx === 3) ? 1 : 0);
                $phys = max(0, $prod->stock + $diff);
                OpnameDetail::create([
                    'stock_opname_id' => $opn2->id,
                    'product_id' => $prod->id,
                    'system_stock' => $prod->stock,
                    'physical_stock' => $phys,
                    'difference' => $diff,
                    'notes' => $diff != 0 ? "Terdapat selisih $diff {$prod->unit?->symbol}" : 'Cocok',
                ]);
            }
        }
    }

    /**
     * Helper to generate a realistic sample transfer receipt image using GD.
     */
    private function generateSampleProof(string $filepath, string $title, string $amount, string $sender): void
    {
        if (File::exists($filepath)) return;

        if (!function_exists('imagecreatetruecolor')) {
            File::put($filepath, 'dummy_image_data');
            return;
        }

        $im = imagecreatetruecolor(420, 600);
        $bg = imagecolorallocate($im, 246, 248, 250);
        $white = imagecolorallocate($im, 255, 255, 255);
        $green = imagecolorallocate($im, 22, 131, 74);
        $dark = imagecolorallocate($im, 37, 43, 51);
        $gray = imagecolorallocate($im, 108, 117, 125);
        $border = imagecolorallocate($im, 222, 226, 230);

        imagefilledrectangle($im, 0, 0, 420, 600, $bg);
        imagefilledrectangle($im, 20, 20, 400, 580, $white);
        imagerectangle($im, 20, 20, 400, 580, $border);

        // Header
        imagefilledrectangle($im, 20, 20, 400, 90, $green);
        imagestring($im, 5, 80, 40, 'BUKTI PEMBAYARAN QRIS', $white);
        imagestring($im, 3, 130, 65, 'TRANSAKSI BERHASIL', $white);

        // Body
        imagestring($im, 3, 50, 130, 'Penerima   : E-KOPERASI PEGAWAI KANTOR', $dark);
        imagestring($im, 3, 50, 160, 'NMID       : ID1020030040506', $gray);
        imagestring($im, 3, 50, 190, 'Pengirim   : ' . $sender, $dark);
        imagestring($im, 3, 50, 220, 'Tanggal    : ' . date('d M Y H:i') . ' WIB', $gray);
        imagestring($im, 3, 50, 250, 'No. Ref    : QRIS' . rand(10000000, 99999999), $gray);

        imageline($im, 50, 290, 370, 290, $border);

        imagestring($im, 4, 50, 320, 'Nominal Tagihan  : ' . $amount, $dark);
        imagestring($im, 3, 50, 350, 'Biaya Transaksi  : Rp 0 (Bebas Biaya)', $gray);

        imageline($im, 50, 390, 370, 390, $border);

        imagestring($im, 5, 50, 420, 'TOTAL DIBAYAR    : ' . $amount, $green);

        imagestring($im, 2, 80, 520, 'Struk elektronik resmi transaksi perbankan', $gray);
        imagestring($im, 2, 90, 540, 'Simpan bukti ini untuk arsip Anda', $gray);

        imagejpeg($im, $filepath, 90);
        imagedestroy($im);
    }
}
