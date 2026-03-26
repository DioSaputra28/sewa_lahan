<?php

namespace App\Filament\Pages;

use App\Rules\GoogleMapsEmbedRule;
use App\Settings\SiteSetting;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageSiteSetting extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string $settings = SiteSetting::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Tabs::make('Site Settings')
                    ->persistTabInQueryString('site-settings-tab')
                    ->tabs([
                        Tab::make('Umum')
                            ->icon(Heroicon::OutlinedGlobeAlt)
                            ->schema([
                                Section::make('Identitas situs')
                                    ->description('Atur identitas utama brand yang akan dipakai di berbagai halaman.')
                                    ->schema([
                                        TextInput::make('site_name')
                                            ->label('Site name')
                                            ->required()
                                            ->minLength(2)
                                            ->maxLength(255)
                                            ->placeholder('Contoh: Pasar Space')
                                            ->helperText('Nama utama website yang tampil di browser title, navbar, dan identitas brand.')
                                            ->validationMessages([
                                                'required' => 'Site name wajib diisi.',
                                                'min' => 'Site name minimal 2 karakter.',
                                            ]),
                                        FileUpload::make('site_logo')
                                            ->label('Site logo')
                                            ->image()
                                            ->imageEditor()
                                            ->maxSize(2048)
                                            ->disk('public')
                                            ->directory('site-settings')
                                            ->visibility('public')
                                            ->helperText('Logo utama website. Rekomendasi PNG/SVG rasio horizontal, maksimal 2MB.'),
                                        FileUpload::make('favicon')
                                            ->label('Favicon')
                                            ->image()
                                            ->imageEditor()
                                            ->maxSize(1024)
                                            ->disk('public')
                                            ->directory('site-settings')
                                            ->visibility('public')
                                            ->helperText('Icon kecil browser tab. Rekomendasi PNG/ICO ukuran persegi, maksimal 1MB.'),
                                    ])
                                    ->columns([
                                        'default' => 1,
                                        'md' => 2,
                                    ]),
                            ]),
                        Tab::make('Sosial Media')
                            ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                            ->schema([
                                Section::make('Link akun sosial media')
                                    ->description('Gunakan URL lengkap agar bisa langsung dipakai di frontend.')
                                    ->schema([
                                        TextInput::make('youtube_url')
                                            ->label('YouTube URL')
                                            ->url()
                                            ->maxLength(255)
                                            ->placeholder('https://youtube.com/@username')
                                            ->helperText('Isi URL profil/channel YouTube lengkap (termasuk https://).'),
                                        TextInput::make('instagram_url')
                                            ->label('Instagram URL')
                                            ->url()
                                            ->maxLength(255)
                                            ->placeholder('https://instagram.com/username')
                                            ->helperText('Isi URL profil Instagram lengkap.'),
                                        TextInput::make('tiktok_url')
                                            ->label('TikTok URL')
                                            ->url()
                                            ->maxLength(255)
                                            ->placeholder('https://tiktok.com/@username')
                                            ->helperText('Isi URL profil TikTok lengkap.'),
                                        TextInput::make('facebook_url')
                                            ->label('Facebook URL')
                                            ->url()
                                            ->maxLength(255)
                                            ->placeholder('https://facebook.com/username')
                                            ->helperText('Isi URL halaman/profil Facebook lengkap.'),
                                        TextInput::make('twitter_x_url')
                                            ->label('Twitter / X URL')
                                            ->url()
                                            ->maxLength(255)
                                            ->placeholder('https://x.com/username')
                                            ->helperText('Isi URL akun X/Twitter lengkap.'),
                                        TextInput::make('threads_url')
                                            ->label('Threads URL')
                                            ->url()
                                            ->maxLength(255)
                                            ->placeholder('https://threads.net/@username')
                                            ->helperText('Isi URL akun Threads lengkap.'),
                                    ])
                                    ->columns([
                                        'default' => 1,
                                        'md' => 2,
                                    ]),
                            ]),
                        Tab::make('Landing Page')
                            ->icon(Heroicon::OutlinedHomeModern)
                            ->schema([
                                Section::make('Hero section')
                                    ->schema([
                                        FileUpload::make('landing_hero_image')
                                            ->label('Hero image')
                                            ->image()
                                            ->imageEditor()
                                            ->maxSize(3072)
                                            ->disk('public')
                                            ->directory('site-settings')
                                            ->visibility('public')
                                            ->helperText('Gambar utama di hero landing page. Rekomendasi landscape, maksimal 3MB.'),
                                        TextInput::make('landing_hero_image_alt')
                                            ->label('Hero image alt text')
                                            ->maxLength(255)
                                            ->placeholder('Contoh: Suasana pasar modern dengan lapak tertata')
                                            ->helperText('Teks alternatif untuk aksesibilitas dan SEO gambar hero.'),
                                    ])
                                    ->columns([
                                        'default' => 1,
                                        'md' => 2,
                                    ]),
                                Section::make('Peta interaktif')
                                    ->description('Isi URL embed Google Maps untuk section peta di landing page.')
                                    ->schema([
                                        Textarea::make('landing_map_embed_url')
                                            ->label('Landing map embed URL')
                                            ->rows(4)
                                            ->maxLength(5000)
                                            ->placeholder("https://www.google.com/maps/embed?...\natau\n<iframe src=\"https://www.google.com/maps/embed?...\" ...></iframe>")
                                            ->helperText('Boleh isi URL embed Google Maps langsung atau full tag iframe embed.')
                                            ->rule(new GoogleMapsEmbedRule)
                                            ->validationMessages([
                                                'max' => 'Panjang embed maksimal 5000 karakter.',
                                            ]),
                                    ]),
                            ]),
                        Tab::make('About Page')
                            ->icon(Heroicon::OutlinedInformationCircle)
                            ->schema([
                                Section::make('Hero section')
                                    ->schema([
                                        FileUpload::make('about_hero_image')
                                            ->label('About hero image')
                                            ->image()
                                            ->imageEditor()
                                            ->maxSize(3072)
                                            ->disk('public')
                                            ->directory('site-settings')
                                            ->visibility('public')
                                            ->helperText('Gambar hero untuk halaman about. Rekomendasi landscape, maksimal 3MB.'),
                                        TextInput::make('about_hero_image_alt')
                                            ->label('About hero image alt text')
                                            ->maxLength(255)
                                            ->placeholder('Contoh: Area pasar modern yang ramai')
                                            ->helperText('Teks alternatif untuk gambar hero halaman about.'),
                                    ])
                                    ->columns([
                                        'default' => 1,
                                        'md' => 2,
                                    ]),
                                Section::make('Genesis section')
                                    ->description('Konten panjang untuk section "Genesis Pasar Modern".')
                                    ->schema([
                                        Textarea::make('about_genesis_content')
                                            ->label('Genesis content')
                                            ->rows(8)
                                            ->minLength(30)
                                            ->maxLength(5000)
                                            ->placeholder('Isi narasi panjang untuk section Genesis Pasar Modern...')
                                            ->helperText('Konten narasi utama pada section Genesis. Minimal 30 karakter.')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                        Tab::make('Kontak')
                            ->icon(Heroicon::OutlinedMapPin)
                            ->schema([
                                Section::make('Informasi kantor')
                                    ->schema([
                                        TextInput::make('office_email')
                                            ->label('Email')
                                            ->email()
                                            ->maxLength(255)
                                            ->placeholder('hello@domain.com')
                                            ->helperText('Email utama yang ditampilkan pada halaman kontak.'),
                                        TextInput::make('office_whatsapp')
                                            ->label('WhatsApp')
                                            ->tel()
                                            ->regex('/^\+?[0-9]{8,15}$/')
                                            ->maxLength(50)
                                            ->placeholder('+6281234567890')
                                            ->helperText('Nomor WhatsApp aktif. Format yang disarankan: +628xxxxxxxxxx.')
                                            ->validationMessages([
                                                'regex' => 'Format nomor WhatsApp tidak valid. Gunakan angka dengan optional + di depan.',
                                            ]),
                                        TextInput::make('office_phone')
                                            ->label('No telepon')
                                            ->tel()
                                            ->regex('/^\+?[0-9]{8,15}$/')
                                            ->maxLength(50)
                                            ->placeholder('+622155501234')
                                            ->helperText('Nomor telepon kantor. Gunakan format angka internasional jika memungkinkan.')
                                            ->validationMessages([
                                                'regex' => 'Format nomor telepon tidak valid. Gunakan angka dengan optional + di depan.',
                                            ]),
                                        Textarea::make('office_location')
                                            ->label('Lokasi kantor')
                                            ->rows(3)
                                            ->maxLength(1000)
                                            ->placeholder("Contoh:\nJl. Contoh No. 123, Jakarta Selatan\nDKI Jakarta 12345")
                                            ->helperText('Alamat kantor lengkap yang akan ditampilkan ke user.'),
                                        Textarea::make('office_map_embed_url')
                                            ->label('Office map embed URL')
                                            ->rows(4)
                                            ->maxLength(5000)
                                            ->placeholder("https://www.google.com/maps/embed?...\natau\n<iframe src=\"https://www.google.com/maps/embed?...\" ...></iframe>")
                                            ->helperText('Boleh isi URL embed Google Maps langsung atau full tag iframe embed.')
                                            ->rule(new GoogleMapsEmbedRule)
                                            ->validationMessages([
                                                'max' => 'Panjang embed maksimal 5000 karakter.',
                                            ])
                                            ->columnSpanFull(),
                                    ])
                                    ->columns([
                                        'default' => 1,
                                        'md' => 2,
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
