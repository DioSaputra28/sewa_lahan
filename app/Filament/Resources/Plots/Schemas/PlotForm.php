<?php

namespace App\Filament\Resources\Plots\Schemas;

use App\Models\Area;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;

class PlotForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi utama')
                    ->description('Data dasar lahan yang akan tampil di panel admin dan katalog.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama lahan')
                            ->placeholder('Contoh: Freezer')
                            ->helperText('Nama ini dipakai sebagai identitas utama lahan.')
                            ->required()
                            ->maxLength(255),
                        Select::make('type')
                            ->label('Tipe lahan')
                            ->options([
                                'lahan' => 'Lahan',
                                'lapak' => 'Lapak',
                                'kios' => 'Kios',
                            ])
                            ->default('lahan')
                            ->native(false)
                            ->helperText('Pilih tipe yang paling sesuai untuk lahan ini.')
                            ->required(),
                        Select::make('status')
                            ->label('Status lahan')
                            ->options([
                                'available' => 'Tersedia',
                                'occupied' => 'Terisi',
                                'inactive' => 'Nonaktif',
                            ])
                            ->default('available')
                            ->native(false)
                            ->helperText('Status membantu admin mengetahui apakah lahan masih dapat ditawarkan atau tidak.')
                            ->required(),
                        Textarea::make('description')
                            ->label('Deskripsi lahan')
                            ->placeholder('Jelaskan kondisi atau catatan penting tentang lahan ini')
                            ->helperText('Kolom ini opsional untuk informasi tambahan tentang lahan.')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull()
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ])
                    ->grow()
                    ->maxWidth(Width::Full),
                Section::make('Lokasi dan pengelompokan')
                    ->description('Hubungkan lahan ke pasar dan area agar datanya rapi dan mudah difilter.')
                    ->schema([
                        Select::make('market_id')
                            ->relationship('market', 'name')
                            ->label('Pasar')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('area_id', null))
                            ->helperText('Pilih pasar tempat lahan ini berada.')
                            ->required()
                            ->native(false),
                        Select::make('area_id')
                            ->label('Area / blok')
                            ->options(fn (Get $get): array => Area::query()
                                ->when(
                                    filled($get('market_id')),
                                    fn ($query) => $query->where('market_id', $get('market_id')),
                                    fn ($query) => $query->whereRaw('1 = 0'),
                                )
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all())
                            ->searchable()
                            ->preload()
                            ->disabled(fn (Get $get): bool => blank($get('market_id')))
                            ->helperText('Boleh dikosongkan jika lahan belum dimasukkan ke area tertentu.')
                            ->native(false),
                        TextInput::make('floor_level')
                            ->label('Level / lantai')
                            ->placeholder('Contoh: 1F atau Lantai Dasar')
                            ->helperText('Isi bila lokasi lahan perlu dibedakan berdasarkan level atau lantai.')
                            ->maxLength(255),
                        TextInput::make('location_note')
                            ->label('Catatan lokasi')
                            ->placeholder('Contoh: Dekat pintu timur')
                            ->helperText('Gunakan untuk petunjuk singkat lokasi lahan.')
                            ->maxLength(255),
                    ])
                    ->columnSpanFull()
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ])
                    ->grow()
                    ->maxWidth(Width::Full),
                Section::make('Ukuran dan luas')
                    ->description('Ukuran fisik lahan membantu admin dan calon penyewa membandingkan pilihan yang tersedia.')
                    ->schema([
                        TextInput::make('length')
                            ->label('Panjang (meter)')
                            ->placeholder('Contoh: 5.00')
                            ->helperText('Isi panjang lahan dalam satuan meter.')
                            ->required()
                            ->numeric()
                            ->minValue(0.01),
                        TextInput::make('width')
                            ->label('Lebar (meter)')
                            ->placeholder('Contoh: 5.20')
                            ->helperText('Isi lebar lahan dalam satuan meter.')
                            ->required()
                            ->numeric()
                            ->minValue(0.01),
                        TextInput::make('area_square_meters')
                            ->label('Luas (m2)')
                            ->placeholder('Contoh: 26.00')
                            ->helperText('Isi luas total lahan dalam meter persegi.')
                            ->required()
                            ->numeric()
                            ->minValue(0.01),
                    ])
                    ->columnSpanFull()
                    ->columns([
                        'default' => 1,
                        'md' => 3,
                    ])
                    ->grow()
                    ->maxWidth(Width::Full),
                Section::make('Harga dasar')
                    ->description('Harga ini menjadi acuan awal sebelum aturan transaksi final diterapkan.')
                    ->schema([
                        TextInput::make('base_price_monthly')
                            ->label('Harga bulanan')
                            ->placeholder('Contoh: 15000000')
                            ->helperText('Masukkan harga dasar per bulan tanpa titik atau simbol rupiah. Boleh dikosongkan jika harga tahunan diisi.')
                            ->requiredWithout('base_price_yearly')
                            ->validationMessages([
                                'required_without' => 'Minimal salah satu harga sewa harus diisi: bulanan atau tahunan.',
                            ])
                            ->nullable()
                            ->numeric()
                            ->minValue(0)
                            ->prefix('Rp'),
                        TextInput::make('base_price_yearly')
                            ->label('Harga tahunan')
                            ->placeholder('Contoh: 150000000')
                            ->helperText('Masukkan harga dasar per tahun tanpa titik atau simbol rupiah. Boleh dikosongkan jika harga bulanan diisi.')
                            ->requiredWithout('base_price_monthly')
                            ->validationMessages([
                                'required_without' => 'Minimal salah satu harga sewa harus diisi: bulanan atau tahunan.',
                            ])
                            ->nullable()
                            ->numeric()
                            ->minValue(0)
                            ->prefix('Rp'),
                    ])
                    ->columnSpanFull()
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ])
                    ->grow()
                    ->maxWidth(Width::Full),
                Section::make('Galeri foto')
                    ->description('Unggah beberapa foto lahan, lalu tentukan foto utama dan urutan tampilnya.')
                    ->schema([
                        Repeater::make('images')
                            ->relationship('images')
                            ->label('Foto lahan')
                            ->helperText('Tambahkan satu atau lebih foto agar lahan lebih mudah dikenali.')
                            ->addActionLabel('Tambah foto lahan')
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->orderColumn('sort_order')
                            ->itemLabel(fn (array $state): string => filled($state['sort_order'] ?? null) ? 'Foto urutan '.$state['sort_order'] : 'Foto baru')
                            ->deleteAction(fn (Action $action) => $action->requiresConfirmation())
                            ->schema([
                                FileUpload::make('image_path')
                                    ->label('File gambar')
                                    ->helperText('Unggah gambar lahan dengan tampilan yang jelas.')
                                    ->image()
                                    ->disk('public')
                                    ->directory('plots')
                                    ->visibility('public')
                                    ->required()
                                    ->imageEditor()
                                    ->maxSize(4096),
                                Toggle::make('is_primary')
                                    ->label('Jadikan gambar utama')
                                    ->helperText('Aktifkan untuk foto yang ingin dijadikan gambar utama lahan.')
                                    ->default(false),
                                TextInput::make('sort_order')
                                    ->label('Urutan tampil')
                                    ->helperText('Angka lebih kecil akan ditampilkan lebih dulu.')
                                    ->numeric()
                                    ->default(0)
                                    ->required(),
                            ])
                            ->columns([
                                'default' => 1,
                                'md' => 3,
                            ]),
                    ])
                    ->columnSpanFull()
                    ->grow()
                    ->maxWidth(Width::Full),
            ]);
    }
}
