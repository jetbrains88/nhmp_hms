<?php

namespace Database\Seeders;

use App\Models\IllnessTag;
use Illuminate\Database\Seeder;

class IllnessTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            // ── Chronic Conditions ────────────────────────────────────────
            ['name' => 'Diabetes Type 1',       'category' => 'chronic',    'icd_code' => 'E10', 'description' => 'Insulin-dependent diabetes mellitus'],
            ['name' => 'Diabetes Type 2',       'category' => 'chronic',    'icd_code' => 'E11', 'description' => 'Non-insulin-dependent diabetes mellitus'],
            ['name' => 'Hypertension',          'category' => 'chronic',    'icd_code' => 'I10', 'description' => 'High blood pressure (primary hypertension)'],
            ['name' => 'Coronary Artery Disease','category' => 'chronic',   'icd_code' => 'I25', 'description' => 'Atherosclerotic heart disease'],
            ['name' => 'Heart Failure',         'category' => 'chronic',    'icd_code' => 'I50', 'description' => 'Congestive heart failure'],
            ['name' => 'Atrial Fibrillation',   'category' => 'chronic',    'icd_code' => 'I48', 'description' => 'Irregular and often rapid heart rate'],
            ['name' => 'Asthma',                'category' => 'chronic',    'icd_code' => 'J45', 'description' => 'Chronic respiratory airway inflammation'],
            ['name' => 'COPD',                  'category' => 'chronic',    'icd_code' => 'J44', 'description' => 'Chronic obstructive pulmonary disease'],
            ['name' => 'Chronic Kidney Disease','category' => 'chronic',    'icd_code' => 'N18', 'description' => 'Gradual reduction in kidney function'],
            ['name' => 'Liver Cirrhosis',       'category' => 'chronic',    'icd_code' => 'K70', 'description' => 'Liver scarring from long-term damage'],
            ['name' => 'Thyroid Disorder',      'category' => 'chronic',    'icd_code' => 'E07', 'description' => 'Includes hypothyroidism and hyperthyroidism'],
            ['name' => 'Hypothyroidism',        'category' => 'chronic',    'icd_code' => 'E03', 'description' => 'Underactive thyroid gland'],
            ['name' => 'Hyperthyroidism',       'category' => 'chronic',    'icd_code' => 'E05', 'description' => 'Overactive thyroid gland'],
            ['name' => 'Anemia',                'category' => 'chronic',    'icd_code' => 'D64', 'description' => 'Low red blood cell count or hemoglobin'],
            ['name' => 'Sickle Cell Disease',   'category' => 'chronic',    'icd_code' => 'D57', 'description' => 'Inherited blood disorder'],
            ['name' => 'Rheumatoid Arthritis',  'category' => 'chronic',    'icd_code' => 'M06', 'description' => 'Autoimmune inflammatory joint disease'],
            ['name' => 'Osteoarthritis',        'category' => 'chronic',    'icd_code' => 'M19', 'description' => 'Degenerative joint disease'],
            ['name' => 'Osteoporosis',          'category' => 'chronic',    'icd_code' => 'M81', 'description' => 'Reduced bone density'],
            ['name' => 'Gout',                  'category' => 'chronic',    'icd_code' => 'M10', 'description' => 'Uric acid crystal deposition in joints'],
            ['name' => 'Epilepsy',              'category' => 'chronic',    'icd_code' => 'G40', 'description' => 'Recurrent seizure disorder'],
            ['name' => 'Parkinson\'s Disease',  'category' => 'chronic',    'icd_code' => 'G20', 'description' => 'Progressive neurological movement disorder'],
            ['name' => 'Alzheimer\'s Disease',  'category' => 'chronic',    'icd_code' => 'G30', 'description' => 'Progressive brain disorder affecting memory and cognition'],
            ['name' => 'Depression',            'category' => 'chronic',    'icd_code' => 'F33', 'description' => 'Major depressive disorder'],
            ['name' => 'Anxiety Disorder',      'category' => 'chronic',    'icd_code' => 'F41', 'description' => 'Persistent excessive worry'],
            ['name' => 'Schizophrenia',         'category' => 'chronic',    'icd_code' => 'F20', 'description' => 'Chronic psychotic disorder'],
            ['name' => 'Bipolar Disorder',      'category' => 'chronic',    'icd_code' => 'F31', 'description' => 'Mood disorder with manic and depressive episodes'],
            ['name' => 'Obesity',               'category' => 'chronic',    'icd_code' => 'E66', 'description' => 'BMI ≥ 30 kg/m²'],
            ['name' => 'Dyslipidemia',          'category' => 'chronic',    'icd_code' => 'E78', 'description' => 'Abnormal blood lipid levels (cholesterol/triglycerides)'],
            ['name' => 'Peptic Ulcer Disease',  'category' => 'chronic',    'icd_code' => 'K27', 'description' => 'Ulcers in the stomach or upper small intestine'],
            ['name' => 'GERD',                  'category' => 'chronic',    'icd_code' => 'K21', 'description' => 'Gastroesophageal reflux disease'],

            // ── Acute Conditions ──────────────────────────────────────────
            ['name' => 'Pneumonia',             'category' => 'acute',      'icd_code' => 'J18', 'description' => 'Lung infection (bacterial/viral/fungal)'],
            ['name' => 'Acute Appendicitis',    'category' => 'acute',      'icd_code' => 'K35', 'description' => 'Inflammation of the appendix'],
            ['name' => 'Acute MI',              'category' => 'acute',      'icd_code' => 'I21', 'description' => 'Acute myocardial infarction (heart attack)'],
            ['name' => 'Stroke (Ischemic)',     'category' => 'acute',      'icd_code' => 'I63', 'description' => 'Brain ischemic event from blocked artery'],
            ['name' => 'Gastroenteritis',       'category' => 'acute',      'icd_code' => 'A09', 'description' => 'Stomach and intestinal infection'],
            ['name' => 'Urinary Tract Infection','category' => 'acute',     'icd_code' => 'N39', 'description' => 'Bacterial infection of the urinary system'],
            ['name' => 'Acute Pancreatitis',    'category' => 'acute',      'icd_code' => 'K85', 'description' => 'Sudden inflammation of the pancreas'],
            ['name' => 'Dengue Fever',          'category' => 'infectious', 'icd_code' => 'A90', 'description' => 'Mosquito-borne viral fever'],
            ['name' => 'Typhoid Fever',         'category' => 'infectious', 'icd_code' => 'A01', 'description' => 'Salmonella typhi bacterial infection'],
            ['name' => 'Malaria',               'category' => 'infectious', 'icd_code' => 'B54', 'description' => 'Plasmodium parasite infection via mosquito'],
            ['name' => 'Hepatitis B',           'category' => 'infectious', 'icd_code' => 'B16', 'description' => 'Hepatitis B virus liver infection'],
            ['name' => 'Hepatitis C',           'category' => 'infectious', 'icd_code' => 'B17', 'description' => 'Hepatitis C virus liver infection'],
            ['name' => 'Tuberculosis',          'category' => 'infectious', 'icd_code' => 'A15', 'description' => 'Mycobacterium tuberculosis bacterial infection'],
            ['name' => 'HIV/AIDS',              'category' => 'infectious', 'icd_code' => 'B20', 'description' => 'Human immunodeficiency virus infection'],
        ];

        foreach ($tags as $tag) {
            IllnessTag::firstOrCreate(
                ['name' => $tag['name']],
                array_merge($tag, ['is_active' => true])
            );
        }

        $this->command->info('✅ IllnessTag seeder completed: ' . count($tags) . ' tags seeded.');
    }
}
