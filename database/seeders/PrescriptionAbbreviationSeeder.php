<?php

namespace Database\Seeders;

use App\Models\PrescriptionAbbreviation;
use Illuminate\Database\Seeder;

class PrescriptionAbbreviationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * doses_per_day: how many doses per day this abbreviation implies.
     * Used by the frontend JS to auto-fill the "days" field in the prescription form.
     *
     * Example logic (JS side):
     *   if abbreviation has doses_per_day = 2 (BID) and quantity = 14 tablets
     *   → days = 14 / 2 = 7 days
     */
    public function run(): void
    {
        $abbreviations = [
            // ─────────────────────────────────────────────────────────────
            // Frequency / Timing
            // ─────────────────────────────────────────────────────────────
            ['abbreviation' => 'QD',   'full_meaning' => 'Once a day (quaque die) — 1 dose/day',            'category' => 'frequency', 'doses_per_day' => 1],
            ['abbreviation' => 'OD',   'full_meaning' => 'Once daily — same as QD (1 dose/day)',             'category' => 'frequency', 'doses_per_day' => 1],
            ['abbreviation' => 'BID',  'full_meaning' => 'Twice a day (bis in die) — 2 doses/day',           'category' => 'frequency', 'doses_per_day' => 2],
            ['abbreviation' => 'BD',   'full_meaning' => 'Twice daily — same as BID (2 doses/day)',           'category' => 'frequency', 'doses_per_day' => 2],
            ['abbreviation' => 'TID',  'full_meaning' => 'Three times a day (ter in die) — 3 doses/day',     'category' => 'frequency', 'doses_per_day' => 3],
            ['abbreviation' => 'TDS',  'full_meaning' => 'Three times daily — same as TID (3 doses/day)',    'category' => 'frequency', 'doses_per_day' => 3],
            ['abbreviation' => 'QID',  'full_meaning' => 'Four times a day (quater in die) — 4 doses/day',   'category' => 'frequency', 'doses_per_day' => 4],
            ['abbreviation' => 'QDS',  'full_meaning' => 'Four times daily — same as QID (4 doses/day)',     'category' => 'frequency', 'doses_per_day' => 4],
            ['abbreviation' => 'Q4H',  'full_meaning' => 'Every 4 hours — 6 doses/day (q quaque, 4H)',       'category' => 'frequency', 'doses_per_day' => 6],
            ['abbreviation' => 'Q6H',  'full_meaning' => 'Every 6 hours — 4 doses/day',                      'category' => 'frequency', 'doses_per_day' => 4],
            ['abbreviation' => 'Q8H',  'full_meaning' => 'Every 8 hours — 3 doses/day',                      'category' => 'frequency', 'doses_per_day' => 3],
            ['abbreviation' => 'Q12H', 'full_meaning' => 'Every 12 hours — 2 doses/day',                     'category' => 'frequency', 'doses_per_day' => 2],
            ['abbreviation' => 'QOD',  'full_meaning' => 'Every other day — 0.5 doses/day (alternate days)', 'category' => 'frequency', 'doses_per_day' => null],
            ['abbreviation' => 'QW',   'full_meaning' => 'Once a week (weekly)',                              'category' => 'frequency', 'doses_per_day' => null],
            ['abbreviation' => 'PRN',  'full_meaning' => 'As needed (pro re nata) — use when required',      'category' => 'frequency', 'doses_per_day' => null],
            ['abbreviation' => 'STAT', 'full_meaning' => 'Immediately / at once (statim)',                    'category' => 'frequency', 'doses_per_day' => null],

            // ─────────────────────────────────────────────────────────────
            // Timing (in relation to meals)
            // ─────────────────────────────────────────────────────────────
            ['abbreviation' => 'AC',   'full_meaning' => 'Before meals (ante cibum)',                         'category' => 'timing', 'doses_per_day' => null],
            ['abbreviation' => 'PC',   'full_meaning' => 'After meals (post cibum)',                          'category' => 'timing', 'doses_per_day' => null],
            ['abbreviation' => 'HS',   'full_meaning' => 'At bedtime (hora somni) — once nightly',            'category' => 'timing', 'doses_per_day' => 1],
            ['abbreviation' => 'QHS',  'full_meaning' => 'Every bedtime — same as HS (nightly)',              'category' => 'timing', 'doses_per_day' => 1],
            ['abbreviation' => 'AM',   'full_meaning' => 'Morning dose only',                                 'category' => 'timing', 'doses_per_day' => 1],
            ['abbreviation' => 'PM',   'full_meaning' => 'Evening dose only',                                 'category' => 'timing', 'doses_per_day' => 1],
            ['abbreviation' => 'CC',   'full_meaning' => 'With meals (cum cibo)',                             'category' => 'timing', 'doses_per_day' => null],

            // ─────────────────────────────────────────────────────────────
            // Routes of administration
            // ─────────────────────────────────────────────────────────────
            ['abbreviation' => 'PO',   'full_meaning' => 'By mouth / oral (per os)',                          'category' => 'route', 'doses_per_day' => null],
            ['abbreviation' => 'SL',   'full_meaning' => 'Under the tongue (sublingual)',                     'category' => 'route', 'doses_per_day' => null],
            ['abbreviation' => 'IV',   'full_meaning' => 'Into a vein (intravenous)',                         'category' => 'route', 'doses_per_day' => null],
            ['abbreviation' => 'IM',   'full_meaning' => 'Into a muscle (intramuscular)',                     'category' => 'route', 'doses_per_day' => null],
            ['abbreviation' => 'SC',   'full_meaning' => 'Under the skin (subcutaneous)',                     'category' => 'route', 'doses_per_day' => null],
            ['abbreviation' => 'TOP',  'full_meaning' => 'Applied to skin surface (topical)',                 'category' => 'route', 'doses_per_day' => null],
            ['abbreviation' => 'INH',  'full_meaning' => 'By inhalation',                                     'category' => 'route', 'doses_per_day' => null],
            ['abbreviation' => 'OD_EYE','full_meaning' => 'Right eye (oculus dexter)',                        'category' => 'route', 'doses_per_day' => null],
            ['abbreviation' => 'OS',   'full_meaning' => 'Left eye (oculus sinister)',                        'category' => 'route', 'doses_per_day' => null],
            ['abbreviation' => 'OU',   'full_meaning' => 'Both eyes (oculus uterque)',                        'category' => 'route', 'doses_per_day' => null],
            ['abbreviation' => 'AD',   'full_meaning' => 'Right ear (auris dextra)',                          'category' => 'route', 'doses_per_day' => null],
            ['abbreviation' => 'AS',   'full_meaning' => 'Left ear (auris sinistra)',                         'category' => 'route', 'doses_per_day' => null],
            ['abbreviation' => 'AU',   'full_meaning' => 'Both ears (aures utrae)',                           'category' => 'route', 'doses_per_day' => null],
            ['abbreviation' => 'PR',   'full_meaning' => 'By rectum (rectal suppository)',                    'category' => 'route', 'doses_per_day' => null],
            ['abbreviation' => 'PV',   'full_meaning' => 'Vaginally (per vaginum)',                           'category' => 'route', 'doses_per_day' => null],

            // ─────────────────────────────────────────────────────────────
            // Dosage / Quantity
            // ─────────────────────────────────────────────────────────────
            ['abbreviation' => 'GTT',  'full_meaning' => 'Drop / drops (guttae)',                             'category' => 'dosage', 'doses_per_day' => null],
            ['abbreviation' => 'SS',   'full_meaning' => 'One half (semis)',                                  'category' => 'dosage', 'doses_per_day' => null],
            ['abbreviation' => 'I',    'full_meaning' => 'One tablet / unit (Roman numeral I)',                'category' => 'dosage', 'doses_per_day' => null],
            ['abbreviation' => 'II',   'full_meaning' => 'Two tablets / units (Roman numeral II)',            'category' => 'dosage', 'doses_per_day' => null],
            ['abbreviation' => 'III',  'full_meaning' => 'Three tablets / units (Roman numeral III)',         'category' => 'dosage', 'doses_per_day' => null],
            ['abbreviation' => 'DISP', 'full_meaning' => 'Dispense (quantity to issue)',                      'category' => 'dosage', 'doses_per_day' => null],

            // ─────────────────────────────────────────────────────────────
            // General / Clinical
            // ─────────────────────────────────────────────────────────────
            ['abbreviation' => 'Rx',   'full_meaning' => 'Prescription / treatment order',                   'category' => 'general', 'doses_per_day' => null],
            ['abbreviation' => 'Dx',   'full_meaning' => 'Diagnosis',                                         'category' => 'general', 'doses_per_day' => null],
            ['abbreviation' => 'Tx',   'full_meaning' => 'Treatment/therapy',                                 'category' => 'general', 'doses_per_day' => null],
            ['abbreviation' => 'SR',   'full_meaning' => 'Sustained release (slow-release formulation)',      'category' => 'general', 'doses_per_day' => null],
            ['abbreviation' => 'XR',   'full_meaning' => 'Extended release formulation',                      'category' => 'general', 'doses_per_day' => null],
            ['abbreviation' => 'CR',   'full_meaning' => 'Controlled release formulation',                    'category' => 'general', 'doses_per_day' => null],
            ['abbreviation' => 'DAW',  'full_meaning' => 'Dispense as written — do not substitute generic',  'category' => 'general', 'doses_per_day' => null],
            ['abbreviation' => 'NKA',  'full_meaning' => 'No known allergies',                                'category' => 'general', 'doses_per_day' => null],
            ['abbreviation' => 'MR',   'full_meaning' => 'Modified release formulation',                      'category' => 'general', 'doses_per_day' => null],
            ['abbreviation' => 'SOB',  'full_meaning' => 'Shortness of breath (symptom abbreviation)',        'category' => 'general', 'doses_per_day' => null],
        ];

        foreach ($abbreviations as $abbr) {
            PrescriptionAbbreviation::firstOrCreate(
                ['abbreviation' => $abbr['abbreviation']],
                array_merge($abbr, ['is_active' => true])
            );
        }

        $this->command->info('✅ PrescriptionAbbreviationSeeder completed: ' . count($abbreviations) . ' abbreviations seeded.');
    }
}
