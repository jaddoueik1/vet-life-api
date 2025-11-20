<?php

namespace Database\Seeders;

use App\Domain\Appointments\Models\Appointment;
use App\Domain\Invoicing\Models\Invoice;
use App\Domain\Invoicing\Models\InvoiceLineItem;
use App\Domain\Invoicing\Models\Payment;
use App\Domain\Patients\Models\Owner;
use App\Domain\Patients\Models\Patient;
use App\Domain\Users\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ClinicDataSeeder extends Seeder
{
    public function run(): void
    {
        $vetId = User::whereHas('roles', fn ($query) => $query->where('slug', 'vet'))
            ->pluck('id')
            ->first();

        $owners = [
            [
                'name' => 'Maria Garcia',
                'email' => 'maria.garcia@example.com',
                'phone' => '555-3001',
                'address' => '123 Pet Street',
                'patients' => [
                    [
                        'name' => 'Luna',
                        'species' => 'Dog',
                        'breed' => 'Labrador Retriever',
                        'age' => 4,
                        'sex' => 'Female',
                    ],
                    [
                        'name' => 'Simba',
                        'species' => 'Cat',
                        'breed' => 'Siamese',
                        'age' => 3,
                        'sex' => 'Male',
                    ],
                ],
            ],
            [
                'name' => 'Pedro Santos',
                'email' => 'pedro.santos@example.com',
                'phone' => '555-3002',
                'address' => '456 Meadow Lane',
                'patients' => [
                    [
                        'name' => 'Bella',
                        'species' => 'Dog',
                        'breed' => 'Beagle',
                        'age' => 2,
                        'sex' => 'Female',
                    ],
                ],
            ],
            [
                'name' => 'Aisha Khan',
                'email' => 'aisha.khan@example.com',
                'phone' => '555-3003',
                'address' => '789 Coastal Road',
                'patients' => [
                    [
                        'name' => 'Max',
                        'species' => 'Dog',
                        'breed' => 'Border Collie',
                        'age' => 6,
                        'sex' => 'Male',
                    ],
                    [
                        'name' => 'Kiwi',
                        'species' => 'Bird',
                        'breed' => 'Cockatiel',
                        'age' => 1,
                        'sex' => 'Female',
                    ],
                ],
            ],
        ];

        $invoiceCounter = 1001;

        foreach ($owners as $ownerData) {
            $patients = $ownerData['patients'];
            unset($ownerData['patients']);

            $owner = Owner::updateOrCreate(
                ['email' => $ownerData['email']],
                $ownerData
            );

            foreach ($patients as $patientData) {
                $patient = Patient::updateOrCreate(
                    ['owner_id' => $owner->id, 'name' => $patientData['name']],
                    $patientData
                );

                $appointmentDate = Carbon::now()->addDays(rand(1, 7))->setTime(10, 0);
                $followUpDate = Carbon::now()->addDays(rand(8, 14))->setTime(14, 0);

                Appointment::updateOrCreate(
                    ['patient_id' => $patient->id, 'scheduled_at' => $appointmentDate],
                    [
                        'assigned_vet_id' => $vetId,
                        'status' => 'scheduled',
                        'notes' => 'Routine check-up and vaccines',
                    ]
                );

                Appointment::updateOrCreate(
                    ['patient_id' => $patient->id, 'scheduled_at' => $followUpDate],
                    [
                        'assigned_vet_id' => $vetId,
                        'status' => 'confirmed',
                        'notes' => 'Follow-up visit',
                    ]
                );

                $visit = $patient->visits()->updateOrCreate(
                    ['visit_date' => Carbon::now()->subDays(rand(5, 30))],
                    [
                        'vet_id' => $vetId,
                        'summary' => 'Comprehensive wellness exam',
                        'diagnosis' => 'Healthy patient with routine vaccine schedule',
                        'treatment' => 'Administered core vaccines and provided diet plan',
                    ]
                );

                $invoice = Invoice::updateOrCreate(
                    ['number' => 'INV-' . $invoiceCounter++],
                    [
                        'owner_id' => $owner->id,
                        'status' => 'sent',
                        'total' => 0,
                    ]
                );

                $lineItems = [
                    ['description' => 'Consultation', 'quantity' => 1, 'price' => 65.00],
                    ['description' => 'Vaccines', 'quantity' => 1, 'price' => 45.00],
                    ['description' => 'Medication', 'quantity' => 1, 'price' => 25.00],
                ];

                $total = 0;
                foreach ($lineItems as $item) {
                    $line = InvoiceLineItem::updateOrCreate(
                        [
                            'invoice_id' => $invoice->id,
                            'description' => $item['description'],
                        ],
                        $item
                    );

                    $total += $line->quantity * $line->price;
                }

                $invoice->update(['total' => $total]);

                Payment::updateOrCreate(
                    ['invoice_id' => $invoice->id],
                    [
                        'amount' => $total - 10,
                        'method' => 'card',
                        'paid_at' => Carbon::now()->subDays(2),
                    ]
                );

                $visit->attachments()->updateOrCreate(
                    ['path' => 'records/' . Str::slug($patient->name) . '-exam.pdf'],
                    ['label' => 'Visit summary']
                );
            }
        }
    }
}
