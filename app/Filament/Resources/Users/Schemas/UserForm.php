<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Role;
use App\Models\User;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Password;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi akun')
                    ->description('Isi data dasar akun yang akan dipakai untuk login dan identitas user di sistem.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->placeholder('Masukkan nama lengkap user')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email')
                            ->placeholder('user@example.com')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('phone')
                            ->label('Nomor telepon')
                            ->placeholder('08xxxxxxxxxx')
                            ->tel()
                            ->maxLength(255),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 3,
                    ]),
                Section::make('Hak akses dan status')
                    ->description('Tentukan peran user dan status akunnya agar panel admin dan fitur customer berjalan sesuai kebutuhan.')
                    ->schema([
                        Select::make('roles')
                            ->label('Role')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->required()
                            ->helperText('Pilih role yang dimiliki user. Admin tidak dapat menghapus role admin miliknya sendiri.')
                            ->options(Role::query()->orderBy('name')->pluck('name', 'id')->all()),
                        Select::make('status')
                            ->label('Status akun')
                            ->options([
                                'active' => 'Aktif',
                                'inactive' => 'Nonaktif',
                                'blocked' => 'Diblokir',
                            ])
                            ->default('active')
                            ->native(false)
                            ->required(),
                        Placeholder::make('email_verified_at_label')
                            ->label('Email terverifikasi')
                            ->content(fn (?User $record): string => $record?->email_verified_at?->format('d M Y H:i') ?? 'Belum diverifikasi'),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 3,
                    ]),
                Section::make('Password')
                    ->description('Password wajib diisi saat membuat user baru. Saat edit, kolom ini boleh dikosongkan jika tidak ingin mengganti password.')
                    ->schema([
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->required(fn (?User $record): bool => $record === null)
                            ->rule(Password::default())
                            ->dehydrated(fn ($state): bool => filled($state))
                            ->same('password_confirmation'),
                        TextInput::make('password_confirmation')
                            ->label('Konfirmasi password')
                            ->password()
                            ->revealable()
                            ->required(fn ($get): bool => filled($get('password')))
                            ->dehydrated(false),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ]),
            ]);
    }
}
