<?php

namespace App\Filament\Resources\Certificates\Tables;

use App\Domain\Learning\Models\Certificate;
use App\Domain\Learning\Models\Course;
use App\Domain\Learning\Models\CourseEnrollment;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CertificatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('certificate_number')
                    ->label('No. Sertifikat')
                    ->badge()
                    ->color('primary')
                    ->copyable()
                    ->copyMessage('Nomor sertifikat disalin!')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('student_name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Certificate $record): string => $record->user?->email ?? '-'),

                TextColumn::make('course_title')
                    ->label('Kursus Kelulusan')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->description(fn (Certificate $record): string => 'Level: ' . ucfirst($record->course?->level ?? 'intermediate')),

                TextColumn::make('issued_at')
                    ->label('Tanggal Diterbitkan')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('verification_status')
                    ->label('Status')
                    ->badge()
                    ->state(fn () => 'Terverifikasi Valid')
                    ->color('success'),
            ])
            ->defaultSort('issued_at', 'desc')
            ->filters([
                SelectFilter::make('course_id')
                    ->label('Filter Kursus')
                    ->options(fn () => Course::pluck('title', 'id')->toArray())
                    ->searchable(),
            ])
            ->headerActions([
                Action::make('manual_issue')
                    ->label('Terbitkan Sertifikat Manual')
                    ->icon('heroicon-o-academic-cap')
                    ->color('success')
                    ->modalHeading('Terbitkan Sertifikat Kelulusan Manual')
                    ->modalDescription('Pilih siswa dan kursus untuk menerbitkan sertifikat kelulusan digital secara langsung.')
                    ->modalSubmitActionLabel('Terbitkan Sekarang')
                    ->form([
                        Select::make('user_id')
                            ->label('Pilih Siswa / Member')
                            ->options(User::pluck('name', 'id')->toArray())
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $user = User::find($state);
                                    if ($user) {
                                        $set('student_name', $user->name);
                                    }
                                }
                            }),

                        TextInput::make('student_name')
                            ->label('Nama Lengkap pada Sertifikat')
                            ->helperText('Nama resmi yang akan tercetak di sertifikat digital.')
                            ->required(),

                        Select::make('course_id')
                            ->label('Pilih Kursus')
                            ->options(Course::where('has_certificate', true)->pluck('title', 'id')->toArray())
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (array $data): void {
                        $user = User::findOrFail($data['user_id']);
                        $course = Course::findOrFail($data['course_id']);

                        $certNumber = 'CERT-' . date('Y') . '-TE-' . strtoupper(Str::random(6));

                        $certificate = Certificate::create([
                            'uuid' => (string) Str::uuid(),
                            'certificate_number' => $certNumber,
                            'user_id' => $user->id,
                            'course_id' => $course->id,
                            'student_name' => $data['student_name'] ?: $user->name,
                            'course_title' => $course->title,
                            'issued_at' => now(),
                            'verification_url' => url('/verify/' . $certNumber),
                        ]);

                        // Set Course Enrollment to 100%
                        CourseEnrollment::updateOrCreate(
                            [
                                'user_id' => $user->id,
                                'course_id' => $course->id,
                            ],
                            [
                                'progress_percentage' => 100.00,
                                'completed_at' => now(),
                                'enrolled_at' => now()->subDays(7),
                            ]
                        );

                        Notification::make()
                            ->title('Sertifikat Berhasil Diterbitkan')
                            ->body("Sertifikat no. {$certificate->certificate_number} telah diterbitkan untuk {$user->name}.")
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                Action::make('preview')
                    ->label('Preview')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading(fn (Certificate $record) => "Sertifikat Kelulusan: {$record->certificate_number}")
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalContent(fn (Certificate $record) => view('filament.components.certificate-preview-modal', [
                        'record' => $record,
                    ])),

                Action::make('verify_link')
                    ->label('Halaman Verifikasi')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn (Certificate $record): string => url('/verify/' . $record->certificate_number))
                    ->openUrlInNewTab(),

                DeleteAction::make()
                    ->label('Hapus')
                    ->modalHeading('Hapus Sertifikat')
                    ->modalDescription('Apakah Anda yakin ingin menghapus catatan sertifikat ini?'),
            ]);
    }
}
