<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PharmacySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedMedicineCategories();
        $this->createViews();

        $this->command->info('💊 Pharmacy module infrastructure (Categories and Views) initialized!');
    }

    /**
     * Seed medicine categories.
     */
    private function seedMedicineCategories(): void
    {
        // Check if categories already exist
        if (DB::table('medicine_categories')->count() > 0) {
            $this->command->info('Medicine categories already seeded.');
            return;
        }

        $categories = [
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Analgesics',
                'slug' => 'analgesics',
                'description' => 'Pain relievers',
                'display_order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Antibiotics',
                'slug' => 'antibiotics',
                'description' => 'Anti-bacterial medicines',
                'display_order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Antipyretic',
                'slug' => 'antipyretic',
                'description' => 'Fever reducers',
                'display_order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Antihistamines',
                'slug' => 'antihistamines',
                'description' => 'Allergy medicines',
                'display_order' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Cardiovascular',
                'slug' => 'cardiovascular',
                'description' => 'Heart and blood pressure medicines',
                'display_order' => 5,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Gastrointestinal',
                'slug' => 'gastrointestinal',
                'description' => 'Stomach and digestive medicines',
                'display_order' => 6,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Respiratory',
                'slug' => 'respiratory',
                'description' => 'Lung and breathing medicines',
                'display_order' => 7,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Vitamins',
                'slug' => 'vitamins',
                'description' => 'Vitamin supplements',
                'display_order' => 8,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($categories as $category) {
            DB::table('medicine_categories')->insert($category);
        }
    }

    /**
     * Seed medicines.
     */
    private function seedMedicines(): void
    {
        // Check if medicines already exist
        if (DB::table('medicines')->count() > 0) {
            $this->command->info('Medicines already seeded.');
            return;
        }

        // Get branch IDs
        $branches = DB::table('branches')->whereIn('type', ['branch', 'pharmacy_only'])->get();
        $mainBranch = $branches->first();

        if (!$mainBranch) {
            $this->command->error('No branches found for pharmacy seeding!');
            return;
        }

        $medicines = [
            [
                'uuid' => (string) Str::uuid(),
                'code' => 'PARA500',
                'name' => 'Paracetamol',
                'generic_name' => 'Acetaminophen',
                'brand' => 'Tylenol',
                'manufacturer' => 'Johnson & Johnson',
                'form' => 'Tablet',
                'strength' => '500mg',
                'unit' => 'pcs',
                'category_id' => 1, // Analgesics
                'description' => 'For fever and mild to moderate pain',
                'unit_price' => 0.50,
                'selling_price' => 1.00,
                'reorder_level' => 200,
                'is_active' => true,
                'requires_prescription' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => (string) Str::uuid(),
                'code' => 'AMOX250',
                'name' => 'Amoxicillin',
                'generic_name' => 'Amoxicillin Trihydrate',
                'brand' => 'Amoxil',
                'manufacturer' => 'GlaxoSmithKline',
                'form' => 'Capsule',
                'strength' => '250mg',
                'unit' => 'pcs',
                'category_id' => 2, // Antibiotics
                'description' => 'Broad spectrum antibiotic',
                'unit_price' => 2.50,
                'selling_price' => 5.00,
                'reorder_level' => 100,
                'is_active' => true,
                'requires_prescription' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => (string) Str::uuid(),
                'code' => 'IBU400',
                'name' => 'Ibuprofen',
                'generic_name' => 'Ibuprofen',
                'brand' => 'Advil',
                'manufacturer' => 'Pfizer',
                'form' => 'Tablet',
                'strength' => '400mg',
                'unit' => 'pcs',
                'category_id' => 1, // Analgesics
                'description' => 'NSAID for pain, fever, and inflammation',
                'unit_price' => 0.75,
                'selling_price' => 1.50,
                'reorder_level' => 100,
                'is_active' => true,
                'requires_prescription' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => (string) Str::uuid(),
                'code' => 'LOR10',
                'name' => 'Loratadine',
                'generic_name' => 'Loratadine',
                'brand' => 'Claritin',
                'manufacturer' => 'Bayer',
                'form' => 'Tablet',
                'strength' => '10mg',
                'unit' => 'pcs',
                'category_id' => 4, // Antihistamines
                'description' => 'Non-drowsy allergy relief',
                'unit_price' => 1.25,
                'selling_price' => 2.50,
                'reorder_level' => 50,
                'is_active' => true,
                'requires_prescription' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => (string) Str::uuid(),
                'code' => 'MVT100',
                'name' => 'Multivitamin',
                'generic_name' => 'Multivitamin Complex',
                'brand' => 'Centrum',
                'manufacturer' => 'Pfizer',
                'form' => 'Tablet',
                'strength' => '100 tablets',
                'unit' => 'bottle',
                'category_id' => 8, // Vitamins
                'description' => 'Daily multivitamin supplement',
                'unit_price' => 15.00,
                'selling_price' => 30.00,
                'reorder_level' => 25,
                'is_active' => true,
                'requires_prescription' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($medicines as $medData) {
            // Insert Medicine
            $medId = DB::table('medicines')->insertGetId($medData);

            // Determine stock based on medicine
            $stock = match ($medData['code']) {
                'PARA500' => 1000,
                'AMOX250' => 500,
                'IBU400' => 50,
                'LOR10' => 300,
                'MVT100' => 0,
                default => 100,
            };

            $batchNumber = match ($medData['code']) {
                'PARA500' => 'BATCH2024001',
                'AMOX250' => 'BATCH2024002',
                'IBU400' => 'BATCH2024003',
                'LOR10' => 'BATCH2024004',
                'MVT100' => 'BATCH2024005',
                default => 'BATCH' . date('Y') . rand(100, 999),
            };

            $expiryDate = match ($medData['code']) {
                'PARA500' => now()->addMonths(12),
                'AMOX250' => now()->addMonths(8),
                'IBU400' => now()->addMonths(9),
                'LOR10' => now()->addMonths(7),
                'MVT100' => now()->addMonths(14),
                default => now()->addMonths(12),
            };

            // Insert Batch for each branch
            foreach ($branches as $branch) {
                DB::table('medicine_batches')->insert([
                    'uuid' => (string) Str::uuid(),
                    'branch_id' => $branch->id,
                    'medicine_id' => $medId,
                    'batch_number' => $batchNumber . '-' . $branch->id,
                    'rc_number' => 'RC-' . strtoupper(Str::random(6)),
                    'expiry_date' => $expiryDate,
                    'unit_price' => $medData['unit_price'],
                    'sale_price' => $medData['selling_price'],
                    'remaining_quantity' => $stock,
                    'is_active' => $stock > 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Seed inventory logs.
     */
    private function seedInventoryLogs(): void
    {
        // Check if inventory logs already exist
        if (DB::table('inventory_logs')->count() > 0) {
            $this->command->info('Inventory logs already seeded.');
            return;
        }

        // Get admin user ID
        $adminUser = DB::table('users')->where('email', 'admin@gmail.com')->first();

        if (!$adminUser) {
            $this->command->error('Admin user not found! Inventory logs not created.');
            return;
        }

        $medicines = DB::table('medicines')->get();
        $inventoryLogs = [];

        foreach ($medicines as $medicine) {
            // Find the batch we just created for this medicine
            $batches = DB::table('medicine_batches')->where('medicine_id', $medicine->id)->get();

            foreach ($batches as $batch) {
                $inventoryLogs[] = [
                    'uuid' => (string) Str::uuid(),
                    'branch_id' => $batch->branch_id,
                    'medicine_id' => $medicine->id,
                    'medicine_batch_id' => $batch->id,
                    'user_id' => $adminUser->id,
                    'type' => 'initial',
                    'quantity' => $batch->remaining_quantity,
                    'previous_stock' => 0,
                    'new_stock' => $batch->remaining_quantity,
                    'reference_id' => null,
                    'reference_type' => null,
                    'notes' => 'Initial stock entry from Seeder',
                    'batch_number' => $batch->batch_number,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Insert in chunks to avoid memory issues
        foreach (array_chunk($inventoryLogs, 100) as $chunk) {
            DB::table('inventory_logs')->insert($chunk);
        }
    }

    /**
     * Seed stock alerts.
     */
    private function seedStockAlerts(): void
    {
        // Check if stock alerts already exist
        if (DB::table('stock_alerts')->count() > 0) {
            $this->command->info('Stock alerts already seeded.');
            return;
        }

        $stockAlerts = [];

        // Get branches
        $branches = DB::table('branches')->whereIn('type', ['branch', 'pharmacy_only'])->get();

        foreach ($branches as $branch) {
            // Low stock alert for Ibuprofen
            $ibuprofen = DB::table('medicines')->where('code', 'IBU400')->first();
            if ($ibuprofen) {
                $stockAlerts[] = [
                    'uuid' => (string) Str::uuid(),
                    'branch_id' => $branch->id,
                    'medicine_id' => $ibuprofen->id,
                    'alert_type' => 'low_stock',
                    'message' => 'Ibuprofen is low on stock. Current stock: 50, Reorder level: 100',
                    'is_resolved' => false,
                    'resolved_at' => null,
                    'resolved_by' => null,
                    'resolution_notes' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Out of stock alert for Multivitamin
            $multivitamin = DB::table('medicines')->where('code', 'MVT100')->first();
            if ($multivitamin) {
                $stockAlerts[] = [
                    'uuid' => (string) Str::uuid(),
                    'branch_id' => $branch->id,
                    'medicine_id' => $multivitamin->id,
                    'alert_type' => 'out_of_stock',
                    'message' => 'Multivitamin is out of stock. Current stock: 0',
                    'is_resolved' => false,
                    'resolved_at' => null,
                    'resolved_by' => null,
                    'resolution_notes' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (!empty($stockAlerts)) {
            DB::table('stock_alerts')->insert($stockAlerts);
        }
    }

    /**
     * Create database views for reporting.
     */
    private function createViews(): void
    {
        // Drop views if they exist
        try {
            DB::statement('DROP VIEW IF EXISTS medicine_dispensing_history');
            DB::statement('DROP VIEW IF EXISTS medicine_stock_value');
        } catch (\Exception $e) {
            // Views might not exist yet, that's okay
        }

        // Medicine dispensing history view
        DB::statement("
            CREATE VIEW medicine_dispensing_history AS
            SELECT
                m.id as medicine_id,
                m.code as medicine_code,
                m.name as medicine_name,
                m.generic_name,
                m.form,
                m.strength,
                p.id as prescription_id,
                p.dispensed_quantity,
                p.dispensed_at,
                p.batch_number,
                p.dispense_notes,
                pat.emrn,
                pat.name as patient_name,
                pat.gender,
                doc.name as prescribed_by,
                pharm.name as dispensed_by
            FROM prescriptions p
            JOIN medicines m ON p.medicine_id = m.id
            LEFT JOIN diagnoses d ON p.diagnosis_id = d.id
            LEFT JOIN visits v ON d.visit_id = v.id
            LEFT JOIN patients pat ON v.patient_id = pat.id
            LEFT JOIN users doc ON d.doctor_id = doc.id
            LEFT JOIN users pharm ON p.dispensed_by = pharm.id
            WHERE p.status = 'dispensed' AND p.dispensed_at IS NOT NULL
            ORDER BY p.dispensed_at DESC
        ");

        // Medicine stock value view
        DB::statement("
            CREATE VIEW medicine_stock_value AS
            SELECT
                m.id,
                m.code,
                m.name,
                m.unit,
                mc.name as category,
                COALESCE(SUM(mb.remaining_quantity), 0) as stock,
                m.reorder_level,
                m.reorder_level as min_stock_level,
                m.unit_price,
                m.selling_price,
                COALESCE(SUM(mb.remaining_quantity * mb.unit_price), 0) as stock_value,
                CASE
                    WHEN COALESCE(SUM(mb.remaining_quantity), 0) = 0 THEN 'out_of_stock'
                    WHEN COALESCE(SUM(mb.remaining_quantity), 0) <= m.reorder_level THEN 'low_stock'
                    ELSE 'in_stock'
                END as stock_status,
                MIN(mb.expiry_date) as expiry_date,
                DATEDIFF(MIN(mb.expiry_date), CURDATE()) as days_to_expire,
                m.is_active,
                m.requires_prescription
            FROM medicines m
            LEFT JOIN medicine_categories mc ON m.category_id = mc.id
            LEFT JOIN medicine_batches mb ON m.id = mb.medicine_id AND mb.deleted_at IS NULL
            WHERE m.deleted_at IS NULL
            GROUP BY m.id, m.code, m.name, m.unit, mc.name, m.reorder_level, m.unit_price, m.selling_price, m.is_active, m.requires_prescription
            ORDER BY stock_status, days_to_expire, m.name
        ");
    }
}
