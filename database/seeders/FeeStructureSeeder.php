<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use App\Models\Fee;
use App\Models\Payment;
use App\Models\StudentFee;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

class FeeStructureSeeder extends Seeder
{
    public function run(): void
    {
        $this->removeLegacyDemoFees();

        $feeBlueprints = [
            'Mathematics' => $this->semesterBlueprints(5200, '2024-04-18'),
            'Science' => $this->semesterBlueprints(5400, '2024-04-18'),
            'English' => $this->semesterBlueprints(5000, '2024-04-18'),
            'Computer' => $this->semesterBlueprints(5600, '2024-04-18'),
            'Art & Music' => $this->semesterBlueprints(3200, '2024-04-22'),
        ];

        $seededFees = collect();

        foreach ($feeBlueprints as $className => $fees) {
            $classRoom = ClassRoom::query()
                ->with('students')
                ->where('name', $className)
                ->first();

            if (! $classRoom) {
                continue;
            }

            foreach ($fees as $attributes) {
                $fee = Fee::updateOrCreate(
                    [
                        'class_room_id' => $classRoom->id,
                        'title' => $attributes['title'],
                        'due_date' => $attributes['due_date'],
                    ],
                    [
                        'amount' => $attributes['amount'],
                    ]
                );

                $fee->setRelation('classRoom', $classRoom);
                $fee->assignToStudents($classRoom->students);

                $seededFees->push($fee->id);
            }
        }

        $studentFees = StudentFee::query()
            ->with(['student', 'fee'])
            ->whereIn('fee_id', $seededFees->unique()->all())
            ->get();

        Payment::query()
            ->whereIn('student_fee_id', $studentFees->pluck('id'))
            ->delete();

        foreach ($studentFees as $studentFee) {
            $this->seedPaymentsForStudentFee($studentFee);
        }
    }

    private function seedPaymentsForStudentFee(StudentFee $studentFee): void
    {
        $semester = $this->semesterNumber($studentFee->fee->title);
        $total = round((float) $studentFee->total_amount, 2);

        $studentFee->paid_amount = 0;
        $studentFee->status = 'pending';
        $studentFee->save();

        if ($semester === 0 || $semester >= 5) {
            return;
        }

        if ($semester <= 3) {
            $this->markFullyPaid($studentFee, $total, $semester);
            return;
        }

        $this->markSemesterFourMixed($studentFee, $total);
    }

    private function markFullyPaid(StudentFee $studentFee, float $total, int $semester): void
    {
        $seed = abs(crc32($studentFee->student->email.'|'.$studentFee->fee->title));
        $parts = ($seed % 3) + 1;
        $remaining = $total;

        for ($index = 1; $index <= $parts; $index++) {
            $amount = $index === $parts
                ? $remaining
                : round(max(1, $remaining / ($parts - $index + 1)), 2);

            $amount = min($amount, $remaining);
            $remaining = round($remaining - $amount, 2);

            $this->createPayment($studentFee, $amount, $index, $seed + $semester);
        }

        $studentFee->paid_amount = $total;
        $studentFee->syncStatus();
        $studentFee->save();
    }

    private function markSemesterFourMixed(StudentFee $studentFee, float $total): void
    {
        $seed = abs(crc32($studentFee->student->email.'|semester-4'));
        $bucket = $seed % 100;

        if ($bucket < 30) {
            return;
        }

        if ($bucket < 70) {
            $paidAmount = round($total * (0.35 + (($seed % 31) / 100)), 2);
            $paidAmount = min($paidAmount, round($total - 1, 2));

            $this->createPayment($studentFee, $paidAmount, 1, $seed);

            $studentFee->paid_amount = $paidAmount;
            $studentFee->syncStatus();
            $studentFee->save();

            return;
        }

        $parts = ($seed % 2) + 2;
        $remaining = $total;

        for ($index = 1; $index <= $parts; $index++) {
            $amount = $index === $parts
                ? $remaining
                : round(max(1, $remaining / ($parts - $index + 1)), 2);

            $amount = min($amount, $remaining);
            $remaining = round($remaining - $amount, 2);

            $this->createPayment($studentFee, $amount, $index, $seed);
        }

        $studentFee->paid_amount = $total;
        $studentFee->syncStatus();
        $studentFee->save();
    }

    private function createPayment(StudentFee $studentFee, float $amount, int $sequence, int $seed): void
    {
        $paidAt = CarbonImmutable::parse($studentFee->fee->due_date)
            ->subDays(($seed + $sequence) % 10)
            ->setTime(10 + ($sequence % 5), 15);

        Payment::create([
            'student_fee_id' => $studentFee->id,
            'amount' => round($amount, 2),
            'payment_method' => $this->paymentMethodForSeed($seed + $sequence),
            'transaction_id' => sprintf('SEED-FEE-%d-%d', $studentFee->id, $sequence),
            'paid_at' => $paidAt,
        ]);
    }

    private function paymentMethodForSeed(int $seed): string
    {
        return match ($seed % 4) {
            0 => 'Cash',
            1 => 'UPI',
            2 => 'Bank Transfer',
            default => 'Card',
        };
    }

    private function removeLegacyDemoFees(): void
    {
        $legacyTitles = [
            'Tuition Fee - Term 1',
            'Exam Fee - Term 1',
            'Workbook Fee',
            'Lab Fee',
            'Library Fee',
            'Software Lab Fee',
            'Activity Fee - Term 1',
            'Performance Fee',
            'Materials Fee',
            'Semester 1 Fee',
            'Semester 2 Fee',
            'Semester 3 Fee',
            'Semester 4 Fee',
            'Semester 5 Fee',
            'Semester 6 Fee',
            'Semester 7 Fee',
            'Semester 8 Fee',
        ];

        Fee::query()
            ->whereIn('title', $legacyTitles)
            ->delete();
    }

    private function semesterBlueprints(int $baseAmount, string $startDate): array
    {
        $semesterDates = [];
        $date = CarbonImmutable::parse($startDate);

        for ($semester = 1; $semester <= 8; $semester++) {
            $semesterDates[] = [
                'title' => "Semester {$semester} Fee",
                'amount' => $baseAmount + (($semester - 1) * 250),
                'due_date' => $date->toDateString(),
            ];

            $date = $date->addMonths(6);
        }

        return $semesterDates;
    }

    private function semesterNumber(string $title): int
    {
        preg_match('/Semester\s+(\d+)/i', $title, $matches);

        return (int) ($matches[1] ?? 0);
    }
}
